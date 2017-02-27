<div class="row">
    <div class="col-md-4">
        <div class="panel panel-primary">
            <!-- Default panel contents -->
            <div class="panel-heading">Générations</div>

                <table class="table">
                    @foreach($generation_counts as $name => $count)
                        <tr class="generation-row">
                            <td class="generation-row-name">{{ $name }}</td>
                            <td class="generation-row-count">{{ $count }}</td>
                        </tr>
                    @endforeach
                </table>
                <hr/>
                <div class="panel-content">

                    <div id="generations-chart"></div>

                </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="panel panel-primary">
            <!-- Default panel contents -->
            <div class="panel-heading">Langue</div>

                <table class="table">
                    @foreach($langage_counts as $name => $count)
                        <tr class="langages-row">
                            <td class="langages-row-name">{{ $name }}</td>
                            <td class="langages-row-count">{{ $count }}</td>
                        </tr>
                    @endforeach
                </table>
                <hr/>
                <div class="panel-content">

                    <div id="langages-chart"></div>

                </div>
        </div>
    </div>

        <div class="col-md-4">
        <div class="panel panel-primary">
            <!-- Default panel contents -->
            <div class="panel-heading">Type</div>

                <table class="table">
                    @foreach($type_counts as $name => $count)
                        <tr class="type-row">
                            <td class="type-row-name">{{ $name }}</td>
                            <td class="type-row-count">{{ $count }}</td>
                        </tr>
                    @endforeach
                </table>
                <hr/>
                <div class="panel-content">
                    <div id="type-chart"></div>
                </div>
        </div>
    </div>

</div>



<div class="row">
    <div class="col-md-4">
        <div class="panel panel-primary">
            <!-- Default panel contents -->
            <div class="panel-heading">Porteur de lunettes</div>

                <table class="table">
                    @foreach($glasses_counts as $name => $count)
                        <tr class="glasses-row">
                            <td class="glasses-row-name">{{ $name }}</td>
                            <td class="glasses-row-count">{{ $count }}</td>
                        </tr>
                    @endforeach
                </table>
                <hr/>
                <div class="panel-content">

                    <div id="glasses-chart"></div>

                </div>
        </div>
    </div>

        <div class="col-md-4">
        <div class="panel panel-primary">
            <!-- Default panel contents -->
            <div class="panel-heading">Gaucher/Droitier</div>

                <table class="table">
                    @foreach($handedness_counts as $name => $count)
                        <tr class="handedness-row">
                            <td class="handedness-row-name">{{ $name }}</td>
                            <td class="handedness-row-count">{{ $count }}</td>
                        </tr>
                    @endforeach
                </table>
                <hr/>
                <div class="panel-content">

                    <div id="handedness-chart"></div>

                </div>
        </div>
    </div>

        <div class="col-md-4">
        <div class="panel panel-primary">
            <!-- Default panel contents -->
            <div class="panel-heading">Genre</div>

                <table class="table">
                    @foreach($sex_counts as $name => $count)
                        <tr class="sex-row">
                            <td class="sex-row-name">{{ $name }}</td>
                            <td class="sex-row-count">{{ $count }}</td>
                        </tr>
                    @endforeach
                </table>
                <hr/>
                <div class="panel-content">

                    <div id="sex-chart"></div>

                </div>
        </div>
    </div>

    

        
</div>