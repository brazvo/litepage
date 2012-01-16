<script type="text/javascript">
jQuery(document).ready(function($){

	$.texyla.setDefaults({
		texyCfg: "admin",
		baseDir: '<?=BASEPATH?>/app/modules/texyla',
		previewPath: "<?=BASEPATH?>/app/modules/texyla/preview.php",
		filesPath: "filesplugin/files.php",
		filesThumbPath: "filesplugin/thumbnail.php?image=%var%",
		filesUploadPath: "filesplugin/files/upload.php"
	});
        
<?php
	foreach($textareas as $textarea):
	$ta_set = db::fetch("SELECT * FROM texyla_settings WHERE role='$role' AND texyla_id=".$textarea['id']);
	if($ta_set['allow']):
        
	?>
	$(function () {			
		$("<?=$textarea['textarea']?>").texyla({
			toolbar: [
			<?if($ta_set['headers']):?>
			'h1', 'h2', 'h3',
			null,
			<?endif;?>
			<?if($ta_set['font_style']):?>
			'bold', 'italic',
			null,
			<?endif;?>
			<?if($ta_set['text_align']):?>
			'center', ['left', 'right', 'justify'],
			null,
			<?endif;?>
			<?if($ta_set['lists']):?>
			'ul', 'ol', ["olAlphabetSmall", "olAlphabetBig", "olRomans", "olRomansSmall"],
			null,
			<?endif;?>
			<?if($ta_set['link']):?>'link', <?endif;?><?if($ta_set['img']):?>'img', <?endif;?><?if($ta_set['table']):?>'table', <?endif;?><?if($ta_set['emoticon']):?>'emoticon', <?endif;?> <?if($ta_set['symbol']):?>'symbol',<?endif;?>
			<?if($ta_set['font_style'] or $ta_set['img'] or $ta_set['table'] or $ta_set['emoticon'] or $ta_set['symbol']):?>null,<?endif;?>
			<?if($ta_set['color']):?>'color',<?endif;?> <?if($ta_set['textTransform']):?>'textTransform',<?endif;?>
			<?if($ta_set['color'] or $ta_set['textTransform']):?>null,<?endif;?>
			<?if($ta_set['blocks']):?>
			'div', ['html', 'blockquote', 'text', 'comment'],
			null,
			<?endif;?>
			<?if($ta_set['codes']):?>
			'code',	['codeHtml', 'codeCss', 'codeJs', 'codePhp', 'codeSql'],
			null,
			<?endif;?>
			<?if($ta_set['others']):?>
			{type: "label", text: "<?=OTHERS?>"}, ['sup', 'sub', 'del', 'acronym', 'hr', 'notexy', 'web']
			<?endif;?>			
			],
			texyCfg: "<?=$ta_set['texyCfg']?>",
			bottomLeftToolbar: ['edit', 'preview', 'htmlPreview'],
			buttonType: "<?=$ta_set['buttonType']?>",
			<?if($ta_set['tabs']):?>tabs: true,<?else:?>tabs: false,<?endif;?>
			<?if(@is_file(APP_DIR."/modules/texyla/languages/$lang.js")):?>
			language: "<?=$lang?>"
			<?else:?>
			language: "sk"
			<?endif;?>
		});
	});	
	<?php
	endif;
	endforeach;
?>
});
</script>