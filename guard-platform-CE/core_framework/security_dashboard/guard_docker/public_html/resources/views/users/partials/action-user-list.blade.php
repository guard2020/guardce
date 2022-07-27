@php
    if(empty($userPermission) && $userPermission !== true){
        $title = 'User does not have permission!';
        $classInactive = 'inactiveLink';
        $disabled = 'disabled';
    }
@endphp
<div class="index-actions"  title="{{ isset($title) ? $title : null }}">
    <form method="POST" action="{{URL::to('/')}}/users/{{ $user->id }}" accept-charset="UTF-8" style="display:inline;"><input
                name="_method" value="DELETE" type="hidden"><input name="_token" value="{{ csrf_token() }}" type="hidden">
        <button {{ isset($disabled) ? $disabled : null }}  class="btn btn-xs btn-danger btn-padding-sm user-del-btn {{ isset($classInactive) ? $classInactive : null }}" title="Delete Pipeline"><i class="fa fa-trash" aria-hidden="true"></i>
        </button>
    </form>
</div>