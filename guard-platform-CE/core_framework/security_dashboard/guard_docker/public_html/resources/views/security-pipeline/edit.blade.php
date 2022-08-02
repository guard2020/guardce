@extends('layouts.app')

@section('page-header')
    @include('layouts.page-header', [
        'pageHeadTitle' => 'Edit Security Pipeline',
        'breadcrumbs' => [
            [
                'name' => 'Pipeline',
                'link' => route('security-pipeline.index'),
                'icon' => 'fas fa-stream'
            ],
            [
                'name' => 'Edit',
                'link' => ''
            ]
        ]
    ])
@endsection
@section('content')
    <div class="row ">
        <div class="col-xl-10 col-12 offset-xl-1">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        {!! Form::model($pipeline, ['url'=>route('security-pipeline.update',$pipeline['id']), 'id' => 'smart-dashboard-form', 'enctype' => "multipart/form-data"]) !!}
                            @include('security-pipeline.partials.create-edit-form')
                            <div class="p-3">
                                <div class="row mt-4 text-right">
                                    <div class="col-sm-12">
                                        <div class="form-group ">
                                            <a href="{{URL::to('/')}}/security-pipeline" class="btn btn-secondary btn-padding-sm legitRipple">Cancel</a>
                                            <button type="submit" class="btn btn-primary btn-padding-sm legitRipple">Update</button>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        {!! Form::close() !!}

                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection

@section('scripts')
    @include('security-pipeline.partials.js.create-edit-scripts')
@endsection

@section('stylesheets')
    @include('security-pipeline.partials.css.create-edit-style')
@endsection
