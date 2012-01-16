<?php
/************* Administration (Slovak) *************/

/*** CONTENT CONTROLLER ***/
define('NOTHING_TO_EDIT', 'Nie je čo editovať!');
define('FILE_WAS_UPLOADED_AND_CHANGES_WERE_SAVED', 'Súbor bol nahratý a zmeny boli uložené.');
define('CHANGES_WERE_SAVED', 'Zmeny boli uložené.');
define('CONTENT_WAS_SAVED', 'Obsah bol uložený.');
define('NOTHING_TO_DELETE', 'Nie je čo vymazať!');
define('CONTENT', 'Obsah');
define('CONTENT_LIST', 'Obsah - zoznam');
define('NO_CONTENT_YET', 'Ešte nemáte vytvorený žiadny obsah.');
define('NO_CONTENT', 'Žiadny obsah.');
define('ADD_NEW_CONTENT', 'Pridať nový obsah');
define('NEW_CONTENT', 'Nový obsah');

//edit form
define('MENU_TITLE_LABEL', 'Titulok v menu');
define('MENU_TITLE_DESC', 'Zadajte titulok, ktorý sa bude zobrazovať ako položka menu. Ak nechcete vložiť položku do menu, nechajte prázdne.');
define('MENU_ITEMS_LABEL', 'Nadradená položka');
define('MENU_ITEMS_DESC', 'Zvoľte alebo zmente nadradenú položku.');
define('MENU_SETTINGS', 'Nastavenia menu');

//LIST
define('LIST_CONT_TITLE','Názov');
define('LIST_CONT_TYPE','Typ');
define('LIST_LAST_UPD','Posl. aktual.');
define('LIST_LANG_ID','Jazyk');
define('LIST_CREATED_BY','Vytvoril');

// FILTER
define('FILTER_CHOOSE_CONT_TYPE','Vyberte Typ obsahu');
define('FILTER_CHOOSE_CATEGORY','Vyberte Kategóriu');
define('FILTER_CHOOSE_LANGUAGE','Vyberte Jazyk');
define('FILTER_WITHOUT_LANGUAGE','Bez určeného jazyka');

// PAGINATOR
define('PAGINATOR_PAGE','Stránka');
define('PAGINATOR_FIRST','prvá');
define('PAGINATOR_LAST','posledná');

/************* LANGUAGES ****************/
define('LANG_ADMIN_TITLE','Adminstrácia jazykov');
define('LANG_ADMIN_ADD_TITLE','Pridať nový jazyk');
define('LANG_ADMIN_EDIT_TITLE','Úprava jazyka');
define('LANG_ADMIN_SET_DEFAULT','Nastav ako predvolené');

define('LANG_FRM_DESC_LANGID','Dvojmiestny kód jazyka (pozri ISO kódy jazykov), ktorý bude súčasťou URL adresy, ak bude zvolená alternatívna jazyková mutácia.');
define('LANG_FRM_DESC_MACH_NAME','Strojové (najlepšie anglické) meno jazyka bez medier a diakritiky. Pri načítavaní jazykovej mutácie bude systém hľadať súbory v adresároch <i>languages</i> skladajúce sa z tohto mena a prípony <i>inc</i>. Príklad: english = english.inc');
define('LANG_FRM_DESC_URL_ALIAS','Zadajte hlavnú stránku jazykovej mutácie, ktorá sa zobrazí po zadaní www.vasa_stranka.sk alebo www.vasa_stranka.sk/sk ak nebude táto mutácia nastavená ako predvolená. Môžete použiť alias cesty alebo odaz na obsah v tvare <i>content/show/%id</i>');
define('LANG_FRM_RULE_NAME','Vyplňte položku Názov');
define('LANG_FRM_RULE_LANGID','Vyplňte položku Kód');
define('LANG_FRM_RULE_MACH_NAME', 'Vyplňte položku Strojové meno');

define('LANG_ADMIN_ERR_ONE','Jazyk s týmto kódom sa nenachádza v databáze.');
define('LANG_ADMIN_ERR_TWO','Vymazanie záznamu sa nepodarilo.');
define('LANG_ADMIN_ERR_THREE','Nemôžete dektivovať predvolený jazyk.');

/************** MENUS ******************/
define('MENUS_ADMIN_TITLE','Adminstrácia menu');
define('MENUS_ADMIN_ADD_TITLE','Pridať nové menu');
define('MENUS_ADMIN_EDIT_TITLE','Úprava menu');
define('MENUS_ADMIN_EDIT_ITEMS_TITLE','Úprava menu');
define('MENUS_ADMIN_ADD_LINK','Pridať menu');

