<div>
    {!! Form::select('role_id', $roles, $user->role_id, ['class' => 'form-control role-change', 'id' => $user->id]) !!}
</div>