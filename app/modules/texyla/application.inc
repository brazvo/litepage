<?php
/**
 * Module Texyla
 * application.inc
 *
 * @author: Branislav Zvolenský
 *
 */
?>
<?php
$textareas = db::fetchAll("SELECT * FROM texyla");

$role = Application::$logged['role'];

$lang = Application::$language['code'];

if($textareas):
?>
<link rel="stylesheet" type="text/css" href="<?=BASEPATH?>/app/modules/texyla/css/jush.css" />
<link rel="stylesheet" type="text/css" href="<?=BASEPATH?>/app/modules/texyla/css/style.css" />

<script type="text/javascript" src="<?=BASEPATH?>/app/modules/texyla/js/1_jush.js"></script>
<script type="text/javascript" src="<?=BASEPATH?>/app/modules/texyla/js/2_texyla.js"></script>
<script type="text/javascript" src="<?=BASEPATH?>/app/modules/texyla/js/ajaxupload.js"></script>
<script type="text/javascript" src="<?=BASEPATH?>/app/modules/texyla/js/buttons.js"></script>
<script type="text/javascript" src="<?=BASEPATH?>/app/modules/texyla/js/dom.js"></script>
<script type="text/javascript" src="<?=BASEPATH?>/app/modules/texyla/js/selection.js"></script>
<script type="text/javascript" src="<?=BASEPATH?>/app/modules/texyla/js/texy.js"></script>
<script type="text/javascript" src="<?=BASEPATH?>/app/modules/texyla/js/view.js"></script>

<?php if(@is_file(APP_DIR."/modules/texyla/languages/$lang.js")): ?>
<script type="text/javascript" src="<?=BASEPATH?>/app/modules/texyla/languages/<?=$lang?>.js"></script>
<?php else: ?>
<script type="text/javascript" src="<?=BASEPATH?>/app/modules/texyla/languages/sk.js"></script>
<?php endif; ?>
<script type="text/javascript" src="<?=BASEPATH?>/app/modules/texyla/plugins/keys/keys.js"></script>
<script type="text/javascript" src="<?=BASEPATH?>/app/modules/texyla/plugins/window/window.js"></script>
<script type="text/javascript" src="<?=BASEPATH?>/app/modules/texyla/plugins/resizableTextarea/resizableTextarea.js"></script>
<script type="text/javascript" src="<?=BASEPATH?>/app/modules/texyla/plugins/img/img.js"></script>
<script type="text/javascript" src="<?=BASEPATH?>/app/modules/texyla/plugins/table/table.js"></script>
<script type="text/javascript" src="<?=BASEPATH?>/app/modules/texyla/plugins/link/link.js"></script>
<script type="text/javascript" src="<?=BASEPATH?>/app/modules/texyla/plugins/emoticon/emoticon.js"></script>
<script type="text/javascript" src="<?=BASEPATH?>/app/modules/texyla/plugins/symbol/symbol.js"></script>
<script type="text/javascript" src="<?=BASEPATH?>/app/modules/texyla/plugins/files/files.js"></script>
<script type="text/javascript" src="<?=BASEPATH?>/app/modules/texyla/plugins/color/color.js"></script>
<script type="text/javascript" src="<?=BASEPATH?>/app/modules/texyla/plugins/textTransform/textTransform.js"></script>
<script type="text/javascript" src="<?=BASEPATH?>/app/modules/texyla/plugins/youtube/youtube.js"></script>
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
<?php endif; ?>