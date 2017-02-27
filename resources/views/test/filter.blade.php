@extends('layouts.base')

@section ('header')
    {!! View::make('partials.test-header', array('test_name' => $test->name))  !!}
@stop

@section('content')

        <h2><i class="fa fa-filter" aria-hidden="true"></i> Filtrage</h2>
        <div id="subjects-filters">
             <div class="panel panel-primary" >
                <div class="panel-heading">Filtres sur les sujets</div>
                <div class="panel-body">  
<!--                    <form action="" method="GET">-->
                    <div class="col-md-2 subject">
                        <select id="subjects" name="subjects[]" multiple="multiple" style="overflow-x :scroll;width:100px;"> 
                             @if($subjects->count()!=0)
                                @foreach($subjects as $subject)
                                    <option value="{{$subject->id}}">{{ $subject->name }}</option>
                                @endforeach
                            @else
                                <option value="none">Aucun sujet</option>
                            @endif                
                        </select>
                    </div>                        
                    <div id="detailsAll" class="col-lg-10" style="visibility : hidden" >                           
                        <div class="col-md-2" id="sex"><strong>Type</strong> <br>
                            <input type="radio" name="gender" value="all" checked> Tout<br>
                            <input type="radio" name="gender" value="male" > Homme<br>
                            <input type="radio" name="gender" value="female"> Femme
                        </div>
                        <div class="col-md-2" id="age"><strong>Age</strong><br>
                            <input type="radio" name="age" value="all" checked> Tout<br>
                            <input type="radio" name="age" value="30" > 18-30<br>
                            <input type="radio" name="age" value="40"> 31-40
                        </div>
                        <div class="col-md-2" id="job"><strong>Travail</strong><br>
                            <input type="radio" name="job" value="all" checked> Tout<br>
                        </div>
                        <div class="col-md-2" id="glasses"><strong>Lunettes</strong> <br>
                            <input type="radio" name="glasses" value="all" checked> Tout<br>
                            <input type="radio" name="glasses" value="with" > Avec<br>
                            <input type="radio" name="glasses" value="without"> Sans
                        </div>
                        <div class="col-md-2" id="language"><strong>Langue</strong> <br>
                            <input type="checkbox" name="language[]" value="all" checked> Tout<br>
                            <input type="checkbox" name="language[]" value="français" > Français<br>
                            <input type="checkbox" name="language[]" value="anglais"> Anglais<br>
                            <input type="checkbox" name="language[]" value="allemand"> Allemand<br>
                            <input type="checkbox" name="language[]" value="italienne"> Itallien<br>
                        </div>
                    </div>  



                    <hr/>

                    <div style="margin-top: 3%"> Filtres sur les sujets selectionnées: <br>
                        <a class="btn btn-default pull-right"  id="getFilterTests" style="margin-left: 3%"> Filtres Test</a>
                        <a class="btn btn-default pull-right"  id="getFilterTrials" style="margin-left: 3%" > Filtres Pages </a>
                        <a class="btn btn-default pull-right"  id="getFilterAOI" style="margin-left: 3%"> Filtres AOIs </a>
                    </div>
                    <!--</form>-->
                </div>
                </div> 
        </div> 
        <form id="detailedFilters" action="" method="GET">
            <div class="raw" id="testFilter">
                 @include('partials.filter-test')   
            </div>          
            <div class="raw" id="trialsFilter">
                @include('partials.filter-trials')           
            </div>        
            <div class="raw" id="AOIFilter">
                @include('partials.filter-aois')
            </div>
        </form>

         <div id="filter-subjects-infos">
            <div><b>Nombre de sujet avant filtre:</b> <span id="all-subjects-count" class="label label-default">{{ $subjects->count() }}</span></div>
            <div><b>Nombre de sujet après filtre:</b> <span id="filtered-subjects-count" class="label label-default">NA</span></div>
            <br/>
            <div><b>Résultat:</b> <span id="percent-subjects-with-all" class="label label-success">NA</span></div>
        </div>
           
        <a class="btn btn-success pull-right btn-valid-filters btn-suite" href="{{ URL::route('urlpath', array('test_id' => $id)) }}"><i class="fa fa-arrow-circle-o-right" aria-hidden="true"></i> Visualiser les résultats</a> 

        <div class="virtual-margin">&nbsp;</div>
@stop


@section ('scripts')
    <div class="hidden-informations">
        <!-- filter.page.js -->
        <span id="show-subjects-details-url">{{ url('/filter/subject') }}</span>
        <span id="filter-subjects-url">{{ url('/filter/'.$test->id.'/subjects') }}</span>

        <!-- filter-aois.partial.js -->
        <span id="get-aois-filter-url">{{ url('/filter/aois') }}</span>

        <!-- filter-test.partial.js -->
        <span id="get-test-filter-url">{{ url('/filter/test') }}</span>

         <!-- filter-trials.partial.js -->
        <span id="get-trials-filter-url">{{ url('/filter/trials') }}</span>
    </div>

    <script src="{{ URL::asset('js/pages/filter/filter.page.js') }}"></script>
    <script src="{{ URL::asset('js/pages/filter/filter-aois.partial.js') }}"></script>
    <script src="{{ URL::asset('js/pages/filter/filter-test.partial.js') }}"></script>
    <script src="{{ URL::asset('js/pages/filter/filter-trials.partial.js') }}"></script>
@stop


