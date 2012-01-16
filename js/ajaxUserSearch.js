$(function(){
	var searchValue = $('#formUserSearch-text').val();
	var oldlength = searchValue.length;
	$(function () {
		setTimeout(checkSearchChanged, 500);
	});

	function checkSearchChanged() {
			searchValue = $('#formUserSearch-text').val();
			if( searchValue.length >= 3 && oldlength != searchValue.length ) {
				oldlength = searchValue.length;
				getUsers( searchValue );
			}
			else {
				oldlength = searchValue.length;
			}
			setTimeout(checkSearchChanged, 500);
	}

    function getUsers( searchValue ) {
		
		$('#formUserSearch-text').ajaxStart(function(){
			$('#formUserSearch-text').addClass('ajax-loader');
		});

		$('#formUserSearch-text').ajaxStop(function(){
			$('#formUserSearch-text').removeClass('ajax-loader');
		});
		
		var url = "/admin/users/list";
		$.ajax(url, {
			data: {text: searchValue, "do": "search"},
			type: 'POST',
			success: function(data){
				var table = $(data).find("#UsersTableContainerInner");
				$("#UsersTableContainerInner").html( table );
			}
		});
	}
	
	// show all
	$('#formUserSearch-reset').click(
		function(){
			var url = "/admin/users/list";
			$.ajax(url, {
				success: function(data){
					var table = $(data).find("#UsersTableContainerInner");
					$("#UsersTableContainerInner").html( table );
				}
			});
			//return false;
		}
	);
});