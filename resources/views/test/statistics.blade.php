@extends ('layouts.base')

@section ('header')
    {!! View::make('partials.test-header', array('test_name' => $test->name))  !!}
@stop

@section ('content')

    <h2><i class="fa fa-pie-chart" aria-hidden="true"></i> Statistiques</h2>
    <p class="under-title">Sur l'ensemble du test</p>

    <p>
        <span class="label label-default">
            <b id="subject-count">{{ $subjects_count }}</b> / {{ $all_subjects_count_test }}  Sujets
        </span>
    </p>

    <div class="panel panel-default">
        <div class="panel-heading">Affichage</div>
        <div class="panel-body">

            <p><b>Sujets affichés :</b> {{ $subjects_count }}</p>

            <p><b>Total du test :</b>  {{ $all_subjects_count_test }}</p>

            <hr/>
            {{ Form::open(array('action' => array('TestController@statistics', $test->id), 'method' => 'get')) }}

                <div class="input-group">
                    <span>
                         {{ Form::checkbox('eliminated', true, $elminated_showed) }}
                    </span>
                    <label>Sujets éliminés</label>
                </div>

                <br/>
                {{ Form::submit('Actualiser', array('class'=>'btn btn-primary pull-right')) }}
               
            {{ Form::close() }}
        </div>
    </div>

    <h3>Conditions</h3>
    <p>Corpus sur l'ensemble des testeurs, éliminés des résultats ou non</p>

     <div class="row">

        <div class="col-md-4">
            <div class="panel panel-primary">
                <!-- Default panel contents -->
                <div class="panel-heading">Eliminé ?</div>

                    <table class="table">
                        @foreach($eliminate_counts as $name => $count)
                            <tr class="eliminate-row">
                                <td class="eliminate-row-name">{{ $name }}</td>
                                <td class="eliminate-row-count">{{ $count }}</td>
                            </tr>
                        @endforeach
                    </table>
                    <hr/>
                    <div class="panel-content">
                        <div id="eliminate-chart"></div>
                    </div>
            </div>
        </div>


        <div class="col-md-4">
            <div class="panel panel-primary">
                <!-- Default panel contents -->
                <div class="panel-heading">Lumière</div>

                    <table class="table">
                        @foreach($light_counts as $name => $count)
                            <tr class="light-row">
                                <td class="light-row-name">{{ $name }}</td>
                                <td class="light-row-count">{{ $count }}</td>
                            </tr>
                        @endforeach
                    </table>
                    <hr/>
                    <div class="panel-content">
                        <div id="light-chart"></div>
                    </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="panel panel-primary">
                <!-- Default panel contents -->
                <div class="panel-heading">Ambiance</div>

                    <table class="table">
                        @foreach($ambiance_counts as $name => $count)
                            <tr class="ambiance-row">
                                <td class="ambiance-row-name">{{ $name }}</td>
                                <td class="ambiance-row-count">{{ $count }}</td>
                            </tr>
                        @endforeach
                    </table>
                    <hr/>
                    <div class="panel-content">
                        <div id="ambiance-chart"></div>
                    </div>
            </div>
        </div>

        
    </div>

    <hr/>
    <h3>Sujets</h3>


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
    <script src="{{ URL::asset('js/pages/statistics/statistics-test.page.js') }}"></script>
@stop
