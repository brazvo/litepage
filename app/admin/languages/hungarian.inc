<?php
/************* Administration (Hungarian) *************/

/*** CONTENT CONTROLLER ***/
define('NOTHING_TO_EDIT', 'Nothing to edit!');
define('FILE_WAS_UPLOADED_AND_CHANGES_WERE_SAVED', 'File was uploaded and changes were saved.');
define('CHANGES_WERE_SAVED', 'Changes saved.');
define('CONTENT_WAS_SAVED', 'Content saved.');
define('NOTHING_TO_DELETE', 'Nothing to delete!');
define('CONTENT', 'Content');
define('CONTENT_LIST', 'Content list');
define('NO_CONTENT_YET', 'No content is in database.');
define('NO_CONTENT', 'No content.');
define('ADD_NEW_CONTENT', 'Add new content');
define('NEW_CONTENT', 'New content');

//edit form
define('MENU_TITLE_LABEL', 'Title in menu');
define('MENU_TITLE_DESC', 'Put title that will appear as menu item. Leave blank if you do not want this as menu item.');
define('MENU_ITEMS_LABEL', 'Parent item');
define('MENU_ITEMS_DESC', 'Choose or change parent item.');
define('MENU_SETTINGS', 'Menu settings');

//LIST
define('LIST_CONT_TITLE','Name');
define('LIST_CONT_TYPE','Type');
define('LIST_LAST_UPD','Last update');
define('LIST_LANG_ID','Lang');
define('LIST_CREATED_BY','Created by');

// FILTER
define('FILTER_CHOOSE_CONT_TYPE','Select Content Type');
define('FILTER_CHOOSE_CATEGORY','Select Category');
define('FILTER_CHOOSE_LANGUAGE','Select Language');
define('FILTER_WITHOUT_LANGUAGE','Without language');

// PAGINATOR
define('PAGINATOR_PAGE','Page');
define('PAGINATOR_FIRST','first');
define('PAGINATOR_LAST','last');

/************* LANGUAGES ****************/
define('LANG_ADMIN_TITLE','Languages administration');
define('LANG_ADMIN_ADD_TITLE','Add new language');
define('LANG_ADMIN_EDIT_TITLE','Language editation');
define('LANG_ADMIN_SET_DEFAULT','Set as default');

define('LANG_FRM_DESC_LANGID','A language code (see ISO codes of languages), that will be put into URL, if alternative language mutation will be selected.');
define('LANG_FRM_DESC_MACH_NAME','Machine name of the language without spaces and non-standard characters. When language mutation will be loaded the system will be looking for files in <i>languages</i> directories which consist of the machine name and file extension <i>inc</i>. Example: english = english.inc');
define('LANG_FRM_DESC_URL_ALIAS','Put main page of the language mutation, which appears after request of www.vasa_stranka.sk or www.vasa_stranka.sk/en if this mutation is not set as default. You can put path alias or link to content such as <i>content/show/%id</i>');
define('LANG_FRM_RULE_NAME','Name is required');
define('LANG_FRM_RULE_LANGID','Code is required');
define('LANG_FRM_RULE_MACH_NAME', 'Vyplňte položku Strojové meno');

define('LANG_ADMIN_ERR_ONE','Language with this code is not in database.');
define('LANG_ADMIN_ERR_TWO','Deleting was not executed.');
define('LANG_ADMIN_ERR_THREE','Cannot deactivate default language.');

/************** MENUS ******************/
define ('MENUS_ADMIN_TITLE', 'Menu administration');
define ('MENUS_ADMIN_ADD_TITLE', 'Add new menu');
define ('MENUS_ADMIN_EDIT_TITLE', 'Edit menu');
define ('MENUS_ADMIN_EDIT_ITEMS_TITLE', 'Edit menu item');
define ('MENUS_ADMIN_ADD_LINK', 'Add menu');

