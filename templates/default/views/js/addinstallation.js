<script>
//js for addorder.view.php

$(document).delegate("#installation-remove-item","click",function(){
	
	$('.item-error').remove();
	
	var item_count = parseInt($('input[name="installation-items-count"]').val());
	
	$('#item-row-'+item_count).remove();
	
	item_count = item_count - 1;
	
	if(item_count < 0) {
		item_count = 0;
	}
	
	if(item_count == 0) {
		$('.installation-remove-item-container').hide();
	} else {
		$('.installation-remove-item-container').show();
	}
	
	$('#installation-remove-item-container-'+item_count).show();
	
	$('input[name="installation-items-count"]').val(item_count);
});

$(document).ready(function(){
	//$('#user-form').submit(function(){
	$('#installation-form-submit').click(function(){

		$('.error').remove();
		
		var ret = true;
		
		if($('input[name="installation-cat-name"]').val() == "") {
			$('input[name="installation-cat-name"]').after('<div class="error">A név nem lehet üres!</div>');
			ret = false;
		} else if($('input[name="installation-cat-name"]').val() !== "") {

			// Egyediség vizsgálata ajax-szal
			
			$.ajax({
				url: ajax_url,
				type : "POST",
				async : false,
				dataType:"json",
				data : { ajax : 1, m : 'masterservice', act : 'checkunique_installation', fieldname : 'CategoryName', value : $('input[name="installation-cat-name"]').val(), s_id : session_id },
				success:function(data){
					if(data['status'] == 'true') {
						$('input[name="installation-cat-name"]').after('<div class="error">A kategória már létezik a rendszerben!</div>');
						ret = false;
					} else {
						ret = true;
					}
					//$('.page-loader-wrapper').hide();
				}
			/*}).done(function(data){
				if(data['status'] === 'true') {
					alert('Status '+data['status']);
					$('input[name="installation-name"]').after('<div class="error">A felhasználónév már létezik a rendszerben!</div>');
					ret = false;
				}*/
			});
			
		}
		
		if($('input[name="installation-cost"]').val() == "") {
			$('input[name="installation-cost"]').after('<div class="error">A díjszabás nem lehet üres!</div>');
			ret = false;
		}
		
		// Check first password
		/*if($('input[name="pwd-one"]').val() == "") {

			$('input[name="pwd-one"]').after('<div class="error">A jelszó nem lehet üres</div>');
			ret = false;
		} else {
			var md5_passw = /^(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{6,20}$/;
			var passw = $('input[name="pwd-one"]').val();
			if(!passw.match(md5_passw)) {

				$('input[name="pwd-one"]').after('<div class="error">Hibás jelszó formátum (Legyen benne szám és legalább egy nagybetű!)</div>');
				ret = false;
			} else {
				first_pwd = true;
			}	
		}*/
		// Check secound password
		/*if($('input[name="pwd-true"]').val() == "") {

			$('input[name="pwd-true"]').after('<div class="error">A jelszó nem lehet üres</div>');
			ret = false;
		} else {
			var md5_passw = /^(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{6,20}$/;
			var passw_two = $('input[name="pwd-true"]').val();
			if(!passw_two.match(md5_passw)) {

				$('input[name="pwd-true"]').after('<div class="error">Hibás jelszó formátum (Legyen benne szám és legalább egy nagybetű!)</div>');
				ret = false;
			} 
			// Compare passwords
			if(passw != passw_two) {
				//$('input[name="master_password_secound"]').css('border-color','#da4453');
				$('input[name="pwd-true"]').after('<div class="error">A két jelszó nem egyezik</div>');
				ret = false;
			}
		}*/
		
		
		/*if($('input[name="installation-cartype"]').val() == "") {
			$('input[name="installation-cartype"]').after('<div class="error">A gépjármű típusa nem lehet üres!</div>');
			ret = false
		}*/
		
		/*if($('input[name="installation-lpnumber"]').val() == "") {
			$('input[name="installation-lpnumber"]').after('<div class="error">A gépjármű rendszáma nem lehet üres!</div>');
			ret = false
		}*/
		
		/*if($('input[name="installation-city"]').val() == "") {
			$('input[name="installation-city"]').after('<div class="error">A helység nem lehet üres!</div>');
			ret = false
		}
		
		if($('input[name="installation-address"]').val() == "") {
			$('input[name="installation-address"]').after('<div class="error">A cím nem lehet üres!</div>');
			ret = false
		}
		
		if($('input[name="installation-phonenum"]').val() == "") {
			//$('input[name="subcontactor-phonenum"]').css('border-color','#da4453');
			$('input[name="installation-phonenum"]').after('<div class="error">Adjon meg telefonszámot!</div>');
			ret = false;
		} else {
			var phonenum_match = /^\d{11}$/;
			var phonenum = $('input[name="installation-phonenum"]').val();
			if(!phonenum.match(phonenum_match)) {
				//$('input[name="subcontactor-phonenum"]').css('border-color','#da4453');
				$('input[name="installation-phonenum"]').after('<div class="error">Nem megfelelő formátum!</div>');
				ret = false;
			}
		}
		
		if($('input[name="installation-email"]').val() == "") {
			$('input[name="installation-email"]').after('<div class="error">Az e-mail nem lehet üres!</div>');
			ret = false;
		} else {
			var email_match = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
			var email = $('input[name="installation-email"]').val();
			if(!email.match(email_match)) {
				//$('input[name="subcontactor-phonenum"]').css('border-color','#da4453');
				$('input[name="installation-email"]').after('<div class="error">Nem megfelelő formátum!</div>');
				ret = false;
			}
		}*/
		
		console.log(ret);

		if(ret === true){
			$('#installation-form').submit();
		}	
	});
	
	$('.edit-form').find('input').focus(function(){
		$(this).next('.error').remove();
	});
	
	var $loading = $('.page-loader-wrapper');
	$(document)
		.ajaxStart(function () {
		$loading.show();
	})
		.ajaxStop(function () {
		$loading.hide();
	});
	
	$("#cities-list").change(function(){
		$("#order-city").val($(this).val());
	});
	
	
	
	$("#installation-add-item").click(function(){
		
		$('.item-error').remove();
		
		var item_count = parseInt($('input[name="installation-items-count"]').val())+1;
		
		/*if($('input[name="installation-items-count"]').val() > 0 && ($('#installation-cat-name-'+(item_count-1)).val() == '' || $('#installation-cat-cost-'+(item_count-1)).val() == '')) {
			$(this).after('<div class="item-error">A név vagy a díjazás nem lehet üres!</div>');
			return false;
		}*/
		
		if($('input[name="installation-items-count"]').val() > 0) {
			if($('#installation-cat-name-'+(item_count-1)).val() == '' || $('#installation-cat-cost-'+(item_count-1)).val() == '') {
				$(this).after('<div class="item-error">A név vagy a díjazás nem lehet üres!</div>');
				return false;
			}/* else if($('#installation-cat-cost-'+(item_count-1)).val() != '') {
				var cost_match = /^\d+$/;
				var cost = $('#installation-cat-cost-'+(item_count-1)).val();
				if(!cost.match(cost_match)) {
					$(this).after('<div class="item-error">A díjazás mező csak számot tartalmazhat!</div>');
					return false;
				}
			}*/
		}
		
		console.log(item_count);
		
		if(item_count >= 1) {
			$('.installation-remove-item-container').show();
		} else {
			$('.installation-remove-item-container').hide();
		}
		
		if(item_count == 1) {
			var template =  '<div class="form-group form-float">'+
							'<div class="col-md-3 col-sm-3 col-xs-12"></div>'+
							'<div class="col-md-3 col-sm-3 col-xs-12">Megnevezés</div>'+
							'<div class="col-md-3 col-sm-3 col-xs-12">Időtartam</div>'+
							//'<div class="col-md-3 col-sm-3 col-xs-12">Díjazás</div>'+
						'</div>'+
						'<div id="item-row-'+item_count+'" class="form-group form-float">'+
							' <div class="col-md-3 col-sm-3 col-xs-12"></div>'+
							'<div class="col-md-3 col-sm-3 col-xs-12">'+
								'<input type="text" class="form-control item-name" id="installation-cat-name-'+item_count+'" name="installation-cat-item-name['+item_count+']" required>'+
							'</div>'+
							'<div class="col-md-3 col-sm-3 col-xs-12">'+
							'<select name="installation-cat-item-req_time['+item_count+']" class="form-control">'+
								'<option value="0">Válasszon</option>'+
								'<option value="1">1 óra</option>'+
								'<option value="2">2 óra</option>'+
								'<option value="4">4 óra</option>'+
								'<option value="8">Teljes munkanap (8 óra)</option>'+
							'</select>'+
						'</div>'
							//'<div class="col-md-3 col-sm-3 col-xs-12">'+
							//	'<input type="text" class="form-control item-cost" id="installation-cat-cost-'+item_count+'" name="installation-cat-item-cost['+item_count+']" required>'+
							//'</div>'+
						'</div>';
		} else {
			var template =  '<div id="item-row-'+item_count+'" class="form-group form-float">'+
								'<div class="col-md-3 col-sm-3 col-xs-12"></div>'+
								'<div class="col-md-3 col-sm-3 col-xs-12">'+
									'<input type="text" class="form-control item-name" id="installation-cat-name-'+item_count+'" name="installation-cat-item-name['+item_count+']" required>'+
								'</div>'+
								//'<div class="col-md-3 col-sm-3 col-xs-12">'+
								//	'<input type="text" class="form-control item-cost" id="installation-cat-cost-'+item_count+'" name="installation-cat-item-cost['+item_count+']" required>'+
								//'</div>'+
							'</div>';
		}
		
		
		
		$('#installations-item-list').append(template);
		
		$('input[name="installation-items-count"]').val(item_count);
	});
	
	
	
});
</script>
