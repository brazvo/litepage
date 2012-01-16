<?php
/************* Administration (Hungarian) *************/

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
define('IS_REQUIRED', ' je povinná');

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


/**************** MODULES *****************/
define('MODULES_ADMIN_DEFAULT_TITLE','Zoznam nainštalovaných modulov');
define('MODULES_ADMIN_LIST_TITLE','Zoznam modulov');

define('MODULES_ADMIN_ERR_DEFAULT','Žiadne moduly nie sú nainštalované.');
define('MODULES_ADMIN_ERR_LIST','Nemáte oprávnenie na zobrazenie tejto stránky.');
