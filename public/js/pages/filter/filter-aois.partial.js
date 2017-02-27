//  set data for aoi filters 
$('a#getFilterAOI').bind('click', function(){  

    $('#panel-box-aois').fadeOut(0);

    $('#AOIFilter').show(); 
        var setFilters = getSubjectSetFilters();
        $.ajax({
        type: 'POST',
        url: $('#get-aois-filter-url').html(),
        data: getSubjectSetFilters(),               
        dataType: "json",
        success: function(data)
        {
            $('#loading-box-aois').fadeOut(0);
            $('#panel-box-aois').fadeIn();
            
            //console.log(data);
            if(data.aois.length >0)
            {
                $('#aoisDetails').show();
                if($('#aoisF').val()== null){
                    $.each(data.aois, function(key, value){
                        $("#aoisF").append('<option value="'+value['id']+'">'+value['name']+'</option>');   
                    }); 
                }   
                $('#aoisF').multiselect({
                    includeSelectAllOption: true,
                    numberDisplayed: 0,                         
                });                      
                $('#aoisF').multiselect('selectAll', false);                    
                $('#aoisF').multiselect('refresh');                     
                setFiltersValues(data); 
                $('#aoisF').change();
            }
            else 
            {
                $('#aoisDetails').hide();
                $('#aoisF').hide();
                if(!$('#alert').length){
                    $('#aoisF').parents(".trials").append('<div class="alert alert-danger" id="alert"><p> No AOI defined </p> </div>')
                }                    
            }
        },
        error: function(data)
        {
            console.log(data);
        },
    });   
});

