<?php
/***************************************** INSTALLATION ROUTINE ***/
  function moduleInstall()
  {
    // create tables
	$sql = "CREATE TABLE IF NOT EXISTS `categories` (`id` INTEGER PRIMARY KEY AUTOINCREMENT  NOT NULL ,
				`title` VARCHAR(256) ,
				`menu_title` VARCHAR(256) ,
				`description` TEXT,
				`main_menu_item` BOOL DEFAULT 1,
				`menu_id` INTEGER,
				`menu_item_id` INTEGER,
				`show_partial` BOOL DEFAULT 0,
				`chars_num` INTEGER,
				`show_updated` BOOL DEFAULT 0,
				`show_user` BOOL DEFAULT 0,
				`image_gallery` BOOL DEFAULT 0,
				`required` BOOL DEFAULT 0,
				`path_alias` VARCHAR(256),
				`show_created` BOOL DEFAULT 0,
				`show_pages` BOOL DEFAULT 0,
				`items_per_page` INTEGER DEFAULT 10,
				`paginator_limit` INTEGER DEFAULT 10
				)";
	
	// if mysql replace some strings
	if(DB_DRIVER == 'mysql') $sql = sqliteToMysql($sql);
	db::exec($sql);
    
	$sql = "CREATE TABLE IF NOT EXISTS `categories_items` (`id` INTEGER PRIMARY KEY  AUTOINCREMENT  NOT NULL ,
				`cat_id` INTEGER,
				`title` VARCHAR(256) ,
				`description` TEXT,
				`priority` INTEGER DEFAULT 0,
				`menu_id` INTEGER,
				`menu_item_id` INTEGER,
				`path_alias` VARCHAR(256),
				`menu_title` VARCHAR(256),
				`show_images` BOOL DEFAULT 0,
				`show_files` BOOL DEFAULT 0)";
				
	// if mysql replace some strings
	if(DB_DRIVER == 'mysql') $sql = sqliteToMysql($sql);
	db::exec($sql);
	
	$sql = "CREATE TABLE IF NOT EXISTS `categories_content_types` (`catid` INTEGER, `ct_name` VARCHAR(256) )";
	// if mysql replace some strings
	if(DB_DRIVER == 'mysql') $sql = sqliteToMysql($sql);
	db::exec($sql);
	
	$sql = "CREATE TABLE IF NOT EXISTS `categories_relations` (`cat_id` INTEGER, `cat_item_id` INTEGER, `cont_id` INTEGER, `menu_item_id` INTEGER)";
	// if mysql replace some strings
	if(DB_DRIVER == 'mysql') $sql = sqliteToMysql($sql);
	db::exec($sql);
	
	// Insert into admin menu
	$row = db::fetch("SELECT * FROM menus WHERE machine_name='administration'");
	$menu_id = $row['id'];
	$row = db::fetch("SELECT id FROM menu_items WHERE menu_id=$menu_id AND path='admin/modules'");
	$child_of = $row['id'];
	db::exec("INSERT INTO menu_items (title, path, allowed, expanded, priority, child_of, menu_id, name, image)
	          VALUES ('Kategórie', 'categories', 1, 0, 0, $child_of, $menu_id, 'Kategórie', '')");
			  
	// Insert into editors menu
	$row = db::fetch("SELECT * FROM menus WHERE machine_name='editors_menu'");
	$menu_id = $row['id'];
	$row = db::fetch("SELECT id FROM menu_items WHERE menu_id=$menu_id AND path='admin/modules'");
	$child_of = $row['id'];
	db::exec("INSERT INTO menu_items (title, path, allowed, expanded, priority, child_of, menu_id, name, image)
	          VALUES ('Kategórie', 'categories', 1, 0, 0, $child_of, $menu_id, 'Kategórie', '')");
		
	// set permisions
	db::exec("INSERT INTO permisions (content, cont_mach_name, editor_view, editor_add, editor_edit, editor_delete, user_view, user_add, user_edit, user_delete, visitor_view, visitor_add, visitor_edit, visitor_delete)
	                                 VALUES ('Kategórie', 'categories', 1,1,1,1,1,0,0,0,1,0,0,0)");
	
	// update modules table
	db::exec("UPDATE modules SET installed=1 WHERE machine_name='categories'");
  };
  
/***************************************** UNINSTALLATION ROUTINE ***/
  function moduleUninstall()
  {
    db::exec("UPDATE modules SET installed=0 WHERE machine_name='categories'");
	db::exec("DELETE FROM permisions WHERE cont_mach_name='categories'");
	db::exec("DROP TABLE categories");
	db::exec("DROP TABLE categories_items");
	db::exec("DROP TABLE categories_content_types");
	db::exec("DROP TABLE categories_relations");
	db::exec("DELETE FROM menu_items WHERE path='categories'");
  }