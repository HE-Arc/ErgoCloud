@extends ('layouts.base')

@section ('header')
    {!! View::make('partials.test-header', array('test_name' => $test->name))  !!}
@stop

@section ('content')
    {!! View::make('partials.visualization.tabs', array('actual_page' => 'Heatmap', 'test' => $test, 'trial' => $trial))  !!}

    <h2><i class="fa fa-thermometer-half" aria-hidden="true"></i> Heatmap sur la page</h2>

    <p class="under-title">{{$trial->name}}</p>

    <div id="heatmap" style="position: relative"></div>


@stop

@section ('scripts')
    <script src="{{ URL::asset('js/heatmap.min.js') }}"></script>
    <script src="{{ URL::asset('js/ergoview.js') }}"></script>

    <script>
        var heatmap = new ErgoView({container: 'heatmap', heatmap: true, json:'{!! $json !!}'});
    </script>
@stop
