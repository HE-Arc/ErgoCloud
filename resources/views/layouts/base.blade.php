<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">    
        <meta name="description" content="ErgoCrowd">
        <title>ErgoCrowd</title>  
        <!-- Bootstrap core CSS -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-slider/9.2.0/css/bootstrap-slider.css">
        <!-- <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css"> -->
        
        
        <!-- Custom styles for this template -->
        <link rel="stylesheet" type="text/css" href="{{asset('css/app.css')}}" >      
        
        <!--scripts-->
        <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/2.0.3/jquery.min.js"></script>
        <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js"></script>
        <script type="text/javascript" src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.13/css/bootstrap-multiselect.css">
        <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.13/js/bootstrap-multiselect.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-slider/9.2.0/bootstrap-slider.js"></script>
        <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js"></script>

        <script src="{{ URL::asset('js/global/template-manager.js') }}"></script>

        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">


        @yield('additional-head-inclusions')

    </head>

    <body>

        <div id="loading-overlay" class="site-overlay">
            <div class="content-overlay">
                <p><i class="fa fa-spinner fa-pulse fa-3x fa-fw"></i><br/>
<span>Chargement...</span></p>
            </div>
        </div>

        
        <nav class="site-header navbar">
            <div class="container">
                    <div class="navbar-header pull-left">
                        <a class="navbar-brand" href="{{URL::to('/')}}">ErgoCrowd</a>
                    </div>
                    <!--<ul class="nav navbar-nav pull-right desktop-menu">
                        <li>{!! link_to(URL::to('/'), 'Tests') !!}</li>
                        <li>{!! link_to(URL::to('/import'), 'Importer des données') !!}</a></li>
                        <li>{!! link_to(URL::to('/help'), 'Aide') !!}</li>
                    </ul>-->
            </div>  
        </nav>  
        
        <nav class="site-navbar navbar navbar-inverse mobile-menu">
            <div class="container">
                <div id="navbar">
                    <ul class="nav navbar-nav">
                        <li>{!! link_to(URL::to('/'), 'Tests') !!}</li>
                        <li>{!! link_to(URL::to('/import'), 'Importer des données') !!}</a></li>
                        <li>{!! link_to(URL::to('/help'), 'Aide') !!}</li>
                    </ul>
                </div>
            </div>
        </nav>

        @yield('header')

        {!! View::make('partials.base-status-header', array('status' => $page_status))  !!}
        
        @if(!empty($breadcrumps))
            @include('partials.breadcrumbs-stage', array('breadcrumbs' => $breadcrumps))
        @endif
           
        <div class="container">
            <div class="alerts-container">
                <!-- Alerts -->
                @if(Session::has('success'))
                    <div class="alert alert-success">
                        @if(is_array(Session::get('success')))
                            @foreach(Session::get('success') as $s)
                                <p>{!! $s !!}</p>
                            @endforeach
                        @else
                            <p>{!! Session::get('success') !!}</p>
                        @endif
                    </div>
                @endif

                @if(Session::has('error'))
                    <div class="alert alert-danger">
                        @if(is_array(Session::get('error')))
                            @foreach(Session::get('error') as $errors)
                                @foreach($errors as $e)
                                    <p>{{$e}}</p>
                                @endforeach
                            @endforeach
                        @else
                            <p>{!! Session::get('error') !!}</p>

                            {{ gettype(Session::get('error'))}}
                        @endif
                    </div>
                @endif

              
            </div>


            @yield('content')
        </div>        
        @yield('scripts')   
    </body>
</html>


