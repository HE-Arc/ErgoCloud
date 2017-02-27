@extends ('layouts.base')

@section ('header')
    {!! View::make('partials.test-header', array('test_name' => $test->name))  !!}
@stop


@section ('content')

    @if($sortedCalibs["counter"] != 0)
        <h2><i class="fa fa-dot-circle-o" aria-hidden="true"></i> Calibration</h2>

        <h3>Statistiques</h3>
        <p> Nombre total de sujets : {{$totalSubjects}}</p>
        <p> Nombre total de calibrations : {{$sortedCalibs["totalC"]}}</p>

        <br/>

        <table class="table table-condensed">
            <thead>
                <tr>
                    <th>Qualité</th>
                    <th>Nombre d'occurences</th>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td> <strong>Parfaite</strong></td>
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
                    <td >{{$sortedCalibs["goodCalibs"]->count()}}</td>
                    <td>{{$sortedCalibs["goodPr"]}}%</td>
                    @if($sortedCalibs["goodCalibs"]->count())
                        <td class="text-right"><a id="btnGood"  class="btn btn-default"></a></td>
                    @endif
                </tr>  
                <tr>
                    <td>Moyenne</td>
                    <input type="hidden" id="moderate" value="{{$sortedCalibs["moderateDisable"]}}">
                    <td >{{$sortedCalibs["moderateCalibs"]->count()}}</td>
                    <td>{{$sortedCalibs["moderatePr"]}}% </td>
                    @if($sortedCalibs["moderateCalibs"]->count())
                        <td class="text-right"><a id="btnModerate"  class="btn btn-default"></a></td>
                    @endif
                </tr>  
                <tr>
                    <td>Mauvaise</td>
                    <input type="hidden" id="poor" value="{{$sortedCalibs["poorDisable"]}}">
                    <td >{{$sortedCalibs["poorCalibs"]->count()}}</td>
                    <td>{{$sortedCalibs["poorPr"]}}%</td>
                    @if($sortedCalibs["poorCalibs"]->count()!=0)
                        <td class="text-right"><a id="btnPoor" class="btn btn-default"></a></td>
                    @endif
                </tr>                 
            </tbody>
        </table> 
 
    @else

      <h4>Le test <strong>{{$test->name}}</strong> n'a aucune calibration enregistrée</h4>
      <h5>Il a été créé avec une ancienne version d'Ogama.</h5>

    @endif

@stop

@section ('scripts')

    <div id="hidden-informations">
        <span id="test-infos-id">{{ $test->id }}</span>
        <span id="calibration-state-url">{{ url('/calibration/state') }}</span>
        <span id="calibration-test-url">{{ URL::route('calibration', array('test_id' => $test->id)) }}</span>
    </div>

    <script src="{{ URL::asset('js/pages/calibration/calibration.page.js') }}"></script>
@stop
