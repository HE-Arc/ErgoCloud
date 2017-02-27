@extends ('layouts.base')

@section ('header')
    {!! View::make('partials.test-header', array('test_name' => $test->name))  !!}

    <style>
        /* The Modal (background) */
        .modal {
            display: none; /* Hidden by default */
            position: fixed; /* Stay in place */
            z-index: 1; /* Sit on top */
            padding-top: 150px; /* Location of the box */
            left: 0;
            top: 0;
            width: 100%; /* Full width */
            height: 100%; /* Full height */
            overflow: auto; /* Enable scroll if needed */
            background-color: rgb(0,0,0); /* Fallback color */
            background-color: rgba(0,0,0,0.4); /* Black w/ opacity */
        }

        /* Modal Content */
        .modal-content {
            position: relative;
            background-color: #fefefe;
            margin: auto;
            padding: 0;
            
            border: 1px solid #888;
            width: 500px;
            box-shadow: 0 4px 8px 0 rgba(0,0,0,0.2),0 6px 20px 0 rgba(0,0,0,0.19);
            -webkit-animation-name: animatetop;
            -webkit-animation-duration: 0.2s;
            animation-name: animatetop;
            animation-duration: 0.2s
        }

        #modal-title{
            font-size: 18px !important;
            margin-top: 12px;
            padding: 0;
        }

        /* Add Animation */
        @-webkit-keyframes animatetop {
            from {top:-300px; opacity:0}
            to {top:0; opacity:1}
        }

        @keyframes animatetop {
            from {top:-300px; opacity:0}
            to {top:0; opacity:1}
        }

        /* The Close Button */
        .close {
            color: white;
            float: right;
            font-size: 28px;
            font-weight: bold;
            padding-top: 10px;
        }

        .close:hover,
        .close:focus {
            color: #000;
            text-decoration: none;
            cursor: pointer;
        }

        .modal-header {
            padding: 2px 16px;
            background-color: #004378;
            color: white;
        }

        .modal-body {padding: 2px 16px;}

        .modal-body ul{
            padding-top: 20px;
            margin: 0 !important;
            padding: 10px;
        }

        .modal-body ul li{
            list-style-type: none;
            font-size: 18px;

            margin: 0 !important;
            padding: 0 !important;
        }

        .modal-footer {
            padding: 2px 16px;
            background-color: #004378;
            color: white;
        }

        .subject{
            padding-bottom: 5px;
        }
        .eval-subject{
            font-size: 12px;
            font-weight: 600;
        }
        .eval-result{
            font-weight: normal;
        }
    </style>
@stop


