<?
/************** Module Kontakt (english)**********/

/**** ACTIONS, RENDERS ****/
define('KONTAKT_ACT_SEND_MESSAGE_SENT', 'The message was sent, thank you.');
define('KONTAKT_ACT_SEND_MESSAGE_SENT_ERROR', 'Message sending failed.');
define('KONTAKT_ACT_SAVE_SETTINGS_SAVE_OK', 'Settings were saved.');
define('KONTAKT_ACT_SAVE_SETTINGS_SAVE_FAILED', 'Settings saving failed');
define('KONTAKT_RND_DEFAULT_TITLE', 'Database of Messages');
define('KONTAKT_RND_SHOW_OPEN_FORM', 'Open contact form');
define('KONTAKT_RND_ADD_TITLE', 'New Contact Form');
define('KONTAKT_RND_EDIT_TITLE', 'Contact Form - Editation');
define('KONTAKT_RND_ADMIN_TITLE', 'Contact Form - Administration');
define('KONTAKT_RND_SETTINGS_TITLE', 'Contact Form - Messages Settings');

/**** SETTINGS FORM ****/
define('KONTAKT_FRM_SETTINGS_MESAGES_PER_PAGE_LABEL', 'Messages per Page');
define('KONTAKT_FRM_SETTINGS_MESAGES_PER_PAGE_DESC', 'Paging of messages in history of messages');
define('KONTAKT_FRM_SETTINGS_ORDER_LABEL', 'Ordering');
define('KONTAKT_FRM_SETTINGS_ORDER_DESC', 'Ordering of messages');
define('KONTAKT_FRM_SETTINGS_ORDER_OLDER', 'Older');
define('KONTAKT_FRM_SETTINGS_ORDER_NEWER', 'Newer');
define('KONTAKT_FRM_SETTINGS_NOTIFICATE_LABEL', 'Email for Notification');
define('KONTAKT_FRM_SETTINGS_NOTIFICATE_DESC', 'Fill email where the messages will be sent. If you will not put your email here, the messages will be saved into database only.');
define('KONTAKT_FRM_SETTINGS_NOTIFICATE_RULE', 'Fill correct email address.');
define('KONTAKT_FRM_SETTINGS_AUTO_CLEAN_LABEL', 'Automatical Erasing of Old Messages');
define('KONTAKT_FRM_SETTINGS_AUTO_CLEAN_DESC', 'If you select this option, the old messages will be deleted automaticaly from database. This option is good to keep you database clean and fresh read.');
define('KONTAKT_FRM_SETTINGS_DELETE_OLDER_LABEL', 'Automaticaly erase older then (day amount)');
define('KONTAKT_FRM_SETTINGS_DELETE_OLDER_DESC', 'If you have selected the option <i>Automatical Erasing of Old Messages</i>, then the messageges older then filled day amount will be automaticaly erased. Minimal amount is 1.');
define('KONTAKT_FRM_SETTINGS_DELETE_OLDER_RULE', 'Fill a number with minimal amount 1.');

/**** EDIT, ADD FORM ****/
define('KONTAKT_FRM_TITLE_RULE', 'Fill item Title');
define('KONTAKT_FRM_FORM_TITLE_LABEL', 'Form Tile');
define('KONTAKT_FRM_FORM_TITLE_DESC', 'Fill if you want have title above the form.');
define('KONTAKT_FRM_BODY_DESC', 'It will appear after the form. If you want have the body in front of th form, select the option <i>Place form after body</i>.');
define('KONTAKT_FRM_COMPANY_SHOW', 'Show field Company');
define('KONTAKT_FRM_COMPANY_REQUIRED', 'Company is required');
define('KONTAKT_FRM_ADDRESS_SHOW', 'Show field Address');
define('KONTAKT_FRM_ADDRESS_REQUIRED', 'Address is required');
define('KONTAKT_FRM_PHONE_SHOW', 'Show field Phone');
define('KONTAKT_FRM_PHONE_REQUIRED', 'Phone is required');
define('KONTAKT_FRM_EMAIL_SHOW', 'Show field Email');
define('KONTAKT_FRM_EMAIL_REQUIRED', 'Email is required');
define('KONTAKT_FRM_SUBJECT_SHOW', 'Show field Subject');
define('KONTAKT_FRM_SUBJECT_REQUIRED', 'Subject is required');
define('KONTAKT_FRM_FORM_AFTER_TEXT', 'Place form after body');
define('KONTAKT_FRM_HIDE_ON_LOAD', 'Hide Form');

define('MENU_TITLE_LABEL', 'Title in Menu');
define('MENU_TITLE_DESC', 'Fill title that appears as menu item. If you do not want to put this content to the menu, leave blank.');
define('MENU_ITEMS_LABEL', 'Parent menu item');
define('MENU_ITEMS_DESC', 'Select or change parent menu item.');
define('MENU_SETTINGS', 'Menu Settings');

/**** CREATE FORM ****/
define('KONTAKT_FRM_NAME_LABEL', 'First Name and Surname');
define('KONTAKT_FRM_NAME_RULE', 'Fill item First Name and Surname');
define('KONTAKT_FRM_COMPANY_LABEL', 'Company');
define('KONTAKT_FRM_COMPANY_RULE', 'Fill item Company');
define('KONTAKT_FRM_ADDRESS_LABEL', 'Address');
define('KONTAKT_FRM_ADDRESS_RULE', 'Fill item Address');
define('KONTAKT_FRM_PHONE_LABEL', 'Phone');
define('KONTAKT_FRM_PHONE_RULE', 'Fill item Phone');
define('KONTAKT_FRM_EMAIL_RULE_EMAIL', 'Put correct email address');
define('KONTAKT_FRM_EMAIL_RULE_FILLED', 'Fill item Email');
define('KONTAKT_FRM_SUBJECT_LABEL', 'Subject');
define('KONTAKT_FRM_SUBJECT_RULE', 'Fill item Subject');
define('KONTAKT_FRM_MESSAGE_LABEL', 'Message');
define('KONTAKT_FRM_MESSAGE_RULE', 'Fill Messag box');
define('KONTAKT_FRM_CAPTCHA_LABEL', 'Write security code from picture');
define('KONTAKT_FRM_CAPTCHA_RULE', 'Write security code from picture correctly');
define('KONTAKT_FRM_SEND_LABEL', 'Send Message');

/**** TEMPLATES ****/
define('KONTAKT_TPL_MESSAGES_SETTINGS_LNK', 'Settings of Sent Messages');
define('KONTAKT_TPL_MESSAGES_HISTORY_LNK', 'History of Sent Messages');
define('KONTAKT_TPL_ADD_FORM_LNK', 'Add Form');
define('KONTAKT_TPL_NO_FORMS_YET', 'No form yet.');
define('KONTAKT_TPL_SENT_FROM_FORM', 'Sent from the form:');
define('KONTAKT_TPL_DATABASE_EMPTY', 'Database is empty yet.');
define('KONTAKT_TPL_NO_PERMISION_FOR_CONTENT', 'You have no permision to watch this content.');
