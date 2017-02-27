$(document).ready(function() {      
    var selected = [];


    $('.btn-valid-filters').attr('onclick','return false;')
    $('.btn-valid-filters').click(function(){
        var dest = $(this).attr('href');
        showLoading(); //From template-manager.js
        window.location = dest;
    });

    $('#subjects').multiselect({
        includeSelectAllOption: true, 
        numberDisplayed: 1,
        onChange: function(){

            selected = $('#subjects').val();   
            showSubjectsDetails(selected);   
            filterSubjects();
        },
        onSelectAll:function(){
            selected = $('#subjects').val();
            showSubjectsDetails(selected);
        }             
    });

    $('#subjects').multiselect('selectAll', false);
    selected = $('#subjects').val();

    $('#subjects').multiselect('updateButtonText'); 

    hideDetails();       
    showSubjectsDetails(selected);         
    filterSubjects();
    
    $('.slider').bootstrapSlider({
        formatter: function(value) {
            return value;
        }
    });  
    
    $('.disable').bind('click', function(){
        $(this).parents(".raw").hide();   
        $(this).parents(".raw").find('input[type=checkbox]:checked').each(function(){               
            this.checked = false;    
            $(this).change();
        });            
        $(this).parents(".raw").find('input[type=checkbox]:disabled').each(function(){               
            this.disabled = false;                 
        });            
    });

   
});

function showSubjectsDetails(selected)
{
    $('#alert').remove();        
    if(selected != null && selected != "none")
    {
        if(selected.length >1)
        {
            $('#detailsAll').css("visibility", "visible");
            $('#detailsOne').remove();               
        }
        else if (selected.length = 1)
        {
            $('#detailsAll').css("visibility", "hidden");                  
            $.ajax({
                type : 'POST',
                url : $('#show-subjects-details-url').html(),
                data: {
                    subjectId : selected,
                },
                dataType: "json",
                success: function(data)
                {
                    $('.subject').after ('<div class="col-lg-10" id="detailsOne" ><p><strong> Sex: </strong>'+data["sex"]+';<strong> Age: </strong>'+data["age"]+' ; <strong> Job: </strong>  '+data["job"]+'; <strong> Glases: </strong> '+data["glasses"]+
                            '; Language: '+data["language"]+'</p> </strong></div>');  
                },
                error: function(data)
                {    
                    alert("There was an error ! Please contact the admin.");
                },                   
            });
        }
        if($('#testFilter').css('display') != 'none')
            $('#getFilterTests').click();
        if($('#trialsFilter').css('display') != 'none')
            $('#getFilterTrials').click();             
        if($('#AOIFilter').css('display') != 'none')
            $('#getFilterAOI').click();            
    }        
    else
    {
        $('#detailsAll').css("visibility", "hidden");
        $('#detailsOne').remove(); 
        $('#detailsAll').after('<div class="alert alert-danger" id="alert"> <strong>You must select a user !!</strong></div>');   
        $('.disable').click();
    }
}     

function getSubjectSetFilters()
{
    var subjectsFilters = {
        subjects : $('#subjects').val(),
        sex:  $('#sex input[name=gender]:checked').val(),
        age: $('#age input[name=age]:checked').val(),
        job: $('#job input[name=job]:checked').val(),
        glasses: $('#glasses input[name=glasses]:checked').val(),
        language: $('#language :checked').map(function(){return $(this).val();}).get(),
    }
    return(subjectsFilters);
}

function hideDetails()
{
    //$('#testFilter').hide();
    $('#path').hide();
    //$('#trialsFilter').hide();
    //$('#AOIFilter').hide();
}


//
// Listeners
//

/**
 * Called by all checkboxes on click
 */
$('#subjects-filters input[type=checkbox]').bind('click', function(){
    filterSubjects();          
});

/**
 * Called by all radio buttons on click
 */
$('#subjects-filters input[type=radio]').bind('click', function(){
    filterSubjects();   
});

/**
 * Called by all sliders when slide is completed
 */
$('.slider').on('slideStop', function(){
    filterSubjects();
});


/**
 * Called when a range input change
 */