@section ('content')

    @if(!is_null($google_spreed_sheet_errors))
        <div class="alert alert-danger">
            <p><i class="fa fa-exclamation-triangle" aria-hidden="true"></i> {{ $google_spreed_sheet_errors }}</p>
        </div>
    @endif


    <h2>Visualisation</h2>

    <!-- URL Path -->
    <div class="row">
        <div class="col-sm-1"><label for="tests">Pages:</label></div>
        <div class="col-lg-2">
            <select class="form-control" id="tests" name="test[]">
                @if($trials->count()!=0)
                    @foreach($trials as $trial)
                        <option value="{{$trial->id}}">{{$trial->name}}</option>
                    @endforeach
                @else
                    <option value="none">Aucune page</option>
                @endif
            </select>
        </div>    
        <div class="col-lg-1"><label for="tests">Sujets:</label></div>
        <div class="col-lg-2">
            <select id="subjects" name="subjects[]" multiple="multiple"> 
                 @if($subjects->count()!=0)
                    @foreach($subjects as $subject)
                        <option value="{{$subject->id}}">{{$subject->name}}</option>
                    @endforeach
                @else
                    <option value="none">Aucune page</option>
                @endif                
            </select>
        </div>
        <div class="col-lg-1 " id="subjectdetails" >
            Subject details
        </div>
    </div>

    <div class="row" id="draggable">
        <div class="col-lg-12" >
            <div class="panel panel-primary" >
                 <div class="panel-heading panel-heading-controls">
                    <i class="fa fa-object-group" aria-hidden="true"></i> <h1 id="hint">URL Path</h1>
                 </div>

                 <div class="panel-body">

                     <!-- The Modal -->
                     <div id="pageDetails" class="modal">

                         <!-- Modal content -->
                         <div class="modal-content">
                             <div class="modal-header">
                                 <span class="close">&times;</span>
                                 <h2 id="modal-title"></h2>
                         </div>
                             <div class="modal-body">
                                 <ul>
                                    <li><a id="link-stats" href="#"><i class="fa fa-pie-chart"></i> Statistiques sur la page</a></li>
                                    <li><a id="link-heat" href="#"><i class="fa fa-thermometer-half"></i> Heatmap</a></li>
                                    <li><a id="link-scan" href="#"><i class="fa fa-eye"></i> Scanpath</a></li>
                                 </ul>
                             </div>
                         </div>

                     </div>

                    <div style="position: relative;">
                            <svg id="urlpath" height="600" width="100%" style="z-index: -1;"></svg>
                        <div id="linkDetails" hidden="hidden" style="position: absolute; padding: 10px; top: 0; right: 0; background-color: lightgrey; width: 180px; margin: 0;">
                            <div class="subject"></div>
                            <div class="color" style="width: 100%; height: 5px;"></div>
                            <div class="buttons">
                                <p></p><a href="#">Données utilisateurs</a></p>
                                <p></p><a href="#">Scanpath dynamique</a></p>
                                <p></p><a href="#">Résultats questionnaire</a></p>
                                <p></p><a href="#">Webcam</a></p>
                            </div>
                        </div>
                    </div>
                 </div>
            </div>                
        </div>

    </div>



    <h3><i class="fa fa-users" aria-hidden="true"></i> Sujets</h3>


    <!-- Statistics -->

    <div class="panel panel-primary panel-content-hiding">
        <div class="panel-heading panel-heading-controls">
            <h1><i class="fa fa-pie-chart" aria-hidden="true"></i> Statistiques</h1>
            <a class="btn btn-danger pull-right disable btn-reduce"><i class="fa fa-minus-square-o" aria-hidden="true"></i> Réduire</a>
        </div>
        <div class="panel-body">

            
            <div class="panel-body-box">


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
            </div>
        </div>
    </div>               

    
    <hr/>

    <p>
        <span class="label label-default pull-right">
            <b id="subject-count">{{ $subjects_count }}</b> Sujets
        </span>
    </p>
    <br/>

    @if($subjects->count()!=0)

        @foreach($subjects->chunk(4) as $chunk_subject)
            <div class="row">
                @foreach($chunk_subject as $subject)
                    <div class="col-md-3">

                        <div class="thumbnail">
                            <div class="caption">
                                <h3>{{$subject->name}}</h3>
                                <p><b>Age:</b> {{$subject->age}}</p>
                                <p><b>Type:</b> {{$subject->sex}}</p>


                                <?php
                                    if(is_null($google_spreed_sheet))
                                    {
                                        $eval = FALSE;
                                    }
                                    else
                                    {
                                        $eval = $google_spreed_sheet->getSubjectEvaluation($subject);
                                    }
                                ?>
                                
                                <p><b>Evaluation Google :
                                    @if($eval === FALSE)
                                        <span class="label label-danger">Non trouvée</span>
                                    @else
                                        <span class="label label-success">{{ $eval }}</span>
                                    @endif
                                </b></p>


                                <?php $trials = $subject->urlPathDetailed();?>
                                @foreach($trials as $trial)
                                    <!--<p>{{$trial['trial_name'] .'***'.$trial['event_param']}}</p>-->
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endforeach
    @else
        <p>Aucun sujet</p>
    @endif

@stop


