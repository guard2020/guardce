@include('flash-message')
<fieldset class="card-body">
    <input type="hidden" name="pipeline_id" id="pipelineId" value="{!! !empty($pipeline) ? $pipeline['id'] : null; !!}">
    <div class="form-group row col pt-2">
        <div class="mt-2" >
            <label class="mr-3 mb-0 ">
                <h6 class="font-weight-bold mb-0">Security Pipeline Name:<span style="color:red; padding-top: 5px;"> *</span></h6>
            </label>
        </div>
        <div style="width: 40em;">
            {!! Form::text('name', null, [
                'class' => 'form-control',
                'required'                      => 'required',
                'data-parsley-trigger'          => 'change focusout',
                'placeholder' => 'Pipeline name',
                ]) !!}
            <span class="text-danger">{{ $errors->first('name') }}</span>
        </div>
    </div>
    <div class="form-group row col pt-2">
        <div class="mt-2" >
            <label class="mr-3 mb-0 ">
                <h6 class="font-weight-bold mb-0 mr-1">Security Policy: </h6>
            </label>
        </div>
        <div style="width: 40em;" class="ml-lg-5">
            {!! Form::textarea('policy', null, [
                'class' => 'form-control',
                'rows' => 5,
                'placeholder' => 'Enter security policy',
                ]) !!}
            <span class="text-danger">{{ $errors->first('policy') }}</span>
        </div>
    </div>
</fieldset>
<fieldset class="card-body">
    <ul id="tabs" class="nav nav-tabs custom-tab" role="tablist">
        <li class="nav-item">
            <a id="tab-A" href="#pane-Agent" class="nav-link active" data-toggle="tab" role="tab">Agent Catalog</a>
        </li>
        <li class="nav-item">
            <a id="tab-B" href="#pane-Algorithm" class="nav-link" data-toggle="tab" role="tab">Algorithm Catalog</a>
        </li>
    </ul>
</fieldset>
<div id="content" class="tab-content" role="tablist">
    <div id="pane-Agent" class="tab-pane fade show active " role="tabpanel" aria-labelledby="tab-A">
        <div id="agentCatalog">
            @include('security-pipeline.partials.agent-catalog-form')
        </div>
    </div>
    <div id="pane-Algorithm" class="tab-pane fade" role="tabpanel" aria-labelledby="tab-B">
        <div id="algorithmCatalog">
            @include('security-pipeline.partials.algorithm-catalog-form')
        </div>
    </div>
</div>