function setFiltersValues(data)
{   

    // round value
    data["min_tff"] = parseInt(data["min_tff"] * 10)/10;
    data["max_tff"] = Math.ceil(data["max_tff"] * 10)/10;

    data["min_tfc"] = parseInt(data["min_tfc"] * 10)/10;
    data["max_tfc"] = Math.ceil(data["max_tfc"] * 10)/10;
   
    data["min_tff-tfc"] = parseInt(data["min_tff-tfc"] * 10)/10;
    data["max_tff-tfc"] = Math.ceil(data["max_tff-tfc"] * 10)/10;

    data["min_time_f"] = parseInt(data["min_time_f"] * 10)/10;
    data["max_time_f"] = Math.ceil(data["max_time_f"] * 10)/10;

    data["min_time_relative"] = parseInt(data["min_time_relative"] * 10)/10;
    data["max_time_relative"] = Math.ceil(data["max_time_relative"] * 10)/10;

    data["min_aoi_noc"] = parseInt(data["min_aoi_noc"] * 10)/10;
    data["max_aoi_noc"] = Math.ceil(data["max_aoi_noc"] * 10)/10;

    data["min_relative"] = parseInt(data["min_relative"] * 10)/10;
    data["max_relative"] = Math.ceil(data["max_relative"] * 10)/10;

    data["min_diff"] = parseInt(data["min_diff"] * 10)/10;
    data["max_diff"] = Math.ceil(data["max_diff"] * 10)/10;

    data["min_time"] = parseInt(data["min_time"] * 10)/10;
    data["max_time"] = Math.ceil(data["max_time"] * 10)/10;


    data["avg_tfc"] = Math.round(data["avg_tfc"] * 10)/10;
    data["avg_tff"] = Math.round(data["avg_tff"] * 10)/10;
    data["avg_diff"] = Math.round(data["avg_diff"] * 10)/10;
    data["avg_time"] = Math.round(data["avg_time"] * 10)/10;
    data["avg_relative"] = Math.round(data["avg_relative"] * 10)/10;


    // Set min/max values for sliders
    $('#tff').attr('data-slider-max',  data["max_tff"]);
    $('#tff').attr('data-slider-min',  data["min_tff"]);

    $('#tfc').attr('data-slider-max',  data["max_tfc"]);
    $('#tfc').attr('data-slider-min',  data["min_tfc"]);

    $('#tff-tfc').attr('data-slider-max',  data["max_tff-tfc"]);
    $('#tff-tfc').attr('data-slider-min',  data["min_tff-tfc"]);

    $('#time_f').attr('data-slider-max',  data["max_time_f"]);
    $('#time_f').attr('data-slider-min',  data["min_time_f"]);

    $('#time_relative').attr('data-slider-max',  data["max_relative"]);
    $('#time_relative').attr('data-slider-min',  data["min_relative"]);

    $('#aoi_noc').attr('data-slider-max',  data["max_aoi_noc"]);
    $('#aoi_noc').attr('data-slider-min',  data["min_aoi_noc"]);




    if(data["min_tff"] == data["max_tff"])             
        $('#tffSlider').css("width", "1px");//$('#tffDiv').hide();
    $('#tffDiv').show(); 
    $('#min_tff').text(data["min_tff"]+" sec");
    $('#max_tff').text(data["max_tff"]+" sec");
    $('.range_tff').text(data["min_tff"]+" sec");
    $('#tff').bootstrapSlider('setAttribute', 'max', data["max_tff"]);               
    $('#tff').bootstrapSlider('setAttribute', 'min', data["min_tff"]);            
    $('#avr_tff').text(data["avg_tff"]+" sec");
        
        
    $('#min_tfc').text(data["min_tfc"]+" sec");
    $('#max_tfc').text(data["max_tfc"]+" sec");
    $('.range_tfc').text(data["min_tfc"]+" sec");
    $('#tfc').bootstrapSlider('setAttribute', 'max', data["max_tfc"]);               
    $('#tfc').bootstrapSlider('setAttribute', 'min', data["min_tfc"]);
    $('#avr_tfc').text(data["avg_tfc"]+" sec");
    if(data["min_tfc"] == data["max_tfc"])
        $('#tfcSlider').css("width", "1px");//$('#tfcDiv').hide();
        
        
    $('#min_tff-tfc').text(data["min_diff"]+" sec");
    $('#max_tff-tfc').text(data["max_diff"]+" sec");
    $('.range_tff-tfc').text(data["min_diff"]+" sec");
    $('#tff-tfc').bootstrapSlider('setAttribute', 'max', data["max_diff"]);               
    $('#tff-tfc').bootstrapSlider('setAttribute', 'min', data["min_diff"]); 
    $('#avr_tff-tfc').text(data["avg_diff"]+" sec");
    if(data["min_diff"] == data["max_diff"])
        $('#tff-tfcSlider').css("width", "1px");
            
    $('#min_time_f').text(data["min_time"]+" sec");
    $('#max_time_f').text(data["max_time"]+" sec");
    $('.range_time_f').text(data["min_time"]+" sec");
    $('#time_f').bootstrapSlider('setAttribute', 'max', data["max_time"]);               
    $('#time_f').bootstrapSlider('setAttribute', 'min', data["min_time"]); 
    $('#avr_time_f').text(data["avg_time"]+" sec");
    if(data["min_time"] == data["max_time"])
        $('#time_fSlider').css("width", "1px");  
            
    $('#min_time_relative').text(data["min_relative"]+" %");
    $('#max_time_relative').text(data["max_relative"]+" %");
    $('.range_time_relative').text(data["min_relative"]+" %");
    $('#time_relative').bootstrapSlider('setAttribute', 'max', data["max_relative"]);               
    $('#time_relative').bootstrapSlider('setAttribute', 'min', data["min_relative"]); 
    $('#avr_time_relative').text(data["avg_relative"]+" %");
    if(data["min_relative"] == data["max_relative"])
        $('#time_relativeSlider').css("width", "1px");       
            
    $('#min_aoi_noc').text(data["min_clicks"]);
    $('#max_aoi_noc').text(data["max_clicks"]);
    $('.range_aoi_noc').text(data["min_clicks"]+" ");
    $('#aoi_noc').bootstrapSlider('setAttribute', 'max', data["max_clicks"]);               
    $('#aoi_noc').bootstrapSlider('setAttribute', 'min', data["min_clicks"]); 
    $('#avr_aoi_noc').text(data["avg_clicks"]);
    if(data["min_clicks"] == data["max_clicks"])
        $('#aoi_nocSlider').css("width", "1px");  



    // Verifications
    if(parseFloat(data["avg_tfc"]) < parseFloat(data["avg_tff"]) || parseFloat(data["avg_tfc"]) == 0 || parseFloat(data["avg_tff"]) == 0)
    {
         $('#avr_tff-tfc').append(" <i class='fa fa-exclamation-triangle' aria-hidden='true' data-toggle='tooltip' title='Le résultat peut être inexact'></i>");
    }
            
}


$('#aoisF').change(function(){  
    aoiFilters['aois'] = $('#aoisF').val();         
}); 
