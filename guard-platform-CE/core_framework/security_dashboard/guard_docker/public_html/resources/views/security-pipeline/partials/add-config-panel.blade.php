<div id="row{!! $counter !!}">
    <div class="form-group row" >
        <label class="col-lg-3 col-form-label">Select Config</label>
        <div class="col-lg-7">
            {!! Form::select('resource_id[]', array_merge([''=>'Select Reseource'],$resourceOptions), null, ["class" => "form-control"/*, 'required' => 'required'*/]); !!}
        </div>
        <div class="col-lg-2">
            <button type="button" name="remove" id="{!! $counter !!}" class="btn btn-danger btn_remove_file">X</button>
        </div>
    </div>

    <div class="form-group row">
        <label class="col-lg-3 col-form-label">Config Upload</label>
        <div class="col-lg-7">
            <div class="file-upload-wrapper">
                <input type="file" name="config_file[]" id="input-file-max-fs" class="file-upload" data-max-file-size="2M" />
            </div>
        </div>
        <div class="col-lg-2">&nbsp;</div>

    </div>
</div>