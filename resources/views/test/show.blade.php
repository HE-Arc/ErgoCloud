@extends ('layouts.base')

@section ('header')
    {!! View::make('partials.test-header', array('test_name' => $test->name))  !!}
@stop


@section ('content')

    <h2><i class="fa fa-cogs" aria-hidden="true"></i> Configuration de l'analyse</h2>

    <div class="panel panel-default panel-content-hiding" >
        

        <div class="panel-body">

            <div class="panel-in-header">
                <p>Choix de la calibration (<b id="subject-count">{{ $subjects_count }}</b> / {{ $all_subjects_count_test }}  sujets)</p>
            </div>

            @if($sortedCalibs["counter"] != 0)
                <table class="table table-condensed">
                    <thead>
                        <tr>
                            <th>Qualité</th>
                            <th>Nombre de sujets</th>
                            <th>&nbsp;</th>
                            <th>&nbsp;</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><strong>Parfaite</strong></td>
                            <input type="hidden" id="perfect" value="{{$sortedCalibs["perfectDisable"]}}">
                            <td>{{$sortedCalibs["perfectCalibs"]->count()}}</td>
                            <td>{{$sortedCalibs["perfectPr"] }}%</td>
                            @if($sortedCalibs["perfectCalibs"]->count())
                                <td class="text-right"><a id="btnPerfect" class="btn btn-default"></a></td>
                            @endif
                        </tr>  
                        <tr>
                            <td>Bonne</td>
                            <input type="hidden" id="good" value="{{$sortedCalibs["goodDisable"]}}">
                            <td>{{$sortedCalibs["goodCalibs"]->count()}}</td>
                            <td>{{$sortedCalibs["goodPr"]}}%</td>
                            @if($sortedCalibs["goodCalibs"]->count())
                                <td class="text-right"><a id="btnGood"  class="btn btn-default"></a></td>
                            @endif
                        </tr>  
                        <tr>
                            <td>Moyenne</td>
                            <input type="hidden" id="moderate" value="{{$sortedCalibs["moderateDisable"]}}">
                            <td>{{$sortedCalibs["moderateCalibs"]->count()}}</td>
                            <td>{{$sortedCalibs["moderatePr"]}}% </td>
                            @if($sortedCalibs["moderateCalibs"]->count())
                                <td class="text-right"><a id="btnModerate"  class="btn btn-default"></a></td>
                            @endif
                        </tr>  
                        <tr>
                            <td>Mauvaise</td>
                            <input type="hidden" id="poor" value="{{$sortedCalibs["poorDisable"]}}">
                            <td>{{$sortedCalibs["poorCalibs"]->count()}}</td>
                            <td>{{$sortedCalibs["poorPr"]}}%</td>
                            @if($sortedCalibs["poorCalibs"]->count()!=0)
                                <td class="text-right"><a id="btnPoor" class="btn btn-default"></a></td>
                            @endif
                        </tr>                 
                    </tbody>
                </table>
            @else
                <div class="panel-body-box">
                    <p>Le test <strong>{{$test->name}}</strong> n'a aucune calibration enregistrée</p>
                    <p>Il a été créé avec une ancienne version d'Ogama.</p>
                </div>
            @endif

            <br/>


            <div class="panel-in-header">
                <p>Configuration du questionnaire post-test</p>
            </div>

            @if($test->testConfig)
                <table class="table table-stripped">
                    <tr>
                        <td class="column-header">URL des résultats</td>
                        <td class="word-wrap">{{ $test->testConfig->google_results_url }}</td>
                    </tr>
                    <tr>
                        <td class="column-header">Colone représentant le nom du sujet</td>
                        <td>{{ $test->testConfig->name_column }}</td>
                    </tr>
                    <tr>
                        <td class="column-header">Colone représentant l'évaluation du sujet</td>
                        <td>{{ $test->testConfig->evaluation_column }}</td>
                    </tr>
                    <tr>
                        <td class="column-header">Sheet</td>
                        <td>{{ $test->testConfig->sheet }}</td>
                    </tr>
                </table>

                <div class="panel-body-box">
                    <a href="{{ URL::route('test.{test_id}.testconfig.edit', array('test_id' => $test->id, 'testconfig' => $test->testConfig->id)) }}" class="btn btn-success btn-block">
                        <i class="fa fa-cog" aria-hidden="true"></i> Modifier
                    </a>
                </div>
            @else
                <div class="panel-body-box">
                    <p><b>Test non configuré, veuillez le configurer pour afficher les résultats des tests Google forms.</b></p>
                    <a href="{{ URL::route('test.{test_id}.testconfig.create', array('test_id' => $test->id)) }}" class="btn btn-success btn-block">
                        <i class="fa fa-cog" aria-hidden="true"></i> Créer une configuration
                    </a>
                </div>
            @endif
        </div>
        
    </div>


           
       

    <h2><i class="fa fa-pie-chart" aria-hidden="true"></i> Statistiques sur l'ensemble du test</h2>

    <span class="label label-primary pull-right">
        <b id="subject-count">{{ $subjects_count }}  Sujets
    </span>

    <h3>Données contextuelles</h3>

     <div class="row">

        <div class="col-md-6">
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

        <div class="col-md-6">
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
    <h3>Données inter-individuelles</h3>


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







        <a href="{{ URL::route('filter', array('test_id' => $test->id)) }}" class="btn btn-success pull-right btn-suite"><i class="fa fa-arrow-circle-o-right" aria-hidden="true"></i> Démarrer le filtrage</a>
    </div>





@stop

@section ('scripts')

    <!-- Statistics -->
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script src="{{ URL::asset('js/pages/statistics/statistics.tools.js') }}"></script>
    <script src="{{ URL::asset('js/pages/statistics/statistics-test.page.js') }}"></script>

    <!-- Calibration -->
    <div id="hidden-informations">
        <span id="test-infos-id">{{ $test->id }}</span>
        <span id="calibration-state-url">{{ url('/calibration/state') }}</span>
        <span id="calibration-test-url">{{ URL::route('show_test', array('test_id' => $test->id)) }}</span>
    </div>

    <script src="{{ URL::asset('js/pages/calibration/calibration.page.js') }}"></script>
@stop
