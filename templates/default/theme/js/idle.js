$(document).on('click',function(evt){
	var str = $(evt.target).attr('href');
	//console.log(str);
	
	if(typeof str === 'undefined'){
		str = "";
	}
	
	var index = str.search('logout');
	
	if(index < 0) {
		return true;
	} else {
		$.ajax({
			url : ajax_url,
			type : "POST",
			async : true,
			dataType:"json",
			data : { ajax : 1, m : 'masterservice', act : 'idlelogout', s_id : session_id},
		}).done(function(data){
			console.log(data);
			//alert('itt');
			//$('#idleModal').modal('show');
		});
	}
	
});
$(document).ready(function(){
	//console.log('login '+login);
	//console.log(refresh_url);
	$('.idle-button').click(function(){
		window.location.href = refresh_url;
	})
	function idleLogout() {
		var t;
		window.onload = resetTimer;
		window.onmousemove = resetTimer;
		window.onmousedown = resetTimer; // catches touchscreen presses
		window.onclick = resetTimer;     // catches touchpad clicks
		window.onscroll = resetTimer;    // catches scrolling with arrow keys
		window.onkeypress = resetTimer;

		function logout() {
			//window.location.href = 'logout.php';
			//alert('Kilépés');
			$.ajax({
				url : ajax_url,
				type : "POST",
				async : true,
				dataType:"json",
				data : { ajax : 1, m : 'masterservice', act : 'idlelogout', s_id : session_id},
			}).done(function(data){
				console.log(data);
				//alert('itt');
				$('#idleModal').modal('show');
			});
			
		}

		function resetTimer() {
			clearTimeout(t);
			t = setTimeout(logout, 300000);  // 5 perc inaktivitás
			//console.log(t);
			if(t % 10 == 0){
				$.ajax({
					url : ajax_url,
					type : "POST",
					async : true,
					dataType:"json",
					data : { ajax : 1, m : 'masterservice', act : 'addcheck', s_id : session_id},
				}).done(function(data){
					//console.log(data);
					//alert('itt');
					//$('#idleModal').modal('show');
				});
			}
		}
	}
	if(login === false){
		idleLogout();
	}
});
