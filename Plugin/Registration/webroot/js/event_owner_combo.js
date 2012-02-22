
// set a loading image  
function ajax_loading_image(div) {  
    $(div).html('<img src="/r_s_v_p/img/ajax_loading.gif"/>');  
}  
  
// remove loading image  
function ajax_remove_loading_image(div) {  
    $(div).html('');  
}

function eventCombo(){
    $('#UserRoleId').change(function(){  
        // selected value  
        var selected = $(this).val();  
        if(selected == 3){
            //set loading image  
            ajax_loading_image('.ajax_loading_image');  

            $.ajax({  
                type: "POST",  
                url: '/event/events/combo',  
                data: "event_id="+selected,  
                success: function(msg){                  
                    $('.ajax_guests').html(msg);  
                    // remove loading image  
                    ajax_remove_loading_image('.ajax_loading_image');  
                }  
            });
        } else {
            alert('nope');
        }
        

    });
}

function init(){
    ajax_loading_image();
    ajax_remove_loading_image();
    eventCombo();
}

$(function(){ 
	init();
});