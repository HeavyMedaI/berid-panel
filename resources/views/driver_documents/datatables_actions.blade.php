<div class='btn-group btn-group-sm'>
    @can('driver_documents.show')
        <a data-toggle="tooltip" data-placement="left" title="{{trans('lang.view_details')}}" href="{{ route('driver_documents.show', $id) }}" class='btn btn-link'>
            <i class="fas fa-eye"></i> </a>
    @endcan

    @can('driver_documents.edit')
        <a data-toggle="tooltip" data-placement="left" title="{{trans('lang.driver_documents_edit')}}" href="{{ route('driver_documents.edit', $id) }}" class='btn btn-link'>
            <i class="fas fa-edit"></i> </a>
    @endcan

    @can('driver_documents.destroy')
        {!! Form::open(['route' => ['driver_documents.destroy', $id], 'method' => 'delete']) !!}
        {!! Form::button('<i class="fas fa-trash"></i>', [
        'type' => 'submit',
        'class' => 'btn btn-link text-danger',
        'onclick' => "return confirm('Are you sure?')"
        ]) !!}
        {!! Form::close() !!}
    @endcan
</div>
