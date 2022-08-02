{!! Form::model($user, ['url'=>route('users.update',$user['id']), 'id' => 'smart-dashboard-form']) !!}
<div class="form-group">
    <div class="row">
        <div class="col-lg-10 offset-xl-1">
            <label>Username/Email</label>
            {!! Form::email('email', null, [
                'class' => 'form-control',
                'required'                      => 'required',
                'data-parsley-trigger'          => 'change focusout',
                'placeholder' => 'Email',
                ])
            !!}
            <span class="text-danger">{{ $errors->first('email') }}</span>

        </div>
    </div>
</div>
<div class="form-group">
    <div class="row">
        <div class="col-lg-10 offset-xl-1">
            <label>Name</label>
            {!! Form::text('name', null, [
                'class' => 'form-control',
                'required'                      => 'required',
                'data-parsley-trigger'          => 'change focusout',
                'placeholder' => 'Name',
                ])
            !!}
            <span class="text-danger">{{ $errors->first('name') }}</span>
        </div>
    </div>
</div>
<div class="form-group">
    <div class="row">
        <div class="col-lg-10 offset-xl-1">
            <label>Role</label>
            {!! Form::text('role_id', ($user->role_id === 1) ? "Administrator" : "User", [
                'class' => 'form-control',
                'style' => 'background-color: #fafafa',
                'disabled' => true,
                'required'                      => 'required',
                'data-parsley-trigger'          => 'change focusout',
                'placeholder' => 'Role',
                ])
            !!}
            <span class="text-danger">{{ $errors->first('role_id') }}</span>
        </div>
    </div>
</div>

<div class="text-right col-lg-10 offset-xl-1">
    <button type="submit" class="btn btn-primary">Save changes</button>
</div>
{!! Form::close() !!}
