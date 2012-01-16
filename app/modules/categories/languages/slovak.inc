<?
/***************** CATEGORIES (Slovak) ******************/

// CONTROLLER
define('CAT_DEFAULT_TITLE', 'Kategória - adminstrácia');
define('CAT_DEFAULT_NO_RECORDS', 'Kategóriu pridáte tak, že kliknete na odkaz Pridať novú.');
define('CAT_DEFAULT_DESC', 'Vytvorte hlavnú kategóriu, zvoľte ktoré typy obsahu bude táto kategória združovať a nastavte parametre pre menu a zobrazovanie.
                          Potom pre kategóriu vytvorte podkategórie.');
define('CAT_ITEMS_LIST_TITLE', 'Položky kategórie - adminstrácia');
define('CAT_ITEMS_NO_RECORDS', 'Podkategóriu pridáte tak, že kliknete na odkaz Pridať novú.');
define('CAT_ADD_TITLE', 'Pridať kategóriu');
define('CAT_ITEMS_ADD_TITLE', 'Pridať podkategóriu');
define('CAT_EDIT_TITLE', 'Upraviť kategóriu');
define('CAT_ITEMS_EDIT_TITLE', 'Upraviť podkategóriu');
define('CAT_DELETE_TITLE', 'Vymazať kategóriu');
define('CAT_ITEMS_DELETE_TITLE', 'Vymazať podkategóriu');
define('CAT_DELETE_TEXT', 'Vymazaním kategórie vymažete aj všetky podkategórie ako aj všetky položky prislúchajúce v menu.');
define('CAT_ITEMS_DELETE_TEXT', 'Vymazaním podkategórie vymažete aj položku prislúchajúcu v menu.');

	//FORM
define('CAT_FRM_TITLE_RULE','Vyplňte položku Titulok');
define('CAT_FRM_MENU_TITLE_DESC','Ak potrebujete, aby sa v menu zobrazoval iný (napr. skrátený) titulok, tak vyplnte túto položku.
                                  Ak položku necháte prázdnu, tak sa automaticky vyplní hodnotou z položky Titulok.');
define('CAT_FRM_CONTENT_TYPES_LABEL', 'Vyberte typy obsahu, ktoré budú patriť do tejto kategórie.');
define('CAT_FRM_MAIN_MENU_ITEM_LABEL', 'Zobraziť v menu ako hlavnú položku');
define('CAT_FRM_SHOW_PARTIAL_LABEL', 'Zobraziť len časť textu');
define('CAT_FRM_SHOW_PARTIAL_DESC', 'Ak ponecháte odškrtnuté, bude sa obsah položky Telo zobrazovať v plnom rozsahu.<br/>
                                     Ak zaškrtnete, tak zadajte počet znakov ktoré sa zobrazia.<br/>
									 Ak zaškrtnete, a počet znakov nastvíte na 0, tak sa nezobrazí žiadny text.');
define('CAT_FRM_CHARS_NUM_LABEL', 'Počet znakov z textu');
define('CAT_FRM_CHARS_NUM_RULE', 'Zadaná hodnota musí byť číslo');
define('CAT_FRM_SHOW_CREATED_LABEL', 'Zobrazovať dátum vytvorenia');
define('CAT_FRM_SHOW_UPDATED_LABEL', 'Zobrazovať dátum aktualizácie');
define('CAT_FRM_SHOW_USER_LABEL', 'Zobrazovať meno autora');
define('CAT_FRM_IMAGE_GALLERY_LABEL', 'Zobraziť ako galériu');
define('CAT_FRM_IMAGE_GALLERY_DESC', 'Ak kategorizujete obrázkové galérie, tak je vhodné zvoliť túto možnosť. V odkaze na galériu sa zobrazí prvý prislúchajúci obrázok.');
define('CAT_FRM_SHOW_IMAGES_LABEL', 'Zobraziť všetky pripojené obrázky');
define('CAT_FRM_SHOW_FILES_LABEL', 'Zobrazť všetky pripojené súbory');
define('CAT_FRM_REQUIRED_DESC','Zaškrtnite, ak bude vyžadované, aby sa vybrala niektorá kategória.');
define('CAT_FRM_SHOW_PAGES_LABEL','Odstránkovať obsah.');
define('CAT_FRM_SHOW_PAGES_DESC','Zaškrtnite, ak chcete, aby sa výpis kategórie odstránkoval. Nastavte počet stránok v stránkovači a počet položiek na stránku.');
define('CAT_FRM_ITEMS_PER_PAGE_LABEL','Položiek na stránku');
define('CAT_FRM_PAGINATOR_LIMIT_LABEL','Stránok v stránkovači');

//ERRORS
define('CAT_ERR_BAD_ID', 'Záznam so zadaným ID neexistuje');
define('CAT_CATEGORY_EMPTY', 'Kategória je zatiaľ prázdna.');
define('CAT_READ_MORE', 'Čítať&nbsp;viac');