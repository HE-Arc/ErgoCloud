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

        <h2><i class="fa fa-exclamation-triangle" aria-hidden="true"></i> Images manquantes</h2>
        <h3>Certaines images sont manquantes</h3>

        <br/>
        

        <p>
            Certaines images nécessaire à la nouvelle base de données n'ont pas été fournies, veuillez fournir ces images.
        </p>

        <br/>
        <p><b>Fichiers attendus:</b></p>
        <ul class="files-list">
            @foreach($neededImages as $image)
                <li class="needed"><i class="fa fa-square-o" aria-hidden="true"></i> {{ $image }}</li>
            @endforeach
        </ul>


        {!! Form::open(array('url'=>'/test/upload/missing_images','method'=>'POST', 'files'=>true, 'enctype' => 'multipart/form-data')) !!}
            <div class="control-group">
                <div class="controls">
                    {!! Form::file('images[]', array('multiple'=>true, 'class'=>'btn btn-primary btn-block')) !!}
                </div>
            </div>
            <br/>
            {!! Form::submit('Importer les images', array('class'=>'btn btn-warning btn-block file-upload', 'name'=>'submit')) !!}
            {!! Form::submit('Ignorer', array('class'=>'btn btn-danger btn-block file-upload', 'name'=>'pass')) !!}
        {!! Form::close() !!}

    </div>
@stop