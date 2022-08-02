@if(isset($env['agents']))
    <ul class="list-inline mb-0">
        <li class="dropdown">
            <a href="#" class="dropdown-toggle text-default selected-agent" data-toggle="dropdown" aria-expanded="false">-</a>
            <ul class="dropdown-menu agents-dropdown" x-placement="bottom-start" style="position: absolute; will-change: transform; top: 0; left: 0; transform: translate3d(0px, 17px, 0px);">
                @foreach($env['agents'] as $agent)
                    <a href="#" id="{!! $agent['id']!!}" data-status="{!! isset($agent['status']) ? $agent['status'] : $agent['id'] !!}" class="dropdown-item agent-item">{!! $agent['id']!!}</a>
                @endforeach
            </ul>
        </li>
    </ul>

@else
    -
@endif
