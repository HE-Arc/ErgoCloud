@extends ('layouts.base')
@section ('content')

    <div class="import-msgs">
        <div class="alerts-container">
            @if(Session::has('importsuccess'))
                <div class="alert alert-success">
                    @if(is_array(Session::get('importsuccess')))
                        @foreach(Session::get('importsuccess') as $s)
                            <p>{!! $s !!}</p>
                        @endforeach
                    @else
                        <p>{!! Session::get('importsuccess') !!}</p>
                    @endif
                </div>
            @endif
        </div>
    </div>

    <div class="import-page">
        <h2><i class="fa fa-upload" aria-hidden="true"></i> Importer une nouvelle base de tests</h2>


        <p><b>Attention, En chargeant un nouveau fichier .db les données actuellement chargées seront perdues.</b></p><br/>
        <p>Veuillez fournir un fichier de type DB, exporté d'Ogama.</p>

        {!! Form::open(array('url'=>'/upload','method'=>'POST', 'files'=>true)) !!}
            <div class="control-group">
                <div class="controls">
                    {!! Form::file('dbfile', array('class'=>'btn btn-primary btn-block')) !!}
                </div>
            </div>
            <br/>
            {!! Form::submit('Importer', array('class'=>'btn btn-warning btn-block file-upload')) !!}
        {!! Form::close() !!}
    </div>
    
@stop