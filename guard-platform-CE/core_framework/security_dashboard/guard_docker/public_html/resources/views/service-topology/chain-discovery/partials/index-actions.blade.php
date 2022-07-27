@php
    if(empty($userPermission) && $userPermission !== true){
        $dataToggle = 'tooltip';
        $title = 'User does not have permission!';
        $classInactive = 'inactiveLink';
        $disabled = 'disabled';
    }
@endphp

<div class="index-actions" data-toggle="{{ isset($dataToggle)? $dataToggle : null }}" title="{{ isset($title) ? $title : null }}">
    <form method="POST" action="{{URL::to('/')}}/servicetopology/chain/delete/{{ $chain['hostname'] }}" accept-charset="UTF-8" style="display:inline;"><input
                name="_method" value="DELETE" type="hidden"><input name="_token" value="{{ csrf_token() }}" type="hidden">
        <button {{ isset($disabled) ? $disabled : null }}  class="btn btn-xs btn-danger btn-padding-sm chain-del-btn {{ isset($classInactive) ? $classInactive : null }}" title="Delete Chain {!! $chain['hostname'] !!}"><i class="fa fa-trash" aria-hidden="true"></i>
        </button>
    </form>
</div>