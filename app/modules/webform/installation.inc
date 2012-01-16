<?php
/***************************************** INSTALLATION ROUTINE ***/
  function moduleInstall()
  {
        db::exec("INSERT INTO permisions (content, cont_mach_name, editor_view, editor_add, editor_edit, editor_delete, user_view, user_add, user_edit, user_delete, visitor_view, visitor_add, visitor_edit, visitor_delete)
	                                 VALUES ('Web formulár', 'webform', 1,1,1,1,1,0,0,0,1,0,0,0)");
	$sql = "CREATE TABLE webform (
	          `id` INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
			  `title` VARCHAR(128),
			  `form_title` VARCHAR(128),
			  `body` TEXT,
			  `path_alias` VARCHAR(128),
			  `form_after_text` BOOL,
			  `hide_on_load` BOOL,
			  `email` VARCHAR(256),
			  `lang` VARCHAR(4),
			  `updated` TIMESTAMP DEFAULT CURRENT_TIMESTAMP)";
	// if mysql replace some strings
	if(DB_DRIVER == 'mysql') $sql = sqliteToMysql($sql);
	db::exec($sql);
	
	$sql = "CREATE TABLE `webform_fields` (
	          `id` INTEGER PRIMARY KEY  NOT NULL ,
			  `webform_id` INTEGER,
			  `type` VARCHAR(256) ,
			  `label` VARCHAR(256) ,
			  `webform_label` VARCHAR(256) ,
			  `attributes` VARCHAR(256) ,
			  `priority` INTEGER DEFAULT 0 ,
			  `default` VARCHAR(256) ,
			  `frm_name` VARCHAR(256) ,
			  `required` BOOL DEFAULT 0,
			  `email` BOOL DEFAULT 1,
			  `field_type` VARCHAR(256) ,
			  `description` TEXT,
			  `machine_field_type` VARCHAR(256)  DEFAULT null)";
	// if mysql replace some strings
	if(DB_DRIVER == 'mysql') $sql = sqliteToMysql($sql);
	db::exec($sql);
	
	$sql = "CREATE TABLE webform_messages (
	          `id` INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
			  `webform_id` INTEGER,
			  `html_content` TEXT,
			  `datetime` TIMESTAMP DEFAULT CURRENT_TIMESTAMP)";
	// if mysql replace some strings
	if(DB_DRIVER == 'mysql') $sql = sqliteToMysql($sql);
	db::exec($sql);
	
	$sql ="CREATE TABLE webform_messages_settings (
	          `constant` VARCHAR(50),
			  `title` VARCHAR(50),
			  `description` TEXT,
			  `value` VARCHAR(256) ,
			  `frm_name` VARCHAR(256) )";
	// if mysql replace some strings
	if(DB_DRIVER == 'mysql') $sql = sqliteToMysql($sql);
	db::exec($sql);
	
	db::exec("INSERT INTO webform_messages_settings (constant, title, description, value, frm_name) VALUES ('WF_MESSAGES_PER_PAGE', 'Počet správ na stránku', 'Stránkovanie správ v histórii správ', 10, 'messages_per_page')");
	db::exec("INSERT INTO webform_messages_settings (constant, title, description, value, frm_name) VALUES ('WF_ORDER', 'Zoradenie', 'Spôsob zoradenia správ', 'ASC', 'order')");
	db::exec("INSERT INTO webform_messages_settings (constant, title, description, value, frm_name) VALUES ('WF_NOTIFICATE', 'Email', 'Zadajte email, na ktorý budú chodiť správy. Ak email nezadáte správy sa nebudú posielať a budú len uložené do databázy.', '', 'notificate')");
	db::exec("INSERT INTO `webform_messages_settings` VALUES ('WF_AUTO_CLEAN','Automatické mazanie starých správ','Ak zaškrtnete túto voľbu, staré správy sa budú automaticky mazať z databázy. Týmto zabezbečíte to, že databáza bude stále udržovaná a jej načítanie bude rýchlejše.','0','auto_clean')");
        db::exec("INSERT INTO `webform_messages_settings` VALUES ('WF_DELETE_OLDER_THEN','Automaticky vymazať staršie ako (počet dní)','Ak je zvolená voľba <i>Automatické mazanie starých správ</i>, tak správy staršie ako zvolený počet dní budú automaticky vymazané. Minimálna hodnota je 1.','30','delete_older_then')");
	
	// Insert into admin menu
	$row = db::fetch("SELECT * FROM menus WHERE machine_name='administration'");
	$menu_id = $row['id'];
	$row = db::fetch("SELECT id FROM menu_items WHERE menu_id=$menu_id AND path='admin/modules'");
	$child_of = $row['id'];
	db::exec("INSERT INTO menu_items (title, path, allowed, expanded, priority, child_of, menu_id, name, image)
	          VALUES ('Web formulár', 'webform/administrate', 1, 0, 0, $child_of, $menu_id, 'Web formulár', '')");
			  
	// Insert into editors menu
	$row = db::fetch("SELECT * FROM menus WHERE machine_name='editors_menu'");
	$menu_id = $row['id'];
	$row = db::fetch("SELECT id FROM menu_items WHERE menu_id=$menu_id AND path='admin/modules'");
	$child_of = $row['id'];
	db::exec("INSERT INTO menu_items (title, path, allowed, expanded, priority, child_of, menu_id, name, image)
	          VALUES ('Web formulár', 'webform/administrate', 1, 0, 0, $child_of, $menu_id, 'Web formulár', '')");
			  
	db::exec("UPDATE modules SET installed=1 WHERE machine_name='webform'");
	
  }
  
/***************************************** UNINSTALLATION ROUTINE ***/
  function moduleUninstall()
  {
        db::exec("DELETE FROM permisions WHERE cont_mach_name='webform'");
	db::exec("DROP TABLE webform");
	db::exec("DROP TABLE webform_fields");
	db::exec("DROP TABLE webform_messages");
	db::exec("DROP TABLE webform_messages_settings");
	db::exec("DELETE FROM menu_items WHERE path='webform/administrate'");
        db::exec("DELETE FROM content WHERE content_type_machine_name='webform'");
	db::exec("UPDATE modules SET installed=0 WHERE machine_name='webform'");
  }