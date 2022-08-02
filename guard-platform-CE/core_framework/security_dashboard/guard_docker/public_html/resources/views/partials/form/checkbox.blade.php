<div class="ak_formLineWrap " >
    <div class="sychb-wrap ">
        @if(isset($isRadio) && $isRadio === true)
            {!! Form::radio($name, $value, isset($checked)?true:null, [
                'id' => $id,
            ]) !!}
        @else
            {!! Form::checkbox($name, $value, isset($checked)?true:null, [
                'id' => $id,
            ]) !!}
        @endif

        <label for="{!! $id !!}" class="sychb-label-1"></label>
    </div>
    {!! Form::customLabel($id, $label, []) !!}
</div>