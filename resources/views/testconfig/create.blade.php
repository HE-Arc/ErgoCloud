@extends('layouts.base')

@section ('header')
    {!! View::make('partials.test-header', array('test_name' => $test->name))  !!}
@stop

@section('content')
    <h2>Créer la configuration du test</h2>
    {{ Form::model($testConfig, array('method' => 'post', 'route' => array('test.{test_id}.testconfig.store', $test->id))) }}    

        <div class="form-group">
            {{ Form::label('google_results_url', "URL complète vers la 'Google SpreadSheet' contenant les résultats") }}
            {{ Form::text('google_results_url', null, array('class' =>'form-control')) }}
            <p><b>ex:</b> https://docs.google.com/spreadsheets/d/1_eseAcMUTj-pQn2edE71zEW9dzbo0Bd9--l1MUXbGFw/edit#gid=1785092430</p>
        </div>

        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    {{ Form::label('name_column', 'Colonne contenant le nom identifiant le sujet') }}
                    {{ Form::text('name_column', null, array('class' =>'form-control')) }}
                    <p><b>ex:</b> A,B,C,...</p>
                </div>
            </div>

            <div class="col-md-4">
                <div class="form-group">
                    {{ Form::label('evaluation_column', 'Colonne contenant l\'évaluation du sujet') }}
                    {{ Form::text('evaluation_column', null, array('class' =>'form-control')) }}
                    <p><b>ex:</b> A,B,C,...</p>
                </div>
            </div>

            <div class="col-md-4">
                <div class="form-group">
                    {{ Form::label('sheet', 'Sheet') }}
                    {{ Form::text('sheet', null, array('class' =>'form-control')) }}
                    <p><b>default:</b> Form Responses 1</p>
                </div>
            </div>
        </div>

        {{ Form::submit('Appliquer',  array('class' =>'btn btn-primary btn-block')) }}

    {{ Form::close() }}
@stop


@section ('scripts')
  
@stop