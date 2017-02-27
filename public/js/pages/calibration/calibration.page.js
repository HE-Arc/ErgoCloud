/**
 * Main view JS file
 */


$(document).ready(function(){
    if($("#perfect").val() == 1)
    {               
        $("a#btnPerfect").text("Ajouter").data("action", "Add");
    }
    else
    {
        $("a#btnPerfect").text("Eliminer").data("action", "Eliminate");
    }
    if($("#good").val() == 1 )
    {
        $("a#btnGood").text("Ajouter").data("action", "Add");
    }
    else 
    {                
        $("a#btnGood").text("Eliminer").data("action", "Eliminate");
    }          
    if($("#moderate").val() == 1)
    {
        $("a#btnModerate").text("Ajouter").data("action", "Add");
    }
    else 
    {                
        $("a#btnModerate").text("Eliminer").data("action", "Eliminate");
    }            
    if($("#poor").val() == 1)
    {
        $("a#btnPoor").text("Ajouter").data("action", "Add");            
    }
    else 
    {                   
        $("a#btnPoor").text("Eliminer").data("action", "Eliminate");
    }
    
    $("tbody a").bind('click', function(){              
        var type;
        var state;
        var testId = $('#test-infos-id').html();   
        if(this.id == "btnPerfect")
        {
            type = "perfect";
            state = $("a#btnPerfect").data("action");
        }
        if(this.id == "btnGood")
        {
            type = "good";                    
            state=$("a#btnGood").data("action");     
        }
        if(this.id == "btnModerate")
        {
            type = "moderate";
            state = $("a#btnModerate").data("action");                  
        }
        if(this.id == "btnPoor")
        {
            type = "poor";
            state = $("a#btnPoor").data("action");
        }  
      
        $.ajax({
            type : 'POST',
            url : $('#calibration-state-url').html(),
            data : {
                type: type,
                state: state,
                id: testId,
            },
            dataType : "json",
            success: function(data)
            {
                console.log(data["success"]);  
                window.location.href = $('#calibration-test-url').html();
            },
            error: function(data)
            {
                console.log("error");
                alert("There was an error ! Please contact the admin.");
            },
        });
        
    });           
});
        