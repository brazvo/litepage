<?
// CONTROLLER
define('CAT_DEFAULT_TITLE', 'Kategória - adminsztráció');
define('CAT_DEFAULT_NO_RECORDS', 'Kategória esetében úgy, hogy az "Új hozzáadása" hivatkozásra kattintanak.');
define('CAT_DEFAULT_DESC', 'Hozza létre a főkategóriát, válassza ki a tartalmakat, amelyeket ebbe a kategóiriába fog összevonni, és állítsa be a nevek és a megjelenítés paramétereit.
                          Ezután hozza létre a kategória alkategóriáit.');
define('CAT_ITEMS_LIST_TITLE', 'Kategóriák elemei - adminsztráció');
define('CAT_ITEMS_NO_RECORDS', 'Alkategóriát úgy ad hozzá, hogy rákattint az "Új hozzáadása" hivatkozásra.');
define('CAT_ADD_TITLE', 'Kategória hozzáadása');
define('CAT_ITEMS_ADD_TITLE', 'Alkategória hozzáadása');
define('CAT_EDIT_TITLE', 'Upraviť kategóriu');
define('CAT_ITEMS_EDIT_TITLE', 'Alkategória szerkesztése');
define('CAT_DELETE_TITLE', 'Kategória törlése');
define('CAT_ITEMS_DELETE_TITLE', 'Alkategória törlése');
define('CAT_DELETE_TEXT', 'A kategória törlésével törli az alkategóriákat is, és a menühöz tartozó összes elemet.');
define('CAT_ITEMS_DELETE_TEXT', 'Az alkategória tölésével törli a menühöz tartozó elemet is.');

	//FORM
define('CAT_FRM_TITLE_RULE','Töltse ki a megnevezést');
define('CAT_FRM_MENU_TITLE_DESC','Ha szeretné, hogy a menüben más megnevezés szerepeljen (pl.: rövidített), akkor töltse ki ezt a mezőt.
                                  Ha a mezőt üresen hagyja, akkor automatikusan a megnevezés tartalma jelenik meg.');
define('CAT_FRM_CONTENT_TYPES_LABEL', 'Válassza ki azokat a tartalmakat, amelyek ebbe a kategóriába fognak tartozni.');
define('CAT_FRM_MAIN_MENU_ITEM_LABEL', 'Megjelenítés a menüben mint fő elemet');
define('CAT_FRM_SHOW_PARTIAL_LABEL', 'Csak szövegrész megjelenítése');
define('CAT_FRM_SHOW_PARTIAL_DESC', 'Ha bejelöletlenül hagyja, a mező teljes tartalma megjelenik.<br/>
                                     Ha bejelöli, akkor adja meg a betűk számát amelyek megjelennek.<br/>
									 Ha bejelöli, és a betűk számát 0-ra állítja, akkor a szöveg nem jelenik meg.');
define('CAT_FRM_CHARS_NUM_LABEL', 'Szöveg betűinek mennyisége');
define('CAT_FRM_CHARS_NUM_RULE', 'Az adott értéknek számnak kell lennie');
define('CAT_FRM_SHOW_CREATED_LABEL', 'Mutasd megalakulásától számított');
define('CAT_FRM_SHOW_UPDATED_LABEL', 'Aktualizálás dátumának megjelenítése');
define('CAT_FRM_SHOW_USER_LABEL', 'Létrehozó nevének megjelenítése');
define('CAT_FRM_IMAGE_GALLERY_LABEL', 'Megjeleníteni galériaként');
define('CAT_FRM_IMAGE_GALLERY_DESC', 'Ha képgalériát kategorizál, akkor ajánlatos ezt a lehetőséget választani. A galéria hivatkozásában a galéria első képe jelenik meg.');
define('CAT_FRM_SHOW_IMAGES_LABEL', 'Zobraziť všetky pripojené obrázky');
define('CAT_FRM_SHOW_FILES_LABEL', 'Zobrazť všetky pripojené súbory');
define('CAT_FRM_REQUIRED_DESC','Jelölje be, ha kéri, hogy valamelyik kategóriát válassza ki.');

//ERRORS
define('CAT_ERR_BAD_ID', 'Nem létezik bejegyzés megadott azonosítóval');
define('CAT_CATEGORY_EMPTY', 'A kategória még üres.');
define('CAT_READ_MORE', 'Read&nbsp;more');