// set a loading image  
function registrationPeriodDatetime(){
    
    $('#RegistrationPeriodBeginDateDate').datepicker({
        dateFormat: 'dd/mm/yy',
    	onSelect: function (selectedDateTime){
    		var start = $(this).datepicker('getDate');
    		$('#RegistrationPeriodEndDateDate').datepicker('option', 'minDate', new Date(start.getTime()) );
    	}
    });

    $('#RegistrationPeriodEndDateDate').datepicker({
        dateFormat: 'dd/mm/yy',
    	onSelect: function (selectedDateTime){
    		var end = $(this).datepicker('getDate');
    		$('#RegistrationPeriodBeginDateDate').datepicker('option', 'maxDate', new Date(end.getTime()) );
    	}
    });    
}

function init(){
    registrationPeriodDatetime();
}

$(function(){ 
	init();
});



