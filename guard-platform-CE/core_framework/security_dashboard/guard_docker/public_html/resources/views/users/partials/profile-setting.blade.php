{!! Form::model($user, ['url'=>route('users.updateSetting',$user['id']), 'id' => 'smart-dashboard-form-settings']) !!}
<div class="form-group">
    <div class="row">
        <div class="col-lg-10 offset-xl-1">
            <label>Username/Email</label>
            {!! Form::email('email', null, [
                'class' => 'form-control',
                'disabled' => true,
                'style' => 'background-color: #fafafa',
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
            <label>Current password</label>
            {!! Form::password('old_password', [
              'class' => 'form-control',
              'required'                      => 'required',
              'data-parsley-trigger'          => 'change focusout',
              'placeholder' => 'Current Password',
              ])
          !!}
            <span class="text-danger">{{ $errors->first('old_password') }}</span>
        </div>
    </div>
</div>

<div class="form-group">
    <div class="row">
        <div class="col-lg-10 offset-xl-1">
            <label>New password</label>
            {!! Form::password('new_password', [
               'class' => 'form-control',
               'id' => "new_password",
               'required'                      => 'required',
               'data-parsley-trigger'          => 'change focusout',
               'placeholder' => 'New Password',
               ])
           !!}
            <span class="text-danger">{{ $errors->first('new_password') }}</span>
        </div>
    </div>
</div>

<div class="form-group">
    <div class="row">
        <div class="col-lg-10 offset-xl-1">
            <label>Confirm password</label>
            {!! Form::password('confirm_password', [
              'class' => 'form-control',
              'id' => "confirm_password",
              'required'                      => 'required',
              'data-parsley-trigger'          => 'change focusout',
              'data-parsley-equalto' => "#new_password",
              'data-parsley-equalto-message' => "Confirm password should be same as new password",
              'placeholder' => 'Confirm Password',
              ])
          !!}
            <span class="text-danger">{{ $errors->first('confirm_password') }}</span>
        </div>
    </div>
</div>

<div class="text-right col-lg-10 offset-xl-1">
    <button type="submit" class="btn btn-primary">Save changes</button>
</div>
{!! Form::close() !!}