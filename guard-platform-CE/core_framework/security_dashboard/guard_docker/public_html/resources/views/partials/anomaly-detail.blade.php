@php
    $json_string = json_encode($data, JSON_PRETTY_PRINT);
@endphp

<pre>
    {!! $json_string !!}
</pre>
