/**
 * 19:56 23td apríl 2010
 *
 * general.js
 *
 * by Branislav Zvolensky
 */

// jQuery code
var sAjax = document.createElement('span');
var sAjaxImg = document.createElement('img');
jQuery(document).ready(function($){
   // Error and Flash Messages
   $('div.flash-errors').delay(5000).fadeOut('slow');
   $('div.flash-messages').delay(5000).fadeOut('slow');
   
   // DELETE Confirmation
   $('a.delete').click(function(){
     var redirect = $(this).attr('href');
	 if(confirm('Ste si istý? Táto operácia je nezvratná!')){
	    self.location = redirect;
	 }
	 else{
	    return false;
	 }
   });
   
   // small ajax loader
   $(sAjax).attr('id', 'smallAjax');
   $(sAjaxImg).attr('src', 'images/ajax-loader-small.gif');
   $(sAjax).append( sAjaxImg );
   
   /*
    * Following code set the same height to div #sidebar1 and div #mainContent
	* Uncomment if you want it applied
	*/
	/*
	var sidebarLeftHeight = $('#sidebar1').height();
	var sidebarRightHeight = $('#sidebar2').height();
	var contentHeight = $('#mainContent').height();
	if(contentHeight > sidebarLeftHeight){
	  $('#sidebar1').height(contentHeight);
	}
	if(contentHeight > sidebarRightHeight){
	  $('#sidebar2').height(contentHeight);
	}
	*/
   
   
   //Gallery page hacks Fancybox
   $("a.gallery_elements").fancybox();
   
   // loadForm Check
   /*var mrgTop = Math.round(($(window).height() - $("#loader").height()) / 2) + 'px';
   var mrgLeft = Math.round(($(window).width() - $("#loader").width()) / 2) + 'px';
   $("#loader").css({"left":mrgLeft, "top":mrgTop});*/
   
   /*
   $("form").submit(function(){
   
   		//$("#loader").show();
		//$(".frm-submit").attr('disabled', 'disabled');
		//$("#buttonUpload").attr('disabled', 'disabled');
   
   });
   */
   
   // Date picker
   $(function() {
		$("input[name=date]").datepicker();
   });
   
   // round corners
   $("#mainContent-inner").corner("5px");
   $(".dialog-wrapper").corner("5px");
   $('#footer-inner').corner("bottom 10px");
   $('ul.menu').corner("bottom 5px");
   $('#sidebar2 .online-wrapper').corner("bottom 5px");
   
});