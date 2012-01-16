/**
 * Texila for administration
 */
 
//Texyla for content body
$.texyla.setDefaults({
	texyCfg: "admin",
	baseDir: 'texyla',
	previewPath: "preview.php",
	filesPath: "filesplugin/files.php",
	filesThumbPath: "filesplugin/thumbnail.php?image=%var%",
	filesUploadPath: "filesplugin/files/upload.php"
});
		
$(function () {			
	$("#formContentEdit-body").texyla({
		toolbar: [
		'h1', 'h2', 'h3',
		null,
		'bold', 'italic',
		null,
		'center', ['left', 'right', 'justify'],
		null,
		'ul', 'ol', ["olAlphabetSmall", "olAlphabetBig", "olRomans", "olRomansSmall"],
		null,
		'link', 'img', 'table', 'emoticon', 'symbol',
		null,
		'color', 'textTransform',
		null,
		'div', ['html', 'blockquote', 'text', 'comment'],
		null,
		'code',	['codeHtml', 'codeCss', 'codeJs', 'codePhp', 'codeSql'],
		null,
		{type: "label", text: "Ostatné"}, ['sup', 'sub', 'del', 'acronym', 'hr', 'notexy', 'web']
					
		],
		texyCfg: "admin",
		bottomLeftToolbar: ['edit', 'preview', 'htmlPreview'],
		buttonType: "span",
		tabs: true,
		language: "sk"
    });
});
