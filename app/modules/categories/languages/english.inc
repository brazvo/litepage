<?
/***************** CATEGORIES (Slovak) ******************/

// CONTROLLER
define('CAT_DEFAULT_TITLE', 'Category - Administration');
define('CAT_DEFAULT_NO_RECORDS', 'To add new category click on link Add New.');
define('CAT_DEFAULT_DESC', 'Create main category, select the content types which will be in this category and set parameters for menus and appearance.
                            Then create subcategories for this category.');
define('CAT_ITEMS_LIST_TITLE', 'Category items - administration');
define('CAT_ITEMS_NO_RECORDS', 'To add subcategory click on ADD NEW.');
define('CAT_ADD_TITLE', 'Add Category');
define('CAT_ITEMS_ADD_TITLE', 'Add Subcategory');
define('CAT_EDIT_TITLE', 'Edit Category');
define('CAT_ITEMS_EDIT_TITLE', 'Edit Subcategory');
define('CAT_DELETE_TITLE', 'Delete Category');
define('CAT_ITEMS_DELETE_TITLE', 'Delete Subcategory');
define('CAT_DELETE_TEXT', 'By deleting category you will delete all subcategories as well as all menu items for this category.');
define('CAT_ITEMS_DELETE_TEXT', 'By deleting subcategory you will delete manu item for this subcategory.');

	//FORM
define('CAT_FRM_TITLE_RULE','Fill item Title');
define('CAT_FRM_MENU_TITLE_DESC','If you need to appear diferent title, fill this field too.
                                  If you leave this blank, the alternative title will be filled automatically from Title item.');
define('CAT_FRM_CONTENT_TYPES_LABEL', 'Select content types for this category.');
define('CAT_FRM_MAIN_MENU_ITEM_LABEL', 'Make as parent item in menu');
define('CAT_FRM_SHOW_PARTIAL_LABEL', 'Partial text appearance');
define('CAT_FRM_SHOW_PARTIAL_DESC', 'If you do not choose this option, full content of Body field will be appeared.<br/>
                                     If you select this option, fill amount of characters which appear.<br/>
									 If you select this option and you set amount of characters to Zero, there will be no text appeared.');
define('CAT_FRM_CHARS_NUM_LABEL', 'Amount of Characters of Body text');
define('CAT_FRM_CHARS_NUM_RULE', 'Amount of Characters must be number');
define('CAT_FRM_SHOW_CREATED_LABEL', 'Show the date of creation');
define('CAT_FRM_SHOW_UPDATED_LABEL', 'Show the date of last update');
define('CAT_FRM_SHOW_USER_LABEL', 'Show the name of author');
define('CAT_FRM_IMAGE_GALLERY_LABEL', 'Show as gallery');
define('CAT_FRM_IMAGE_GALLERY_DESC', 'If you categorize image galleries, it is useful to select this option. An image link will be created from the first gallery image.');
define('CAT_FRM_SHOW_IMAGES_LABEL', 'Show all content images');
define('CAT_FRM_SHOW_FILES_LABEL', 'Show all attached files');
define('CAT_FRM_REQUIRED_DESC','Select this option if you want to make choosing of categories required.');

//ERRORS
define('CAT_ERR_BAD_ID', 'The record with this ID does not exists');
define('CAT_CATEGORY_EMPTY', 'Category is empty.');
define('CAT_READ_MORE', 'Read&nbsp;more');