<?php
/***************************************** INSTALLATION ROUTINE ***/
  function moduleInstall()
  {
    db::exec("INSERT INTO permisions (content, cont_mach_name, editor_view, editor_add, editor_edit, editor_delete, user_view, user_add, user_edit, user_delete, visitor_view, visitor_add, visitor_edit, visitor_delete)
	                                 VALUES ('Novinky', 'novinky', 1,1,1,1,1,0,0,0,1,0,0,0)");
	$sql = "CREATE TABLE hotnews (
	          `id` INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
			  `title` VARCHAR(50),
			  `message` TEXT,
			  `datetime` TIMESTAMP DEFAULT CURRENT_TIMESTAMP)";
	// if mysql replace some strings
	if(DB_DRIVER == 'mysql') $sql = sqliteToMysql($sql);
	db::exec($sql);
	
	$sql = "CREATE TABLE hotnews_settings (
	          `constant` VARCHAR(50),
			  `title` VARCHAR(50),
			  `description` TEXT,
			  `value` VARCHAR(256),
			  `frm_name` VARCHAR(256))";
	// if mysql replace some strings
	if(DB_DRIVER == 'mysql') $sql = sqliteToMysql($sql);
	db::exec($sql);
	
	db::exec("INSERT INTO hotnews_settings (constant, title, description, value, frm_name) VALUES ('HN_MESSAGES_IN_BLOCK', 'Počet noviniek v bloku', 'Zobrazí počet posledných noviniek podľa zadaného čísla.', 3, 'messages_in_block')");
	db::exec("INSERT INTO hotnews_settings (constant, title, description, value, frm_name) VALUES ('HN_CHARACTERS_IN_BLOCK', 'Počet znakov textu v bloku', 'Zobrazí dĺžku správy podľa zadaného čísla. Ak správa presahuje dľžkou počet znakov, zobrazí sa link <i>Čítaj ďalej</i>. Ak zadáte hodnotu nula alebo necháte políčko prázdne, tak sa zobrazí celá správa.', 128, 'characters_in_block')");
	db::exec("INSERT INTO hotnews_settings (constant, title, description, value, frm_name) VALUES ('HN_MESSAGES_ON_PAGE', 'Počet noviniek na stránke.', 'Zobrazí počet noviniek na stránke noviniek podľa zadaného čísla.', 10, 'messages_on_page')");
	db::exec("INSERT INTO hotnews_settings (constant, title, description, value, frm_name) VALUES ('HN_ORDER', 'Zoradenie', 'Spôsob zoradenia noviniek', 'DESC', 'order')");
	db::exec("INSERT INTO hotnews_settings (constant, title, description, value, frm_name) VALUES ('HN_SHOW_DATE', 'Zobraziť dátum?', '', 1, 'show_date')");
	
	// Insert into admin menu
	$row = db::fetch("SELECT * FROM menus WHERE machine_name='administration'");
	$menu_id = $row['id'];
	$row = db::fetch("SELECT id FROM menu_items WHERE menu_id=$menu_id AND path='admin/modules'");
	$child_of = $row['id'];
	db::exec("INSERT INTO menu_items (title, path, allowed, expanded, priority, child_of, menu_id, name, image)
	          VALUES ('Novinky', 'novinky/administrate/directory', 1, 0, 0, $child_of, $menu_id, 'Novinky', '')");
			  
	// Insert into editors menu
	$row = db::fetch("SELECT * FROM menus WHERE machine_name='editors_menu'");
	$menu_id = $row['id'];
	$row = db::fetch("SELECT id FROM menu_items WHERE menu_id=$menu_id AND path='admin/modules'");
	$child_of = $row['id'];
	db::exec("INSERT INTO menu_items (title, path, allowed, expanded, priority, child_of, menu_id, name, image)
	          VALUES ('Novinky', 'novinky/admin', 1, 0, 0, $child_of, $menu_id, 'Novinky', '')");
			  
	db::exec("UPDATE modules SET installed=1 WHERE machine_name='novinky'");
	
  }
  
/***************************************** UNINSTALLATION ROUTINE ***/
  function moduleUninstall()
  {
    db::exec("DELETE FROM permisions WHERE cont_mach_name='novinky'");
	db::exec("DROP TABLE hotnews");
	db::exec("DROP TABLE hotnews_settings");
	db::exec("DELETE FROM menu_items WHERE path='novinky/administrate/directory'");
	db::exec("UPDATE modules SET installed=0 WHERE machine_name='novinky'");
  }