@extends ('layouts.base')

@section ('header')
    {!! View::make('partials.test-header', array('test_name' => $test->name))  !!}
@stop

@section ('content')
    {!! View::make('partials.visualization.tabs', array('actual_page' => 'Scanpath', 'test' => $test, 'trial' => $trial))  !!}

    <h2><i class="fa fa-eye" aria-hidden="true"></i> Scanpath sur la page</h2>


    <div style="float: right; left: 0; width: 50%">
        Minimum duration :
        <input id="min-duration" type="range"  min="0" max="1500" value="400" />
        <span style="margin-left: 10px" id="min-duration-label"></span>
    </div>

    <p class="under-title">{{$trial->name}}</p>

    <div id="path-buttons" style="margin-left: 20px">
        <button type="button" class="btn btn-primary btn-sm" style="background-color: #16a085 ">Primary</button>
        <button type="button" class="btn btn-primary btn-sm" style="background-color: #f39c12 ">Primary</button>
        <button type="button" class="btn btn-primary btn-sm" style="background-color: #27ae60 ">Primary</button>
    </div>

    <div id="scanpath" style="position: relative"></div>

@stop

@section ('scripts')
    <script src="{{ URL::asset('js/heatmap.min.js') }}"></script>
    <script src="{{ URL::asset('js/ergoview.js') }}"></script>

    <script>
        var scanpath = new ErgoView({container: 'scanpath', scanpath: true, json:'{!! $json !!}'});

        $('input[type=range]').on('input', function () {
           $(this).trigger('change');
        });

        $('#min-duration').change(function () {
           $('#min-duration-label').html($('#min-duration').val() + ' ms');
            scanpath.changeMinDuration($('#min-duration').val());
        });

        $('#min-duration').trigger('change');
    </script>
@stop
