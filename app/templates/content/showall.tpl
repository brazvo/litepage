{* Smarty *}
{block name="content"}
<div id="main" class="{$class}">
    <div id="pages">
	{foreach from=$contents item=content}
		<div class="content-inner" id="content-{$content['cid']}">
		  <div class="content-body">
		   <div class="main-upper">
			 <h2 class="title">{$content['title']}</h2>
			 {if Application::$edit}
			   {$content['edit']}
			 {/if}
		   </div>
		   <div class="main-lower">
		   {foreach from=$content['contentElements'][$content['cid']] item=element}
		     {$element}
		   {/foreach}
		   </div>
		  </div>
		</div>
	{/foreach}
	</div>
</div>
{literal}
<script type="text/javascript">
$(document).ready(function(){
	$("#mainContent-inner").css({'overflow':'hidden'});

	$("ul.menu-okna_a_dvere a").click(function(){return false;});


	// main vertical scroll
	$("#main").scrollable({

		// basic settings
		vertical: true,

		// up/down keys will always control this scrollable
		keyboard: 'static'

		// assign left/right keys to the actively viewed scrollable
		/*onSeek: function(event, i) {
			horizontal.eq(i).data("scrollable").focus();
		}*/

	// main navigator (thumbnail images)
	});//.navigator("ul.menu-ostatne_produkty");
});
</script>
{/literal}
{/block}