define ('MENUS_FRM_RULE_NAME', 'Enter the name of the menu');
define ('MENUS_FRM_DESC_MACH_NAME', 'Machine name used to invoke the menu by an office block <b>: getMenu ("machine_name") </b>. Machine name must not contain illegal characters and accents such as + - / | \ space š č ť etc. If you leave this field blank, machine name is created automatically adjusting the name of the menu.');

/************** MENU iTEMS ****************/
define('MENUITEMS_ADMIN_TITLE','Editation of Menu Items');
define('MENUITEMS_ADMIN_EDIT_TITLE','Editation of Menu Item');
define('MENUITEMS_ADMIN_ADD_TITLE','New Menu Item');
define('MENUITEMS_ADMIN_DELETE_TITLE','Removing a Menu Item');

/**************** MODULES *****************/
define('MODULES_ADMIN_DEFAULT_TITLE','List of Installed Modules');
define('MODULES_ADMIN_LIST_TITLE','List of Modules');

define('MODULES_ADMIN_ERR_DEFAULT','No modules are installed.');
define('MODULES_ADMIN_ERR_LIST','You have no permission to see this page.');

/**************** USERS *******************/
define('USERS_ADMIN_USERS_PERMISIONS_LNK', 'User permissions editation');
define('USERS_ADMIN_USERS_PERMISIONS_SAVE_OK', 'Permissions have been saved');
define('USERS_ADMIN_USERS_PERMISIONS_SAVE_FAILED', 'User permissions saving failed');
define('USERS_ADMIN_FRM_ADD_USERS_PERMISIONS_MACHINE_NAME', 'Choose a module or content type');
define('USERS_ADMIN_FRM_ADD_USERS_PERMISIONS_VIEW', 'View');
define('USERS_ADMIN_FRM_ADD_USERS_PERMISIONS_ADD', 'Adding');
define('USERS_ADMIN_FRM_ADD_USERS_PERMISIONS_EDIT', 'Editing');
define('USERS_ADMIN_FRM_ADD_USERS_PERMISIONS_DELETE', 'Deleting');

/**************** PERMISSIONS **************/
define('PERMISSIONS_ADMIN_TITLE', 'Permissions');
define('PERMISSIONS_ADMIN_NO_PERMISSIONS', 'You do not have any permission to view this page');

/*************** DEVELOPMENT **************/
define('DEV_ADMIN_DEFAULT_TITLE', 'Development Tools and Performance');
define('DEV_ADMIN_DEFAULT_ERROR', 'You do not have permission to change these settings');
define('FRM_DEV_CACHE_PAGES_TITLE', 'Enable page chaching');
define('FRM_DEV_CACHE_PAGES_DESC', 'We recomend to disable the page chaching in development');
define('FRM_DEV_CACHE_CSS_TITLE', 'Enable CSS chaching');
define('FRM_DEV_CACHE_CSS_DESC', 'Enable the CSS caching in production. It is prevence of overloading of max. number of CSS files in IE.');
define('FRM_DEV_CACHE_JS_TITLE', 'Enable JavaScript chaching');
define('FRM_DEV_CACHE_JS_DESC', 'Enable the JavaScript caching in production. It is prevence of overloading of max. number of JavaScript files in IE.');
define('FRM_DEV_ENABLE_TITLE', 'Enable the Development Mode');
define('FRM_DEV_ENABLE_DESC', 'If Development Mode is enabled the error messages are active.');
define('FRM_DEV_SQL_TRACKER_TITLE', 'Enable SQL Tracker');
define('FRM_DEV_SQL_TRACKER_DESC', 'SQL Tracker shows a list of sql queries.');
define('FRM_DEV_ADMIN_FLUSH_CACHE', 'Flush Cache');
define('FRM_DEV_APP_INFORMER_TITLE', 'Enable Application Informer');
define('FRM_DEV_APP_INFORMER_DESC', 'Shows a list of active application parts.');