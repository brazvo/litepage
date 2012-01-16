jQuery(document).ready(function($) {

	$('.menu-item-313 a').live('click',
		function(){
			
			$('.menu-item-313').ajaxStart(
				function(){
					$(this).append(sAjax);
				}
			);
				
			$('.menu-item-313').ajaxStop(
				function(){
					$(sAjax).remove();
				}
			);
			if( $('body').find('#ajaxLoginForm').length == 0 ) {
				var url = $(this).attr('href');
				var wrap = document.createElement('div');
				$(wrap).attr('id', 'ajaxLoginForm');

				$(wrap).load(url+"?isajax=1 #formLogin",
					function(){
						$("#container").append(wrap);
					}
				);
			}
			
			return false;
		}
	);
		
	$('#ajaxLoginForm #frm-content-1').live('click', function(){$('#ajaxLoginForm').remove();});
	
	$('#formLogin-login').live('click',
		function(){
			var usr = $("#formLogin-user").val();
			var pwd = $("#formLogin-password").val();
			var url =$("#formLogin").attr('action');
			var st_logged = $("#formLogin-stay_logged").is(":checked") ? 1 : null;
			var par = $(this).parent(".frm-control");

			if( validateLoginForm(usr, pwd) == false ) {
				return false;
			}
			
			$(par).ajaxStart(
				function(){
					$(this).append(sAjax);
				}
			);
				
			$(par).ajaxStop(
				function(){
					$(sAjax).remove();
				}
			);
			
			$.ajax({
				url: url+"Ajax",
				data: {user: usr, password: pwd, stay_logged: st_logged},
				type: 'POST',
				dataType: 'JSON',
				success: function(data){
					if(data.result == 1) {
						self.location = self.location.href;
					}
					else {
						alert("Zadali ste zlé VID alebo heslo. / You put wrong VID or password.");
					}
				},
				error: function(){
					$("#ajaxLoginForm").remove();
					alert("Počas prihlasovania došlo k chybe.");
				}
			});
			
			//$("body").load(url+" #body-inner", {'user':user, 'password':pwd, 'stay_logged':stay_logged});
			return false;
		}
	);
		
	function validateLoginForm(user, password) {

		if( user.length == 0 ) {
			alert("VID musí byť vyplnené / VID must be filled");
			return false;
		}
		else if( password.length == 0 ) {
			alert("Heslo musí byť vyplnené / Password must be filled");
			return false;
		}
		else {
			return true;
		}
	}
	
	$("a.ivaoDetail").click(
		function(){
			
			var url = $(this).attr('href');
			
			$.ajax({
				url: url,
				context: document.body,
				success: function(data) {
					//alert(data);
				},
				error: function(jqXHR, text, statement){
					//alert(jqXHR.getResponseHeader("Location")+", "+text+": "+statement);
					$.ajax({
						url: "http://www.ivao.aero/login.asp",
						data: {Id: "282561", Pwd: "J6n507tJjlSJ"},
						type: 'POST',
						success: function(){alert("Prihlaseny")},
						error: function(){alert("Neviem co")}
					});
				}
			});
			
			return false;
		}
	);

});