define('MENUS_FRM_RULE_NAME','Zadajte názov menu');
define('MENUS_FRM_DESC_MACH_NAME','Strojové meno slúži na vyvolanie menu pomocou funckie <b>block::getMenu("strojove_meno")</b>. Strojové meno nesmie obsahovat nepovolené znaky a diakritiku ako sú + - / | \ medzera š č ť a pod. Ak necháte toto políčko prázdne, strojové meno bude vytvorené automaticky úpravou názvu menu.');

/************** MENU iTEMS ****************/
define('MENUITEMS_ADMIN_TITLE','Úprava položiek menu');
define('MENUITEMS_ADMIN_EDIT_TITLE','Úprava položky menu');
define('MENUITEMS_ADMIN_ADD_TITLE','Nová položka menu');
define('MENUITEMS_ADMIN_DELETE_TITLE','Vymazanie položky menu');




/**************** MODULES *****************/
define('MODULES_ADMIN_DEFAULT_TITLE','Zoznam nainštalovaných modulov');
define('MODULES_ADMIN_LIST_TITLE','Zoznam modulov');

define('MODULES_ADMIN_ERR_DEFAULT','Žiadne moduly nie sú nainštalované.');
define('MODULES_ADMIN_ERR_LIST','Nemáte oprávnenie na zobrazenie tejto stránky.');

/**************** USERS *******************/
define('USERS_ADMIN_USERS_PERMISIONS_LNK', 'Úprava užívateľských práv');
define('USERS_ADMIN_USERS_LOCK_LNK', 'Zablokovať užívateľa');
define('USERS_ADMIN_USERS_UNLOCK_LNK', 'Odblokovať užívateľa');
define('USERS_ADMIN_USERS_GENERATE_LNK', 'Generovať nové heslo');
define('USERS_ADMIN_USERS_PERMISIONS_SAVE_OK', 'Oprávnenia boli uložené');
define('USERS_ADMIN_USERS_PERMISIONS_SAVE_FAILED', 'Oprávnenia sa neuložili');
define('USERS_ADMIN_FRM_ADD_USERS_PERMISIONS_MACHINE_NAME', 'Vyberte modul alebo typ obsahu');
define('USERS_ADMIN_FRM_ADD_USERS_PERMISIONS_VIEW', 'Prezeranie');
define('USERS_ADMIN_FRM_ADD_USERS_PERMISIONS_ADD', 'Pridávanie');
define('USERS_ADMIN_FRM_ADD_USERS_PERMISIONS_EDIT', 'Editácia');
define('USERS_ADMIN_FRM_ADD_USERS_PERMISIONS_DELETE', 'Výmaz');

/**************** PERMISSIONS **************/
define('PERMISSIONS_ADMIN_TITLE', 'Oprávnenia');
define('PERMISSIONS_ADMIN_NO_PERMISSIONS', 'Nemáte oprávnenie na zobrazenie tejto stránky');

/*************** DEVELOPMENT **************/
define('DEV_ADMIN_DEFAULT_TITLE', 'Vývojárske nástroje a výkon');
define('DEV_ADMIN_DEFAULT_ERROR', 'Nemáte oprávnenie meniť tieto nastavenie');
define('FRM_DEV_CACHE_PAGES_TITLE', 'Zapnúť cache pre stránky');
define('FRM_DEV_CACHE_PAGES_DESC', 'Cache pre stránky doporučujeme vypnúť pri vývoji');
define('FRM_DEV_CACHE_CSS_TITLE', 'Zapnúť cache pre CSS súbory');
define('FRM_DEV_CACHE_CSS_DESC', 'Zapnite cache pre css v produkcii. Zabránite tak prekročeniu maximálneho počtu .css súborov v IE.');
define('FRM_DEV_CACHE_JS_TITLE', 'Zapnúť cache pre JavaScript súbory');
define('FRM_DEV_CACHE_JS_DESC', 'Zapnite cache pre JS v produkcii. Zabránite tak prekročeniu maximálneho počtu .js súborov v IE.');
define('FRM_DEV_ENABLE_TITLE', 'Zapnúť mód Vývoj');
define('FRM_DEV_ENABLE_DESC', 'Pri zapnutom móde Vývoj sa zobrazujú chybové hlásenia potrebne pri vývoji.');
define('FRM_DEV_SQL_TRACKER_TITLE', 'Zapnúť SQL Tracker');
define('FRM_DEV_SQL_TRACKER_DESC', 'Zaobrazuje prehľad vykonaných sql príkazov.');
define('FRM_DEV_ADMIN_FLUSH_CACHE', 'Vyprázdniť Cache');
define('FRM_DEV_APP_INFORMER_TITLE', 'Zapnúť Aplikačný informátor');
define('FRM_DEV_APP_INFORMER_DESC', 'Zaobrazuje prehľad aktívnych častí aplikácie.');
