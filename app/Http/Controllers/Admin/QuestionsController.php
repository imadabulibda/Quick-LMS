<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\MediaUploadingTrait;
use App\Http\Requests\MassDestroyQuestionRequest;
use App\Http\Requests\StoreQuestionRequest;
use App\Http\Requests\UpdateQuestionRequest;
use App\Models\Question;
use App\Models\QuestionsOption;
use App\Models\Test;
use App\Models\TestResult;
use Gate;
use Illuminate\Http\Request;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Symfony\Component\HttpFoundation\Response;

class QuestionsController extends Controller {
    use MediaUploadingTrait;

    public function index()
    {
        abort_if(Gate::denies('question_access') , Response::HTTP_FORBIDDEN , '403 Forbidden');

        $questions = Question::with(['media'])->get();

        return view('admin.questions.index' , compact('questions'));
    }

    public function create()
    {
        abort_if(Gate::denies('question_create') , Response::HTTP_FORBIDDEN , '403 Forbidden');

        $tests = Test::all()->pluck('title', 'id');
        return view('admin.questions.create',compact('tests'));
    }

    public function store(StoreQuestionRequest $request)
    {
        $question = Question::create($request->all());
        $question->tests()->sync($request->input('tests', []));

        for ($q = 1; $q <= 4; $q++) {
            $option = $request->input('option_text_' . $q , '');
            if ($option != '') {
                QuestionsOption::create([
                    'question_id' => $question->id ,
                    'option_text' => $option ,
                    'is_correct'  => $request->input('is_correct_' . $q),
                ]);
            }
        }

        if ($request->input('question_image' , false)) {
            $question->addMedia(storage_path('tmp/uploads/' . $request->input('question_image')))->toMediaCollection('question_image');
        }

        if ($media = $request->input('ck-media' , false)) {
            Media::whereIn('id' , $media)->update(['model_id' => $question->id]);
        }

        return redirect()->route('admin.questions.index');
    }

    public function edit(Question $question)
    {
        abort_if(Gate::denies('question_edit') , Response::HTTP_FORBIDDEN , '403 Forbidden');
        $tests = Test::all()->pluck('title', 'id');

        return view('admin.questions.edit' , compact('question','tests'));
    }

    public function update(UpdateQuestionRequest $request , Question $question)
    {
        $question->update($request->all());

        if ($request->input('question_image' , false)) {
            if ( ! $question->question_image || $request->input('question_image') !== $question->question_image->file_name) {
                if ($question->question_image) {
                    $question->question_image->delete();
                }

                $question->addMedia(storage_path('tmp/uploads/' . $request->input('question_image')))->toMediaCollection('question_image');
            }
        } else if ($question->question_image) {
            $question->question_image->delete();
        }

        $question->tests()->sync($request->input('tests', []));

        return redirect()->route('admin.questions.index');
    }

    public function show(Question $question)
    {
        abort_if(Gate::denies('question_show') , Response::HTTP_FORBIDDEN , '403 Forbidden');

        $question->load('options');

        return view('admin.questions.show' , compact('question'));
    }

    public function destroy(Question $question)
    {
        abort_if(Gate::denies('question_delete') , Response::HTTP_FORBIDDEN , '403 Forbidden');

        $question->delete();

        return back();
    }

    public function massDestroy(MassDestroyQuestionRequest $request)
    {
        Question::whereIn('id' , request('ids'))->delete();

        return response(null , Response::HTTP_NO_CONTENT);
    }

    public function storeCKEditorImages(Request $request)
    {
        abort_if(Gate::denies('question_create') && Gate::denies('question_edit') , Response::HTTP_FORBIDDEN , '403 Forbidden');

        $model = new Question();
        $model->id = $request->input('crud_id' , 0);
        $model->exists = true;
        $media = $model->addMediaFromRequest('upload')->toMediaCollection('ck-media');

        return response()->json(['id' => $media->id , 'url' => $media->getUrl()] , Response::HTTP_CREATED);
    }
}
