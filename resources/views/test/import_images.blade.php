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
        <div class="progress">
            <div class="progress-bar progress-bar-danger" 
                    role="progressbar" 
                    aria-valuenow="{{ $all_tests_count - $remaning_tests_count }}"
                    aria-valuemin="0" 
                    aria-valuemax="{{ $all_tests_count }}" 
                    style="width: {{ (($all_tests_count - $remaning_tests_count) / $all_tests_count) * 100 }}%">
                <span>{{ $all_tests_count - $remaning_tests_count }}/{{ $all_tests_count }}</span>
            </div>
        </div>

        <h2>{{ $test->name }}</h2>
        <h3><i class="fa fa-upload" aria-hidden="true"></i> Importer les images pour {{ $test->name }}</h3>

        <br/>
        

        <p>
            Veuillez selectionner l'ensemble des fichiers nécessaire au test {{ $test->name }} en une seule fois.<br/>
            Les images sont partagées entre les tests, si vous ne trouvez pas une image, elle peut être dans le dossier d'un autre test.<br/>
            Vous pouvez l'ajouter maintenant ou attendre le test suivant.
        </p>

        <br/>
        <p><b>Fichiers attendus:</b></p>
        <ul class="files-list">
            @foreach($neededImages as $image)
                <li class="needed"><i class="fa fa-square-o" aria-hidden="true"></i> {{ $image }}</li>
            @endforeach
        </ul>

        @if(count($neededImages)==0)
            <p><b><i class="fa fa-smile-o" aria-hidden="true"></i> Tous les fichiers nécessaires pour ce test sont fournis</b></p>
        @endif

        @if(count($uploadedImages)>0)
            <p><b>Fichiers nécessaire au test déjà fournis:</b></p>
            <ul class="files-list">
                @foreach($uploadedImages as $image)
                    <li class="ok"><i class="fa fa-check-square-o" aria-hidden="true"></i> {{ $image }}</li>
                @endforeach
            </ul>
        @endif

        {!! Form::open(array('url'=>'/test/'.$test->id.'/upload/images','method'=>'POST', 'files'=>true, 'enctype' => 'multipart/form-data')) !!}
            <div class="control-group">
                <div class="controls">
                    {!! Form::file('images[]', array('multiple'=>true, 'class'=>'btn btn-primary btn-block')) !!}
                </div>
            </div>
            <br/>
            {!! Form::submit('Importer les images', array('class'=>'btn btn-warning btn-block file-upload',  'name'=>'submit')) !!}
            {!! Form::submit('Passer', array('class'=>'btn btn-danger btn-block file-upload','name'=>'pass')) !!}
        {!! Form::close() !!}

    </div>
@stop