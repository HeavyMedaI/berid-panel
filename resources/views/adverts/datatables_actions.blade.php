<div class='btn-group btn-group-sm'>
    @can('adverts.show')
        <a data-toggle="tooltip" data-placement="left" title="{{trans('lang.view_details')}}" href="{{ route('adverts.show', $id) }}" class='btn btn-link'>
            <i class="fas fa-eye"></i> </a> @endcan

    {{--@can('adverts.edit')
        <a data-toggle="tooltip" data-placement="left" title="{{trans('lang.advert_edit')}}" href="{{ route('adverts.edit', $id) }}" class='btn btn-link'>
            <i class="fas fa-edit"></i> </a> @endcan--}}

    @can('adverts.destroy') {!! Form::open(['route' => ['adverts.destroy', $id], 'method' => 'delete']) !!} {!! Form::button('<i class="fas fa-trash"></i>', [ 'type' => 'submit', 'class' => 'btn btn-link text-danger', 'onclick' => "return confirm('Are you sure?')" ]) !!} {!! Form::close() !!} @endcan
</div>
