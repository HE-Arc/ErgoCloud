<div class="test-header">
    <div class="container">
        <div class="row nav-status">
            <div class="col-xs-4 nav-status-1 nav-status-common
                @if ($status == 0)
                    nav-status-active
                @endif
                ">
                <h3 class="nav-status-header"><span class="label label-success">1</span> Import des données <i class="fa fa-arrow-right pull-right" aria-hidden="true"></i></h3>
                <p>Liste / Import</p>
                
            </div>
            <div class="col-xs-4 nav-status-2 nav-status-common

                @if ($status == 1)
                    nav-status-active
                @endif
                ">
                <h3 class="nav-status-header"><span class="label label-success">2</span>  Filtrage des données <i class="fa fa-arrow-right pull-right" aria-hidden="true"></i></h3>
                <p>Calibration / Filtrage</p>
            </div>
            <div class="col-xs-4 nav-status-3 nav-status-common

                @if ($status == 2)
                    nav-status-active
                @endif
                ">
                <h3 class="nav-status-header"><span class="label label-success">3</span>  Visualisation</h3>
                <p>URL Path / Heatmap / Scanpath / Statistiques </p>
            </div>
        </div>
    </div>
</div>