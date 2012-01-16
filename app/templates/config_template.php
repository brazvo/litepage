<?php
/**
 * Project LitePage
 * 
 * Configuration for 1-2-1-left-fix template
 *
 * @author Branislav Zvolensky <zvolensky@mbmartworks.sk>
 */

/* Header elements */

// Application::setStylesheet($path, $ie, $media);
Application::setStylesheet('/fancybox/jquery.fancybox-1.3.1.css');
Application::setStylesheet('/css/ui-lightness/jquery-ui-1.8.2.custom.css');
Application::setStylesheet('/css/general.css');
Application::setStylesheet('/css/1-2-1-left-fix.css');
Application::setStylesheet('/css/ie.css', true);
Application::setStylesheet('/fancybox/jquery.fancybox-1.3.1-ie.css', true);

// Application::setJavascript($path);
//Application::setJavascript('/js/globals.js.php');
Application::setJavascript('/js/external.js');
Application::setJavascript('/js/jquery.tools.min.js');
Application::setJavascript('/js/ajaxfileupload.js');
Application::setJavascript('/fancybox/jquery.fancybox-1.3.1.pack.js');
Application::setJavascript('/fancybox/jquery.easing-1.3.pack.js');
Application::setJavascript('/fancybox/jquery.mousewheel-3.0.2.pack.js');
Application::setJavascript('/js/jquery-ui-1.8.2.custom.min.js');
Application::setJavascript('/js/jquery.ui.datepicker-sk.js');
Application::setJavascript('/js/general.js');

// Application::setFavicon($path);
Application::setFavicon('/images/favicon.gif');

// Application::setMeta($attributes)

?>