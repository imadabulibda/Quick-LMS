<div class="m-3">
    @can('questions_option_create')
        <div style="margin-bottom: 10px;" class="row">
            <div class="col-lg-12">
                <a class="btn btn-success" href="{{ route('admin.questions-options.create') }}">
                    {{ trans('global.add') }} {{ trans('cruds.questionsOption.title_singular') }}
                </a>
            </div>
        </div>
    @endcan
    <div class="card">
        <div class="card-header">
            {{ trans('cruds.questionsOption.title_singular') }} {{ trans('global.list') }}
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class=" table table-bordered table-striped table-hover datatable datatable-questionQuestionsOptions">
                    <thead>
                        <tr>
                            <th width="10">

                            </th>
                            <th>
                                {{ trans('cruds.questionsOption.fields.id') }}
                            </th>
                            <th>
                                {{ trans('cruds.questionsOption.fields.question') }}
                            </th>
                            <th>
                                {{ trans('cruds.questionsOption.fields.option_text') }}
                            </th>
                            <th>
                                {{ trans('cruds.questionsOption.fields.is_correct') }}
                            </th>
                            <th>
                                &nbsp;
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($questionsOptions as $key => $questionsOption)
                            <tr data-entry-id="{{ $questionsOption->id }}">
                                <td>

                                </td>
                                <td>
                                    {{ $questionsOption->id ?? '' }}
                                </td>
                                <td>
                                    {{ $questionsOption->question->question ?? '' }}
                                </td>
                                <td>
                                    {{ $questionsOption->option_text ?? '' }}
                                </td>
                                <td>
                                    <span style="display:none">{{ $questionsOption->is_correct ?? '' }}</span>
                                    <input type="checkbox" disabled="disabled" {{ $questionsOption->is_correct ? 'checked' : '' }}>
                                </td>
                                <td>
                                    @can('questions_option_show')
                                        <a class="btn btn-xs btn-primary" href="{{ route('admin.questions-options.show', $questionsOption->id) }}">
                                            {{ trans('global.view') }}
                                        </a>
                                    @endcan

                                    @can('questions_option_edit')
                                        <a class="btn btn-xs btn-info" href="{{ route('admin.questions-options.edit', $questionsOption->id) }}">
                                            {{ trans('global.edit') }}
                                        </a>
                                    @endcan

                                    @can('questions_option_delete')
                                        <form action="{{ route('admin.questions-options.destroy', $questionsOption->id) }}" method="POST" onsubmit="return confirm('{{ trans('global.areYouSure') }}');" style="display: inline-block;">
                                            <input type="hidden" name="_method" value="DELETE">
                                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                            <input type="submit" class="btn btn-xs btn-danger" value="{{ trans('global.delete') }}">
                                        </form>
                                    @endcan

                                </td>

                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@section('scripts')
@parent
<script>
    $(function () {
  let dtButtons = $.extend(true, [], $.fn.dataTable.defaults.buttons)
@can('questions_option_delete')
  let deleteButtonTrans = '{{ trans('global.datatables.delete') }}'
  let deleteButton = {
    text: deleteButtonTrans,
    url: "{{ route('admin.questions-options.massDestroy') }}",
    className: 'btn-danger',
    action: function (e, dt, node, config) {
      var ids = $.map(dt.rows({ selected: true }).nodes(), function (entry) {
          return $(entry).data('entry-id')
      });

      if (ids.length === 0) {
        alert('{{ trans('global.datatables.zero_selected') }}')

        return
      }

      if (confirm('{{ trans('global.areYouSure') }}')) {
        $.ajax({
          headers: {'x-csrf-token': _token},
          method: 'POST',
          url: config.url,
          data: { ids: ids, _method: 'DELETE' }})
          .done(function () { location.reload() })
      }
    }
  }
  dtButtons.push(deleteButton)
@endcan

  $.extend(true, $.fn.dataTable.defaults, {
    orderCellsTop: true,
    order: [[ 1, 'desc' ]],
    pageLength: 25,
  });
  let table = $('.datatable-questionQuestionsOptions:not(.ajaxTable)').DataTable({ buttons: dtButtons })
  $('a[data-toggle="tab"]').on('shown.bs.tab click', function(e){
      $($.fn.dataTable.tables(true)).DataTable()
          .columns.adjust();
  });
  
})

</script>
@endsection