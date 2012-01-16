<?
/************** Module Kontakt (slovak)**********/

/**** ACTIONS, RENDERS ****/
define('KONTAKT_ACT_SEND_MESSAGE_SENT', 'Správa bola odoslaná, ďakujeme.');
define('KONTAKT_ACT_SEND_MESSAGE_SENT_ERROR', 'Odoslanie správy zlyhalo.');
define('KONTAKT_ACT_SAVE_SETTINGS_SAVE_OK', 'Nastavenia boli uložené.');
define('KONTAKT_ACT_SAVE_SETTINGS_SAVE_FAILED', 'Uloženie nastavení zlyhalo.');
define('KONTAKT_RND_DEFAULT_TITLE', 'Databáza správ');
define('KONTAKT_RND_SHOW_OPEN_FORM', 'Otvoriť kontaktný formulár');
define('KONTAKT_RND_ADD_TITLE', 'Nový kontaktný formulár');
define('KONTAKT_RND_EDIT_TITLE', 'Kontaktný formulár - úprava');
define('KONTAKT_RND_ADMIN_TITLE', 'Kontaktný formulár - administrácia');
define('KONTAKT_RND_SETTINGS_TITLE', 'Kontaktný formulár - nastavenie správ');

/**** SETTINGS FORM ****/
define('KONTAKT_FRM_SETTINGS_MESAGES_PER_PAGE_LABEL', 'Počet správ na stránku');
define('KONTAKT_FRM_SETTINGS_MESAGES_PER_PAGE_DESC', 'Stránkovanie správ v histórii správ');
define('KONTAKT_FRM_SETTINGS_ORDER_LABEL', 'Zoradenie');
define('KONTAKT_FRM_SETTINGS_ORDER_DESC', 'Spôsob zoradenia správ');
define('KONTAKT_FRM_SETTINGS_ORDER_OLDER', 'Staršie');
define('KONTAKT_FRM_SETTINGS_ORDER_NEWER', 'Novšie');
define('KONTAKT_FRM_SETTINGS_NOTIFICATE_LABEL', 'Notifikačný email');
define('KONTAKT_FRM_SETTINGS_NOTIFICATE_DESC', 'Zadajte email, na ktorý budú chodiť správy. Ak email nezadáte správy sa nebudú posielať a budú len uložené do databázy.');
define('KONTAKT_FRM_SETTINGS_NOTIFICATE_RULE', 'Zadajte korektný email.');
define('KONTAKT_FRM_SETTINGS_AUTO_CLEAN_LABEL', 'Automatické mazanie starých správ');
define('KONTAKT_FRM_SETTINGS_AUTO_CLEAN_DESC', 'Ak zaškrtnete túto voľbu, staré správy sa budú automaticky mazať z databázy. Týmto zabezbečíte to, že databáza bude stále udržovaná a jej načítanie bude rýchlejše.');
define('KONTAKT_FRM_SETTINGS_DELETE_OLDER_LABEL', 'Automaticky vymazať staršie ako (počet dní)');
define('KONTAKT_FRM_SETTINGS_DELETE_OLDER_DESC', 'Ak je zvolená voľba <i>Automatické mazanie starých správ</i>, tak správy staršie ako zvolený počet dní budú automaticky vymazané. Minimálna hodnota je 1.');
define('KONTAKT_FRM_SETTINGS_DELETE_OLDER_RULE', 'Zadajte číslo s minimálnou hodnotou 1.');

