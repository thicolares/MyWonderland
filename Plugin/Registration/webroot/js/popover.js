// set a loading image  
function popover(){
//    alert('Escolha!');
    $('#element').ready(function(){
        $(this).popover('show');
    });

}


function init(){
    popover();
}

$(function(){ 
	init();
});



