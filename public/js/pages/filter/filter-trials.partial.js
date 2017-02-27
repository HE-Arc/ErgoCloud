//set data for trials filters 
$('a#getFilterTrials').bind('click', function(){  
    $('#trialsFilter').show();
    getTrialData(getSubjectSetFilters());
});

$('#trialsF').multiselect({ 
    includeSelectAllOption: true,
    numberDisplayed: 0,   
    onChange: function(){
        var data = { trials : $('#trialsF').val(), };
        getTrialData(data);                
    }, 
});

$('#trialsF').change(function(){  
    //console.log($('#trialsF').val()) ;  
    trialFilters['trials'] = $('#trialsF').val();        
});


function getTrialData($data)
{
    $('#panel-box-trials').fadeOut(0);
    $.ajax({
        type: 'POST',
        url: $('#get-trials-filter-url').html(),
        data: $data,               
        dataType: "json",
        success: function(data){

            $('#loading-box-trials').fadeOut(0);
            $('#panel-box-trials').fadeIn();

            if(data.change_trials){     

                if(!$.isEmptyObject(data.trials)){
                    $('#trialsF').next().show();
                    if($('#trialsF').val()!= null){                            
                        $('#trialsF option[value]').remove();                            
                    }  
                    $.each(data.trials, function(key, value){                              
                            $("#trialsF").append('<option value="'+key+'">'+value+'</option>');   
                        });                                            
                    $('#trialsF').multiselect('selectAll', false);                    
                    $('#trialsF').multiselect('rebuild');
                    setTrialsFiltersValues(data);
                    trialFilters['trials'] = $('#trialsF').val();
                    $('#trialsDetails').show();
                    $('#alertT').remove();
                }
                else{
                    $('#trialsF option[value]').remove();
                    $('#trialsDetails').hide();
                    $('#trialsF').multiselect('rebuild');
                    $('#trialsF').next().hide();

                    if(!$('#alertT').length){
                        $('#trialsF').parents(".trials").append('<div class="alert alert-danger" id="alertT"><p> No visited trials </p> </div>')
                    }                  
                }
            }
            else{                        
                setTrialsFiltersValues(data);
            }  
            
        },
        error: function(data){
            console.log(data);
        },
    });   
}

function setTrialsFiltersValues(data)
{ 
    // round values
    data["min"] = parseInt(data["min"] * 10)/10;
    data["max"] = Math.ceil(data["max"] * 10)/10;

    data["average_duration"] = Math.round(data["average_duration"] * 10)/10;
    data["average_scrolls"] = Math.round(data["average_scrolls"] * 10)/10;
    data["average_clicks"] = Math.round(data["average_clicks"] * 10)/10;


    //set min/max values for sliders
    $('#trialduration').attr('data-slider-max',  data["max"]);
    $('#trialduration').attr('data-slider-min',  data["min"]);

    $('#noc').attr('data-slider-max',  data["max_c"]);
    $('#noc').attr('data-slider-min',  data["min_c"]);

    $('#nos').attr('data-slider-max',  data["max_s"]);
    $('#nos').attr('data-slider-min',  data["min_s"]);



    $('#min_trialduration').text(data["min"]+" sec");
    $('#max_trialduration').text(data["max"]+" sec");
    $('.range_trialduration').text(data["min"]+" sec");
    $('#trialduration').bootstrapSlider('setAttribute', 'max', data["max"]);               
    $('#trialduration').bootstrapSlider('setAttribute', 'min', data["min"]); 
    $('#avr_duration').text(data["average_duration"]+" sec");
    if(data["min"]== data["max"])                   
        $('#durationTrialSlider').css("width", "1px"); 


    $('#min_noc').text(data["min_c"]);
    $('#max_noc').text(data["max_c"]);
    $('.range_noc').text(data["min_c"]+" ");
    $('#noc').bootstrapSlider('setAttribute', 'max', data["max_c"]);
    $('#noc').bootstrapSlider('setAttribute', 'min', data["min_c"]);    
    $('#avr_noc').text(data["average_clicks"]);
        if(data["min_c"]== data["max_c"])                   
        $('#nocSlider').css("width", "1px"); 


    $('#min_nos').text(data["min_s"]);
    $('#max_nos').text(data["max_s"]);
    $('.range_nos').text(data["min_s"]+" ");
    $('#nos').bootstrapSlider('setAttribute', 'max', data["max_s"]);
    $('#nos').bootstrapSlider('setAttribute', 'min', data["min_s"]);   
    $('#avr_nos').text(data["average_scrolls"]);
        if(data["min_s"]== data["max_s"])                   
        $('#nosSlider').css("width", "1px"); 
}