@section('scripts')

    <!-- Statistics -->
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script src="{{ URL::asset('js/pages/statistics/statistics.tools.js') }}"></script>
    <script src="{{ URL::asset('js/pages/statistics/statistics-trial.page.js') }}"></script>


    <!-- Ressources -->
    <script type="text/javascript" src="{{ URL::asset('js/ressources/snap.svg.js') }}"></script>
    <script type="text/javascript" src="{{ URL::asset('js/ressources/svg-pan-zoom.js') }}"></script>

    <!-- Populating -->
    <script type="text/javascript">
        // Get the modal
        var modal = document.getElementById('pageDetails');

        // Get the <span> element that closes the modal
        var span = document.getElementsByClassName("close")[0];

        // When the user clicks on <span> (x), close the modal
        span.onclick = function() {
            modal.style.display = "none";
        };

        // When the user clicks anywhere outside of the modal, close it
        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = "none";
            }
        };



        $(document).ready(function() {
            $('#subjects').multiselect();
            $('#subjects').multiselect('selectAll', false);
            $('#subjects').multiselect('updateButtonText');


            // dragging (?)
            var panelList = $('#draggable');

            panelList.sortable({
                // Only make the .panel-heading child elements support dragging.
                // Omit this to make then entire <li>...</li> draggable.
                handle: '.panel-heading',
                update: function() {
                    $('.panel', panelList).each(function(index, elem) {
                        var $listItem = $(elem),
                            newIndex = $listItem.index();

                        // Persist the new indices.
                    });
                }
            });
        });


        //Populate from database
        <?php

            $last = '';
            echo 'var subjectsArray = [';
                foreach($subjects as $subject){
                    $trials = $subject->urlPathDetailed();
                    $lastkey = key(end($trials));
                    echo "\n".'{"name": "'.$subject->name.'", "evaluation": "'.$google_spreed_sheet->getSubjectEvaluation($subject).'" , age:"'.$subject->age.'", occupation:"'.$subject->category.'", "trials": [';

                    $trialsNames = array_map(function($trial) {return '"'.$trial['trial_name'].'"'; }, $trials);

                    $lastEventParam = array_values(array_slice($trials, -1))[0]['event_param']; ;


                    $trialsNamesClean = [];

                    $last = "";
                    foreach($trialsNames as $trialName){
                        if($trialName != $last){
                            array_push($trialsNamesClean, $trialName);
                            $last = $trialName;
                        }
                    }

                    /*if(in_array($lastEventParam, ["Key: F1", "Key: F2"])){
                        array_push($trialsNamesClean, '"'.$lastEventParam.'"');
                    }*/

                    $trialsString = implode(',', $trialsNamesClean);
                    echo $trialsString;
                    echo '], "lastEvent": "'.$lastEventParam.'"},';

                }
            echo "\n];"

        ?>

        var statistic_url = "{{ URL::to('/test/'.$test->id.'/trial') }}";
       
        /*
        var subjectsArray=[
            {"name": "1", "trials": ["A", "B", "C", "D"]},
            {"name": "1", "trials": ["C", "B","C", "B","C", "D","C", "B","C", "B","C", "B","C", "B","C", "B","C", "B",]},
            {"name": "1", "trials": ["A", "C", "B","C", "B","C", "B","C", "B","C", "B","C", "B","C", "B",]},
            {"name": "1", "trials": ["B", "B", "C","C","C", "C","C","C","C","C","C","C", "D", "A"]},
            {"name": "1", "trials": ["B", "B", "C","C","C","C","C","C","A","C","C","C","C","C","C","C","C","C", "D", "A"]},

            //  {"name": "1", "trials": ["A", "B", "C"]},
            //  {"name": "4", "trials": ["A", "B", "A"]},
            //  {"name": "2", "trials": ["C", "A", "A"]},
            //  {"name": "2", "trials": ["C", "A", "A"]},
            //  {"name": "2", "trials": ["A", "B", "A"]},
            //  {"name": "4", "trials": ["B", "B", "A", "C", "A", "C"]},
            //  {"name": "4", "trials": ["B", "A", "C"]},
            //  {"name": "4", "trials": ["C", "A", "B"]},
            //  {"name": "1", "trials": ["A", "B", "C", "D"]},
            // {"name": "2", "trials": ["A", "B", "A"]},
            // {"name": "4", "trials": ["B", "B", "C", "C"]},
            //  {"name": "4", "trials": ["B", "A", "C"]},
            //  {"name": "4", "trials": ["C", "A", "B"]},
            //  {"name": "1", "trials": ["A", "B", "C", "D"]},
            // {"name": "2", "trials": ["A", "B", "D", "C"]},
            // {"name": "3", "trials": ["A", "D", "B", "C"]},
        ];*/
    </script>


    <!-- Main script -->
    <script type="text/javascript" src="{{ URL::asset('js/pages/urlpath/urlpath.page.js') }}"></script>

@stop



