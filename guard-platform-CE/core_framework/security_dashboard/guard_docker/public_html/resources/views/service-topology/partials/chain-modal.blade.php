<div id="modal_chain" class="modal" tabindex="-1" aria-modal="true" role="dialog">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            {!! Form::open(['route'=>'service-topology.chain.discover']) !!}
            <div class="modal-header bg-transparent header-elements-inline">
                <h5 class="modal-title">Discover New Chain</h5>
                <button type="button" class="close" data-dismiss="modal">×</button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-12">
{{--                        <div class="form-group ">--}}
{{--                            <label class="mr-3 mb-0 ">Hostname</label>--}}
{{--                            <div>--}}
{{--                                <select class="hostname-select" name="hostname" id="hostname">--}}
{{--                                    @foreach($environments as $env)--}}
{{--                                        <option value="{!! $env['hostname'] !!}">{!! $env['hostname'] !!}</option>--}}
{{--                                    @endforeach--}}
{{--                                </select>--}}
{{--                            </div>--}}
{{--                        </div>--}}
                        <div class="form-group form-group-float">
                            <label class="mr-3 mb-0 ">Hostname</label>
                            <input type="text" class="form-control" id="hostname" name="hostname"
                                   data-toggle="tooltip" title="Begin typing to view auto suggestion of existing execution environments"
                            >
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="mr-3 mb-0 ">Port</label>
                            <div>
                                {!! Form::number('port', '4000', [
                                    'class' => 'form-control'
                                    ]) !!}
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 align-self-center">
                        <div class="form-group my-auto ml-xl-3">
                            <label class="mr-3 mb-0" for="https">Https</label>
                            {!! Form::checkbox('https', true, false) !!}
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-link" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary btn-padding-sm legitRipple">Discover Chain</button>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>