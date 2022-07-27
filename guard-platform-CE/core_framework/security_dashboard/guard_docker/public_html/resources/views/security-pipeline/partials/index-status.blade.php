@php

    if($pipeline['status'] === "started"){
        $badge = 'badge-primary';
    }else if($pipeline['status'] === "stopped"){
        $badge = 'badge-danger';
    }else{
        $badge = 'badge-light';
    }

@endphp

<div>
    <span class="status badge {!! $badge !!}" id="{!! $pipeline['id'] !!}">{!! ucfirst(isset($pipeline['status']) ? $pipeline['status'] : "") !!}</span>
</div>