<div class="index-actions">
    {!! Form::select('status', $options, $user->status, ['class' => 'form-control status-change selectpicker', 'id' => $user->id]) !!}
</div>