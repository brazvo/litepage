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
		texyCfg: "admin",
		buttonType: "button",
		language: "sk"
	});

});