/**** EDIT, ADD FORM ****/
define('KONTAKT_FRM_TITLE_RULE', 'Vyplňte položku Titulok');
define('KONTAKT_FRM_FORM_TITLE_LABEL', 'Titulok formulára');
define('KONTAKT_FRM_FORM_TITLE_DESC', 'Vyplňte, ak chcete, aby sa nad formulárom zobrazoval titulok.');
define('KONTAKT_FRM_BODY_DESC', 'Zobrazí sa za formulárom. Ak chcete, aby sa tento obsah zobrazil pred formulárom, zaškrtnite políčko <i>Zobraziť formulár za textom</i>.');
define('KONTAKT_FRM_COMPANY_SHOW', 'Zobraziť pole firma');
define('KONTAKT_FRM_COMPANY_REQUIRED', 'Pole firma bude povinné');
define('KONTAKT_FRM_ADDRESS_SHOW', 'Zobraziť pole adresa');
define('KONTAKT_FRM_ADDRESS_REQUIRED', 'Pole adresa bude povinné');
define('KONTAKT_FRM_PHONE_SHOW', 'Zobraziť pole telefón');
define('KONTAKT_FRM_PHONE_REQUIRED', 'Pole telefón bude povinné');
define('KONTAKT_FRM_EMAIL_SHOW', 'Zobraziť pole email');
define('KONTAKT_FRM_EMAIL_REQUIRED', 'Pole email bude povinné');
define('KONTAKT_FRM_SUBJECT_SHOW', 'Zobraziť pole predmet');
define('KONTAKT_FRM_SUBJECT_REQUIRED', 'Pole predmet bude povinné');
define('KONTAKT_FRM_FORM_AFTER_TEXT', 'Zobraziť formulár za textom');
define('KONTAKT_FRM_HIDE_ON_LOAD', 'Schovávať formulár');

define('MENU_TITLE_LABEL', 'Titulok v menu');
define('MENU_TITLE_DESC', 'Zadajte titulok, ktorý sa bude zobrazovať ako položka menu. Ak nechcete vložiť položku do menu, nechajte prázdne.');
define('MENU_ITEMS_LABEL', 'Nadradená položka');
define('MENU_ITEMS_DESC', 'Zvoľte alebo zmente nadradenú položku.');
define('MENU_SETTINGS', 'Nastavenia menu');

/**** CREATE FORM ****/
define('KONTAKT_FRM_NAME_LABEL', 'Meno a Priezvisko');
define('KONTAKT_FRM_NAME_RULE', 'Vyplňte položku Meno a Priezvisko');
define('KONTAKT_FRM_COMPANY_LABEL', 'Firma');
define('KONTAKT_FRM_COMPANY_RULE', 'Vyplňte položku Firma');
define('KONTAKT_FRM_ADDRESS_LABEL', 'Adresa');
define('KONTAKT_FRM_ADDRESS_RULE', 'Vyplňte položku Adresa');
define('KONTAKT_FRM_PHONE_LABEL', 'Telefón');
define('KONTAKT_FRM_PHONE_RULE', 'Vyplňte položku Telefón');
define('KONTAKT_FRM_EMAIL_RULE_EMAIL', 'Zadajte korektný Email');
define('KONTAKT_FRM_EMAIL_RULE_FILLED', 'Vyplňte položku Email');
define('KONTAKT_FRM_SUBJECT_LABEL', 'Predmet');
define('KONTAKT_FRM_SUBJECT_RULE', 'Vyplňte položku Predmet');
define('KONTAKT_FRM_MESSAGE_LABEL', 'Správa');
define('KONTAKT_FRM_MESSAGE_RULE', 'Vyplňte položku Správa');
define('KONTAKT_FRM_CAPTCHA_LABEL', 'Opíšte bezpečnostný kód');
define('KONTAKT_FRM_CAPTCHA_RULE', 'Opíšte správne bezpečnostný kód');
define('KONTAKT_FRM_SEND_LABEL', 'Odoslať správu');

/**** TEMPLATES ****/
define('KONTAKT_TPL_MESSAGES_SETTINGS_LNK', 'Nastavenia odoslaných správ');
define('KONTAKT_TPL_MESSAGES_HISTORY_LNK', 'História odoslaných správ');
define('KONTAKT_TPL_ADD_FORM_LNK', 'Pridať formulár');
define('KONTAKT_TPL_NO_FORMS_YET', 'Zatiaľ nemáte vytvorené žiadne formuláre.');
define('KONTAKT_TPL_SENT_FROM_FORM', 'Odoslané s formulára');
define('KONTAKT_TPL_DATABASE_EMPTY', 'Databáza správ zatiaľ neobsahuje žiadne správy.');
define('KONTAKT_TPL_NO_PERMISION_FOR_CONTENT', 'Nemáte právo na prezeranie tohto obsahu.');