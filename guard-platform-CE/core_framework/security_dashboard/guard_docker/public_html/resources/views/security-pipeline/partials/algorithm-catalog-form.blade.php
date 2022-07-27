<fieldset class="card-body">
    <div class="row py-3">
        <div class="col-12">
            <div>
                <h6 class="font-weight-bold">Available Algorithms</h6>
            </div>
            <div class="table-responsive algorithm-list-form">
                <table class="table table-sm table-bordered tableFixHead" id="algoPipelineFormTable" style="border-top:none;">
                    <thead>
                        <tr style="height: 42px;">
                            <th class="text-center">#</th>
                            <th class="text-center">ID</th>
                            <th colspan="5" class="text-center">Description</th>
                        </tr>
                    </thead>
                    <tbody>

                    @foreach($algorithms as $algoKey=>$algo)
                        <tr id="algorithm_{!! $algo['id'] !!}">
                            <td style="text-align: -webkit-center !important; width: 100px;">
                                <div class="align-content-center">
                                    @if(in_array($algo['id'], $activeAlgorithms) && ((isset($pipeline['algorithm_catalog_id']) && $algo['id'] !== $pipeline['algorithm_catalog_id']) || (!isset($pipeline['algorithm_catalog_id']))))
                                        <span data-toggle="tooltip" data-html="true" title="Algorithm already in use."><i class="fas fa-lock custom-lock"></i></span>
                                    @else
                                        <input type="radio" name="algorithm_id" value="{!! $algo['id'] !!}"
                                           class="form-check-input-styled" id="{!! $algo['id'] !!}"
                                           @if(isset($pipeline))
                                           {!! (isset($pipeline['algorithm_catalog_id']) && $pipeline['algorithm_catalog_id'] === $algo['id']) ? 'checked': "" !!}
                                           @else
                                           {!! ($algoKey === 0) ? "" : "" !!}
                                           @endif
                                           data-fouc>
                                    @endif
                                </div>
                            </td>
                            <td class="pl-3">{!! $algo['id'] !!}</td>
                            <td class="pl-3" colspan="5">
                                @if(isset($algo['description']))
                                    {!! $algo['description'] !!}
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</fieldset>
