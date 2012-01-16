<div class="closer">X</div>
<div class="block-inner">
<div class="block-content">
<?=Application::getQueries()?>
</div>
</div>
<script type="text/javascript">
jQuery(document).ready(function($){
	$("#sql_browser .closer").click(function(){
		$("#sql_browser").fadeOut(400);
	});
});
</script>