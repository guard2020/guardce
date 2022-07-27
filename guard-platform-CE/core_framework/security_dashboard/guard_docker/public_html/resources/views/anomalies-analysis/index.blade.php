@extends('layouts.app')

@section("page-header")
    @include('layouts.page-header', [
        'pageHeadTitle' => 'Anomalies Analysis',
        'breadcrumbs' => [
            [
                'name' => 'Anomalies Analysis',
                'link' => '',
                'icon' => 'fas fa-desktop'
            ]
        ]
    ])
@endsection


@section("content")
    <div class="row">
        <div class="col-lg-12 w-100">
{{--            <div class="card card-body w-100 p-2">--}}
{{--                <iframe src="{!! config('constants.kibana_url') !!}/app/dashboards#/view/f42b1d90-887c-11ea-97f0-1130b1a7a73f?embed=true&_g=(filters%3A!()%2CrefreshInterval%3A(pause%3A!t%2Cvalue%3A0)%2Ctime%3A(from%3Anow-15m%2Cto%3Anow))&show-time-filter=true" scrolling="no" class="w-100"></iframe>--}}
{{--            </div>--}}

            <fieldset class="card card-body w-100 p-2">
                <ul id="tabs" class="nav nav-tabs custom-tab" role="tablist">
                    <li class="nav-item">
                        <a id="tabAminer" href="#aminerKibana" class="nav-link active" data-toggle="tab" role="tab">AMiner Anomalies Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a id="tabCti" href="#ctiKibana" class="nav-link" data-toggle="tab" role="tab">AMiner CTI Dashboard</a>
                    </li>
                </ul>
                <div id="content" class="tab-content" role="tablist">
                    <div id="aminerKibana" class="tab-pane fade show active " role="tabpanel" aria-labelledby="tabAminer">
                        <div>
                            @if(getenv("KUBERNETES_SERVICE_HOST") !== false)
                                <iframe src=https://kibana-guardce.apps.ocp4.italtel.com/goto/f8bac5b1b90e5986f836a7d02e52278b scrolling="no" class="w-100"></iframe>
                            @else
                                <iframe src="{!! config('constants.kibana_url') !!}/goto/c67e27b8f4cff6413fe5edab22984101" scrolling="no" class="w-100"></iframe>
                            @endif
                        </div>
                    </div>
                    <div id="ctiKibana" class="tab-pane fade" role="tabpanel" aria-labelledby="tabCti">
                        <div style="overflow: hidden;">
                            <iframe scrolling="no" src="{!! config('constants.kibana_url') !!}/app/aminer" class="w-100" style="margin-top: -100px;">
                            </iframe>
                        </div>
                    </div>
                </div>
            </fieldset>


        </div>
    </div>

@endsection
@section("stylesheets")
    <style>

        iframe {
            height: 1800px;
            overflow: hidden;
            border: none;
        }

        .card{
            background-color: #fafbfd;
        }
    </style>
@endsection