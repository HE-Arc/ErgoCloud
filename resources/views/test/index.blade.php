@extends ('layouts.base')


@section ('content')

    <h2>Tests</h2>
    
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Nom</th>
                <th>Nombre de participant</th>
                <th>Objectif du test</th>
                <th>&nbsp;</th>
            </tr>
        </thead>
        <tbody>
            @foreach($tests as $test)
            <tr>
                <td><a href="{{ URL::route('filter', array('test_id' => $test->id)) }}">{{$test->name}} </a></td>
                <td>
                    {{ $test->subjects->count() }} 
                    @if(isset($calibsInfo[$test->id])) 
                        <span class="label label-primary">Calibration disponnible</span>
                    @endif
                </td>
                <td><textarea rows="3" cols="60" disabled>{{$test->instruction}}</textarea></td>
                <td>
                    <!--<a href="{{ URL::route('filter', array('test_id' => $test->id)) }}" class="btn btn-success btn-block"><i class="fa fa-filter" aria-hidden="true"></i> Filtrer</a>
                    <a href="{{ URL::route('statistics_test', array('test_id' => $test->id)) }}" class="btn btn-primary btn-block"><i class="fa fa-pie-chart" aria-hidden="true"></i> Statistiques</a>                    
                    -->
                    <a href="{{ URL::route('show_test', array('test_id' => $test->id)) }}" class="btn btn-success btn-block"><i class="fa fa-play" aria-hidden="true"></i> Ouvrir</a>                    
                    
                    
                    <!--
                    @if(isset($calibsInfo[$test->id])) 
                        <a href="{{ URL::route('calibration', array('test_id' => $test->id)) }}" class="btn btn-warning btn-block"><i class="fa fa-dot-circle-o" aria-hidden="true"></i> Calibration</a>
                    @endif
                    -->
                </td>
            </tr>  
            @endforeach 
        </tbody>
    </table>  



    <a href="{{URL::to('/import')}}" class="btn btn-success pull-right btn-suite"><i class="fa fa-upload" aria-hidden="true"></i> Importer les données</a>
             

@stop
