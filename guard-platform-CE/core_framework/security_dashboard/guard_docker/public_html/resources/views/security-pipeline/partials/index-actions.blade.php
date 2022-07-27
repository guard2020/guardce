@php
    if(empty($userPermission) && $userPermission !== true){
        $dataToggle = 'tooltip';
        $title = 'User does not have permission!';
        $classInactive = 'inactiveLink';
        $disabled = 'disabled';
    }
    $reloadInactive = "";
    if(!$reloadStatus){
        $reloadInactive = 'disabled';
    }
@endphp

<div class="index-actions" data-toggle="{{ isset($dataToggle)? $dataToggle : null }}" title="{{ isset($title) ? $title : null }}">
    <a {{ isset($disabled) ? $disabled : null }} href="{{URL::to('/')}}/security-pipeline/{{ $pipeline['id'] }}/edit" title="Edit Pipeline" class="btn btn-xs btn-info btn-padding-sm {{ isset($classInactive) ? $classInactive : null }}"><i class="fa fa-edit"></i></a>
    <form method="POST" action="{{URL::to('/')}}/security-pipeline/{{ $pipeline['id'] }}" accept-charset="UTF-8" style="display:inline;"><input
                name="_method" value="DELETE" type="hidden"><input name="_token" value="{{ csrf_token() }}" type="hidden">
        <button {{ isset($disabled) ? $disabled : null }}  class="btn btn-xs btn-danger btn-padding-sm pipeline-del-btn {{ isset($classInactive) ? $classInactive : null }}" title="Delete Pipeline"><i class="fa fa-trash" aria-hidden="true"></i>
        </button>
    </form>
</div>