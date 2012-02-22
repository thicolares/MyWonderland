// set a loading image  
// function ajax_loading_image(div) {  
//     $(div).html('<img src="/r_s_v_p/img/ajax_loading.gif"/>');  
// }  
  
// remove loading image  
function ajax_remove_loading_image(div) {  
    $(div).html('');  
}

function disableAll(){
    $('.voteBtn').click(function(){ 
        alert('asd');
        $(this).attr("disabled", true);
    }
}

function updateGuests(){
    $('.voteBtn').click(function(){  
        $('#continueButton').attr("disabled", true);
        // console.log('addd');
        
        // selected value  
        var selected = $(this).value();  
          
        // ajax  
        $.ajax({  
            type: "POST",  
            url: '/quiz/quizzes/vote',  
            data: "event_id="+selected,  
            success: function(msg){                  
                $('.ajax_guests').html(msg);  
                // remove loading image  
//                ajax_remove_loading_image('.ajax_loading_image');  
            }  
        }); 
    });
}

function init(){
    disableAll();
}

$(function(){ 
	init();
});