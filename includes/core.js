$(document).ready(function() {
	// for future use for character info click
	$('#ch_0, #ch_1, #ch_2, #ch_3, #ch_4, #ch_5, #ch_6, #ch_7, #ch_8').click(function() {
		var x = $(this).attr("id").slice(-1);
	alert(x);
	});
	
	//selection drop down list change
	$("#type_drop").change(function() { 
	var drop = $("#type_drop").val();
	
	if (drop == "Sorcerer"){
		$("#magic_drop").slideDown(500);
	} else {
			$("#magic_drop").slideUp(500);
	}
	if (drop == "Cleric"){
		$("#cleric_drop").slideDown(500);
	} else {
			$("#cleric_drop").slideUp(500);
	}
	 });
	 
// check if character name is set in input field
	$('#char_name').on('input', function() {
	var input=$(this);
	var is_name=input.val();
	if(is_name){input.removeClass("invalid").addClass("valid");}
	else{input.removeClass("valid").addClass("invalid");}
});

// check to see if all character input fields are valid
$("#char_submit button").click(function(event){
	var error_free=true;
	
		var element=$("#char_name");
		var elementType = $("#type_drop");
		
		var valid=element.hasClass("valid");
		var valid2=elementType.val();
		//alert(valid2);
		if (!valid2){

			$('#type_error').removeClass("error_hide").addClass("error_show"); 
			error_free=false;
		} else {
			$("#type_error").removeClass("error_show").addClass("error_hide");
		}

		if (!valid){
			$('#name_error').removeClass("error_hide").addClass("error_show"); 
			error_free=false;
		} else {
			$("#name_error").removeClass("error_show").addClass("error_hide");
		}
	//don't submit form, show errors
	if (error_free == false){
		event.preventDefault(); 
	}
	else{
		//alert('No errors: Form will be submitted');
	}

});
});
	/////background changes
	function background(x) {
			//var x = Math.floor((Math.random() * 5) + 1);
			$('body').css('background-image', 'url(" img/backs/wall_'+x+'.png") ');
		}

// casualties result in these graphics
	function showSkull() {
		$('#skull').fadeIn(.2).fadeOut(5000);
	}
	
	function showFlash() {
		$('#attack_flash').fadeIn(.2).fadeOut(100);
	}
	
	
	
	/*function errorShow() {
		
	$('#name_error').addClass('error_show').removeClass('error_hide');	
	}*/