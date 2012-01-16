<?php
/***************************************** INSTALLATION ROUTINE ***/
  function moduleInstall()
  {
    // set permisions
	db::exec("INSERT INTO permisions (content, cont_mach_name, editor_view, editor_add, editor_edit, editor_delete, user_view, user_add, user_edit, user_delete, visitor_view, visitor_add, visitor_edit, visitor_delete)
	                                 VALUES ('Texyla', 'texyla', 0,0,0,0,0,0,0,0,0,0,0,0)");
	
	$sql = "CREATE TABLE texyla (
	          `id` INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
			  `textarea` VARCHAR(128),
			  `description` TEXT)";
	// if mysql replace some strings
	if(DB_DRIVER == 'mysql') $sql = sqliteToMysql($sql);
	db::exec($sql);
	
	$sql = "CREATE TABLE `texyla_settings` (
			  `texyla_id` INTEGER,
			  `role` VARCHAR(20),
			  `allow` BOOL DEFAULT 0,
			  `texyCfg` VARCHAR(50),
			  `bottomLeftToolbarEdit` BOOL DEFAULT 1,
			  `bottomLeftToolbarPreview` BOOL DEFAULT 1,
			  `bottomLeftToolbarHtmlPreview` BOOL DEFAULT 0,
			  `buttonType` VARCHAR(20),
			  `tabs` BOOL DEFAULT 1,
			  `headers` BOOL DEFAULT 0,
			  `font_style` BOOL DEFAULT 1,
			  `text_align` BOOL DEFAULT 1,
			  `lists` BOOL DEFAULT 1,
			  `link` BOOL DEFAULT 1,
			  `img` BOOL DEFAULT 1,
			  `table` BOOL DEFAULT 1,
			  `emoticon` BOOL DEFAULT 1,
			  `symbol` BOOL DEFAULT 1,
			  `color` BOOL DEFAULT 1,
			  `textTransform` BOOL DEFAULT 1,
			  `blocks` BOOL DEFAULT 1,
			  `codes` BOOL DEFAULT 1,
			  `others` BOOL DEFAULT 1)";
			  
	// if mysql replace some strings
	if(DB_DRIVER == 'mysql') $sql = sqliteToMysql($sql);
	db::exec($sql);
	
	// Insert into admin menu
	$row = db::fetch("SELECT * FROM menus WHERE machine_name='administration'");
	$menu_id = $row['id'];
	$row = db::fetch("SELECT id FROM menu_items WHERE menu_id=$menu_id AND path='admin/modules'");
	$child_of = $row['id'];
	db::exec("INSERT INTO menu_items (title, path, allowed, expanded, priority, child_of, menu_id, name, image)
	          VALUES ('Texyla', 'texyla', 1, 0, 0, $child_of, $menu_id, 'Texyla', '')");
			  
	db::exec("UPDATE modules SET installed=1 WHERE machine_name='texyla'");
	
  }
  
/***************************************** UNINSTALLATION ROUTINE ***/
  function moduleUninstall()
  {
	db::exec("DELETE FROM permisions WHERE cont_mach_name='texyla'");
	db::exec("DROP TABLE texyla");
	db::exec("DROP TABLE texyla_settings");
	db::exec("DELETE FROM menu_items WHERE path='texyla'");
	db::exec("UPDATE modules SET installed=0 WHERE machine_name='texyla'");
  }