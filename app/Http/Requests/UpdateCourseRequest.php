<?php

namespace App\Http\Requests;

use App\Models\Course;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class UpdateCourseRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('course_edit');
    }

    public function rules()
    {
        return [
            'teachers.*' => [
                'integer',
            ],
            'teachers'   => [
                'array',
            ],
            'title'      => [
                'string',
                'required',
            ],
            'slug'       => [
                'string',
                'nullable',
            ],
            'start_date' => [
                'date_format:' . config('panel.date_format'),
                'nullable',
            ],
        ];
    }
}
