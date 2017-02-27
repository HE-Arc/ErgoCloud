$("#trials").dblclick(function(){
    $("#trials option:selected").each(function () {           
        $("#chosentrials").append('<option value="'+$(this).val()+'">'+$(this).text()+'</option>');
    });            
});

$("#chosentrials").dblclick(function(){
    $("#chosentrials option:selected").each(function () {           
        $(this).remove();
    });            
});

$("#pathToggle").on("click", function(){                      
    $("#path").toggle();
});

var indexPath = 0;    
$("#createPath").click(function(){
    var text = "";
    var pathArray = [];   
    $("#chosentrials option").map(function(){
        text += ' <i class="fa fa-arrow-circle-right" aria-hidden="true"></i> ' + $(this).text();
        pathArray.push(
            {
                'id': $(this).val(),
                'page_name': $(this).text()
            }
        );          
    });         
    if(typeof text != 'undefined' && text)
    {
        indexPath++;
        $('#pathtable').append('<tr class="path" data-id="'+ indexPath +'"><td class="path-details">' + text + '</td><td class="path-control"><a class="btn btn-danger remove">Supprimer</a></td></tr>'); 
        testFilters['paths'][indexPath] = pathArray;  

        console.log(testFilters);           
    }          
    $('#detailedFilters').change();

    filterSubjects(); //load subjects
});

$("#pathtable").on('click', 'a.remove', function(){
    $(this).parents('tr').remove();        
    delete testFilters['paths'][$(this).parents('tr').attr("data-id")];
    //console.log(testFilters);

    filterSubjects(); //load subjects
});

$("#abandon").change(function(){
    if($('#abandon').is(':checked'))
    {
        testFilters['abandon'] = $('#abandonText').text();   
    }
    else 
    {
        delete testFilters ['abandon'];
    }
});

//set data for test filters
$('a#getFilterTests').bind('click', function(){
    $('#testFilters').fadeOut(0);
    $('#testFilter').show();            
    $.ajax({
        type: 'POST',
        url: $('#get-test-filter-url').html(),
        data : getSubjectSetFilters(),
        dataType: "json",
        success: function(data)
        {
            $('#loading-box-tests').fadeOut(0);
            $('#testFilters').fadeIn();
            setTestFiltersValues(data);              
        },
        error: function(data)
        {
            console.log(data);
        },
    });          
}); 

function setTestFiltersValues(data)
{
    // round values
    data["max_duration"] = Math.ceil(data["max_duration"] * 10)/10;
    data["min_duration"] = parseInt(data["min_duration"] * 10)/10;
    data["max_visited"] = Math.ceil(data["max_visited"] * 10)/10;
    data["min_visited"] = parseInt(data["min_visited"] * 10)/10;
    data["max_avrPerPage"] = Math.ceil(data["max_avrPerPage"] * 10)/10;
    data["min_avrPerPage"] = parseInt(data["min_avrPerPage"] * 10)/10;

    data["average_duration"] = Math.round(data["average_duration"] * 10)/10;
    data["average_visited"] = Math.round(data["average_visited"] * 10)/10;
    data["average_perPage"] = Math.round(data["average_perPage"] * 10)/10;


    //set min/max values for sliders
    $('#duration').attr('data-slider-max',  data["max_duration"]);
    $('#duration').attr('data-slider-min',  data["min_duration"]);

    $('#visited').attr('data-slider-max',  data["max_visited"]);
    $('#visited').attr('data-slider-min',  data["min_visited"]);

    $('#perpage').attr('data-slider-max',  data["max_avrPerPage"]);
    $('#perpage').attr('data-slider-min',  data["min_avrPerPage"]);
    


    $('#avr_time').text(data["average_duration"]+" sec");
    $('#min_duration').text(data["min_duration"]+" sec");
    $('#max_duration').text(data["max_duration"]+" sec");
    $('.range_duration').text(data["min_duration"]+" sec");
    $("#duration").bootstrapSlider('setAttribute', 'max', data["max_duration"]);
    $("#duration").bootstrapSlider('setAttribute', 'min', data["min_duration"]);        
    if(data["min_duration"]== data["max_duration"])                   
        $('#durationSlider').css("width", "1px"); 

    $('#avr_visited').text(data["average_visited"]);
    $('#min_visited').text(data["min_visited"]+" ");
    $('#max_visited').text(data["max_visited"]+" ");
    $('.range_visited').text(data["min_visited"]+" ");
    $("#visited").bootstrapSlider('setAttribute', 'max', data["max_visited"]);
    $("#visited").bootstrapSlider('setAttribute', 'min', data["min_visited"]);
    $("#visited").bootstrapSlider('setAttribute', 'aria-valuenow', 1);
    if(data["min_visited"]== data["max_visited"])                   
        $('#visitedSlider').css("width", "1px"); 

    $('#avr_perpage').text(data["average_perPage"]+" sec");
    $('#min_perpage').text(data["min_avrPerPage"]+" sec");
    $('#max_perpage').text(data["max_avrPerPage"]+" sec");
    $('.range_perpage').text(data["min_avrPerPage"]+" sec");
    $("#perpage").bootstrapSlider('setAttribute', 'max', data["max_avrPerPage"]);
    $("#perpage").bootstrapSlider('setAttribute', 'min', data["min_avrPerPage"]);          
    if(data["min_perPage"]== data["max_perPage"])                   
        $('#perPageSlider').css("width", "1px"); 

    $('#abandonText').text(data["abandons"]);     
}   