$('.range-input').on('focusout', function(){

    var id = $(this).attr('id');
    var tabSplit = id.split("-");
    var targetSliderId = tabSplit[tabSplit.length - 1];

    if(tabSplit[tabSplit.length - 2] == 'tff'){ //workaround
        targetSliderId = tabSplit[tabSplit.length - 2] + '-' + tabSplit[tabSplit.length - 1];
    }

    var val = parseFloat($(this).val());

    var slider = $('#'+targetSliderId);
    var sliderMinValue = parseFloat(slider.attr('data-slider-min'));
    var sliderMaxValue = parseFloat(slider.attr('data-slider-max'));
    var sliderStepValue = parseFloat(slider.attr('data-slider-step'));

    if(isNaN(val)){
        val = sliderMinValue;
    }
    if(val > sliderMaxValue){
        val = sliderMaxValue;
    }
    if(val < sliderMinValue){
        val = sliderMinValue;
    }

    val = Math.round(val*(1/sliderStepValue))/(1/sliderStepValue);
   
    slider.bootstrapSlider('setValue', val);
    showValue(val, targetSliderId);
    filterSubjects();
    //$('#'+targetSliderId).val($(this).val());
});


/**
 * Called by sliders on change
 */
function showValue(newValue, id)
{            
    newValue = newValue;

    if(id == "visited" || id =="noc" || id =="nos" || id == "aoi_noc" || id=="time_relative")	
    {	
        $('.range_'+id).text(newValue+" ");	
    }
    else
    {            
        $('.range_'+id).text(newValue+" sec" );  
    }           
    if($("#less"+id).is(':checked'))
        $("#less"+id).change();
    if($("#more"+id).is(':checked'))
        $("#more"+id).change()

    $('#range-input-'+id).val(newValue);
}


var testFilters = {paths : []};    
var trialFilters = {};
var aoiFilters = {};
function addToFilters(id, saveTo)
{
    var first = id.substring(0,4);
    var second = id.substring(4);        
    if($("#"+id).is(':checked')) 
    {  
        var min, max;
        if(first == "less")
        { 
            min = ($.trim($('#min_'+second.toLowerCase()).text())).split(" ")[0];
            max = ($.trim($('.range_'+second.toLowerCase()).text())).split(" ")[0];
            $('#more'+second).attr("disabled", true);                 
        }         
        if(first == "more")
        {
            min = ($.trim($('.range_'+second.toLowerCase()).text())).split(" ")[0];
            max = ($.trim($('#max_'+second.toLowerCase()).text())).split(" ")[0];
            $('#less'+second).attr("disabled", true);
        }  
        switch(saveTo) {
            case 'testFilters' :
                testFilters[second] = {
                    min: min ,
                    max: max,
                }  
                break;
            case 'trialFilters' :
                trialFilters[second] = {
                    min: min ,
                    max: max,
                } 
                break;
            case 'aoiFilters' :
                aoiFilters[second] = {
                    min: min ,
                    max: max,
                } 
                break;                   
        }
    }
    else
    {
        if(first == "less")
            $('#more'+second).attr("disabled", false);
        if(first == "more")
            $('#less'+second).attr("disabled", false);
        switch(saveTo) {
            case 'testFilters' :
                delete testFilters[second];
                break;
            case 'trialFilters' :
                delete trialFilters[second];
                break;
            case 'aoiFilters' :
                delete aoiFilters[second];                    
                break;                   
        }           
    }

    filterSubjects();
}

/**
 * Change filter mode
 */
function changeFilterMode(id, saveTo)
{
    switch(saveTo) {
        case 'testFilters' :
            testFilters[id] = $("#"+id).is(':checked');
            break;
        case 'trialFilters' :
            trialFilters[id] = $("#"+id).is(':checked');
            break;
        case 'aoiFilters' :
            aoiFilters[id] = $("#"+id).is(':checked');
            break;                   
    }
    
    filterSubjects();
}

function filterSubjects()
{
    var msgData = {
        subjectsF: getSubjectSetFilters(),
        testFilters: testFilters,
        trialFilters: trialFilters,
        aoiFilters: aoiFilters,                
    };

    console.log('filter subjects');
    console.log(msgData);

    $.ajax({
        type: 'POST',
        url: $('#filter-subjects-url').html(),
        data : msgData,
        dataType: "json",
        success: function(data)
        {
            console.log(data); 
            var resultSubjects = data.result_subjects;

            var subjectsInfos = $('#filter-subjects-infos');

            var allSubjectCount = parseInt(subjectsInfos.find('#all-subjects-count').text());
            var filteredSubjectCount = resultSubjects.length;

            subjectsInfos.find('#filtered-subjects-count').text(filteredSubjectCount);

            var percentSubjectWithAll = (filteredSubjectCount / allSubjectCount) * 100;
            
            subjectsInfos.find('#percent-subjects-with-all').text(Math.round(percentSubjectWithAll) + '% des sujets');

        },
        error: function(data)
        {
            console.log("oOooups... processing error")
            console.log(data);
        },
    });  
}