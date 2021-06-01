<script>

$(document).ready(function(){
		
		$('.error').remove();
		
		$('.tag-insert').click(function(){
			
			var insertText = $(this).data('tag');
			
			var selection = document.getSelection();
			var cursorPos = selection.anchorOffset;
			var oldContent = selection.anchorNode.nodeValue;
			if(!oldContent.trim()) {
				var newContent = '[!{'+insertText+'}!]';
			} else {
				//var toInsert = "InsertMe!";
				var toInsert = '[!{'+insertText+'}!]';
				var newContent = oldContent.substring(0, cursorPos) + toInsert + oldContent.substring(cursorPos);
				
			}
			console.log(newContent);
			selection.anchorNode.nodeValue = newContent;
		});
		
		$('#email_contents-form-cancel').click(function(){
			var cancel_href = $(this).data('cancel-href');
			
			var id = $(this).data('id');
			
			//unlock(id);
			
			//console.log(cancel_href);
			window.location.href = cancel_href;
			return false;
		});
	
		$('#email_contents-form-submit').click(function(){
			
			ret = true;
			
			if($('input[name="email_contents-label"]').val() == "") {
				$('input[name="email_contents-label"]').after('<div class="error">A Címke nem lehet üres!</div>');
				ret = false;
			}
			
			if($('input[name="email_contents-hook"]').length > 0 && $('input[name="email_contents-hook"]').val() == "") {
				$('input[name="email_contents-hook"]').after('<div class="error">A Hook nem lehet üres!</div>');
				ret = false;
			}
			
			if($('#editor-one').html() == "") {
				$('.email_contents-content').after('<div class="error">A Szöveg nem lehet üres!</div>');
				ret = false;
			} else {
				$('textarea[name="email_contents-content"]').val($('#editor-one').html());
			}
			
			console.log(ret);

			if(ret === true){
				$('#email_contents-form').submit();
			}	
			
			return false;
			
		});
		
		$('#email_contents-form').find('input').focus(function(){
			$(this).next('.error').remove();
		});
		
		$('#editor-one').focus(function(){
			$(this).next('.error').remove();
		});
		
		
		
});

function unlock(id) {
	$.ajax({
		url: ajax_url,
		type : "POST",
		async : true,
		dataType:"json",
		data : { ajax : 1, m : 'masterservice', act : 'client_unlock', id : id, s_id : session_id },
		success:function(data){
			console.log(data);			
		}
	});
}

</script>
