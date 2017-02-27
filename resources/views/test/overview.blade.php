@extends ('layouts.base')
@section ('content')
    <div class="row" > 
        <div class="col-sm-1"><label for="tests">Trials:</label></div>        
        <div class="col-lg-2">
            <select class="form-control" id="tests" name="test[]">
                @if($trials->count()!=0)
                    @foreach($trials as $trial)
                        <option value="{{$trial->id}}">{{$trial->name}}</option>
                    @endforeach
                @else
                    <option value="none">No trial</option>
                @endif
            </select>
        </div>    
        <div class="col-lg-1"><label for="tests">Subjects:</label></div>
        <div class="col-lg-2">
            <select id="subjects" name="subjects[]" multiple="multiple"> 
                 @if($subjects->count()!=0)
                    @foreach($subjects as $subject)
                        <option value="{{$subject->id}}">{{$subject->name}}</option>
                    @endforeach
                @else
                    <option value="none">No trial</option>
                @endif                
            </select>
        </div>
        <div class="col-lg-1 " id="subjectdetails" >
            Subject details
        </div>
    </div> 
    
    <div class="row" id="draggable">
        <div class="col-lg-4">
            <div class="panel panel-primary" >
                 <div class="panel-heading">Heatmap</div>
                 <div class="panel-body">
                    <div id="heatmap" style="position: relative"></div>
                 </div>
            </div>                
        </div>
        <div class="col-lg-5">
            <div class="panel panel-primary" >
                 <div class="panel-heading">Scanpath</div>
                 <div class="panel-body">
                     <div id="scanpath" style="position: relative"></div>
                 </div>
            </div>        
        </div>
        <div class="col-lg-4">
            <div class="panel panel-primary" >
                 <div class="panel-heading">Clickpath</div>
                 <div class="panel-body">
                     <img src="/img_temp/click.png" style="width: 100%">
                 </div>
            </div>  
        </div>  
         <div class="col-lg-4">
            <div class="panel panel-primary" >
                 <div class="panel-heading">Statistcs</div>
                 <div class="panel-body">
                     <p>Total time: 3 min.</p>
                     <p>Fixation number: 20 </p>                     
                 </div>
            </div>                
        </div>
    </div>
@stop
@section('scripts')

<script src="js/heatmap.min.js"></script>
<script src="js/ergoview.js"></script>


<script type="text/javascript">
    $(document).ready(function() {
        $('#subjects').multiselect();
        $('#subjects').multiselect('selectAll', false);
        $('#subjects').multiselect('updateButtonText');

	var heatmap = new ErgoView({container: 'heatmap', heatmap: true, json_url:'data/25.json'});
        var scanpath = new ErgoView({container: 'scanpath', scanpath: true, json_url:'data/25.json'});

     });
     
     jQuery(function($) {
        var panelList = $('#draggable');

        panelList.sortable({
            // Only make the .panel-heading child elements support dragging.
            // Omit this to make then entire <li>...</li> draggable.
            handle: '.panel-heading', 
            update: function() {
                $('.panel', panelList).each(function(index, elem) {
                     var $listItem = $(elem),
                         newIndex = $listItem.index();

                     // Persist the new indices.
                });
            }
        });
    });
</script>
@stop
