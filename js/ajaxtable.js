jQuery(document).ready(function($) {
	//load as default
    $('#dataTable').append('<div id="ajax-spinner"></div>');
	$('#dataTable').load('../table-class-example.php');
	
	$('a.hreforder').live('click', function(){
		var href = $(this).attr('href');

        $('#dataTable').append('<div id="ajax-spinner"></div>');
	    $('#dataTable').load('../'+href);
		
		return false;
		
	});

});