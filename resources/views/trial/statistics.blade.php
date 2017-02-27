@extends ('layouts.base')

@section ('header')
    {!! View::make('partials.test-header', array('test_name' => $test->name))  !!}
@stop

@section ('content')

    {!! View::make('partials.visualization.tabs', array('actual_page' => 'Statistiques', 'test' => $test, 'trial' => $trial))  !!}

    <h2><i class="fa fa-pie-chart" aria-hidden="true"></i> Statistiques sur la page</h2>
    <p class="under-title">{{$trial->name}}</p>

    

    <h3>Sujets</h3>
    <p class="subject-infos">
        <span class="label label-primary">
            <b id="subject-count">{{ $subjects_count }}</b> / {{ $all_subjects_count_test }}  sujets
        </span>
    </p>


    {!! View::make('partials.visualization.statistics-piecharts',
            array(
                'generation_counts' => $generation_counts, 
                'langage_counts' => $langage_counts, 
                'type_counts' => $type_counts, 
                'glasses_counts' => $glasses_counts, 
                'handedness_counts' => $handedness_counts, 
                'sex_counts' => $sex_counts,
            )
    ) !!}


@stop

@section ('scripts')
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script src="{{ URL::asset('js/pages/statistics/statistics.tools.js') }}"></script>
    <script src="{{ URL::asset('js/pages/statistics/statistics-trial.page.js') }}"></script>
@stop
