/**
 * Manage main template
 */
$(document).ready(function(){
    $('.alerts-container').delay(4000).fadeOut();

    hidingPanelStart();
});


function hidingPanelStart(){
    $('.panel-content-hiding').each(function(){
        if($(this).data('default-hide') != null){
           elem = $(this).find('.panel-body');
           animateHeight(elem, 0, 0);
           $(this).find('.btn-reduce').html('<i class="fa fa-plus-square-o" aria-hidden="true"></i> Ouvrir');
        }
    });

    $('.panel-content-hiding').find('.btn-reduce').click(function(){
        var elemBody = $(this).parent().parent().find('.panel-body');

        if(elemBody.height() == 0){

            elemBody.height('auto');
            var orginialHeight = elemBody.height();
            elemBody.height(0);

            animateHeight(elemBody, orginialHeight);
            $(this).html('<i class="fa fa-minus-square-o" aria-hidden="true"></i> Réduire');
        }
        else{
            animateHeight(elemBody, 0);
            $(this).html('<i class="fa fa-plus-square-o" aria-hidden="true"></i> Ouvrir');
        }
        
    });
}


function animateHeight(elem, newHeight, speed=500){
    elem.animate({
        height: newHeight
    }, speed);
}


/**
 * Show obstructive loading element
 */
function showLoading(){   
    $('#loading-overlay').css('display', 'block');
}

/**
 * Hide obstructive loading element
 */
function hideLoading(){
    $('#loading-overlay').css('display', 'none');
}