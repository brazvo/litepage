<?php
/***************************************** INSTALLATION ROUTINE ***/
  function moduleInstall()
  {
    db::exec("INSERT INTO permisions (content, cont_mach_name, editor_view, editor_add, editor_edit, editor_delete, user_view, user_add, user_edit, user_delete, visitor_view, visitor_add, visitor_edit, visitor_delete)
	                                 VALUES ('Guestbook', 'guestbook', 1,1,1,1,1,1,0,0,1,1,0,0)");
	$sql = "CREATE TABLE guestbook (
	          `id` INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
			  `name` VARCHAR(50),
			  `email` VARCHAR(128),
			  `subject` VARCHAR(50),
			  `message` TEXT,
			  `datetime` TIMESTAMP DEFAULT CURRENT_TIMESTAMP)";
	
	// if mysql replace some strings
	if(DB_DRIVER == 'mysql') $sql = sqliteToMysql($sql);
	db::exec($sql);
	db::exec("INSERT INTO guestbook (name, subject, message) VALUES ('Administrátor', 'Kniha návštev', 'Kniha návštev bola spustená. Môžete posielať svoje odkazy, námety alebo pripomienky.')");
	
	$sql = "CREATE TABLE guestbook_settings (
	          `constant` VARCHAR(50),
			  `title` VARCHAR(50),
			  `description` TEXT,
			  `value` VARCHAR(256),
			  `frm_name` VARCHAR(256))";
	// if mysql replace some strings
	if(DB_DRIVER == 'mysql') $sql = sqliteToMysql($sql);
	db::exec($sql);
	
	db::exec("INSERT INTO guestbook_settings (constant, title, description, value, frm_name) VALUES ('MESSAGES_PER_PAGE', 'Počet odkazov na stránku', '', 10, 'messages_per_page')");
	db::exec("INSERT INTO guestbook_settings (constant, title, description, value, frm_name) VALUES ('ORDER', 'Zoradenie', 'Spôsob zoradenia odkazov', 'ASC', 'order')");
	db::exec("INSERT INTO guestbook_settings (constant, title, description, value, frm_name) VALUES ('EMAIL_WANTED', 'Políčko email zap./vyp.', '', 0, 'email_wanted')");
	db::exec("INSERT INTO guestbook_settings (constant, title, description, value, frm_name) VALUES ('SUBJECT_WANTED', 'Políčko predmet zap./vyp.', '', 0, 'subject_wanted')");
	db::exec("INSERT INTO guestbook_settings (constant, title, description, value, frm_name) VALUES ('NOTIFICATE', 'Email pre notifikáciu', 'Zadajte email, na ktorý budú chodiť notifikačné maily o novom odkaze. Ak email nezadáte notifikácie sa nebudú posielať.', '', 'notificate')");
	db::exec("INSERT INTO `guestbook_settings` VALUES ('AUTO_CLEAN','Automatické mazanie starých správ','Ak zaškrtnete túto voľbu, staré správy sa budú automaticky mazať z databázy. Týmto zabezbečíte to, že databáza bude stále udržovaná a jej načítanie bude rýchlejše.','0','auto_clean')");
    db::exec("INSERT INTO `guestbook_settings` VALUES ('DELETE_OLDER_THEN','Automaticky vymazať staršie ako (počet dní)','Ak je zvolená voľba <i>Automatické mazanie starých správ</i>, tak správy staršie ako zvolený počet dní budú automaticky vymazané. Minimálna hodnota je 1.','365','delete_older_then')");
	
	// Insert into admin menu
	$row = db::fetch("SELECT * FROM menus WHERE machine_name='administration'");
	$menu_id = $row['id'];
	$row = db::fetch("SELECT id FROM menu_items WHERE menu_id=$menu_id AND path='admin/modules'");
	$child_of = $row['id'];
	db::exec("INSERT INTO menu_items (title, path, allowed, expanded, priority, child_of, menu_id, name, image)
	          VALUES ('Kniha návštev', 'guestbook/admin', 1, 0, 0, $child_of, $menu_id, 'Kniha návštev', '')");
	
	// Insert into editors menu
	$row = db::fetch("SELECT * FROM menus WHERE machine_name='editors_menu'");
	$menu_id = $row['id'];
	$row = db::fetch("SELECT id FROM menu_items WHERE menu_id=$menu_id AND path='admin/modules'");
	$child_of = $row['id'];
	db::exec("INSERT INTO menu_items (title, path, allowed, expanded, priority, child_of, menu_id, name, image)
	          VALUES ('Kniha návštev', 'guestbook/admin', 1, 0, 0, $child_of, $menu_id, 'Kniha návštev', '')");
	
	$row = db::fetch("SELECT id FROM menus WHERE machine_name='primary_menu'");
	$menu_id = $row['id'];
	db::exec("INSERT INTO menu_items (title, path, allowed, expanded, priority, child_of, menu_id, name, image)
	          VALUES ('Kniha návštev', 'guestbook', 1, 0, 30, 0, $menu_id, 'Kniha návštev', '')");
	
	$row = db::fetch("SELECT id FROM menus WHERE machine_name='navigation'");
	$menu_id = $row['id'];
	db::exec("INSERT INTO menu_items (title, path, allowed, expanded, priority, child_of, menu_id, name, image)
	          VALUES ('Kniha návštev', 'guestbook', 1, 0, 30, 0, $menu_id, 'Kniha návštev', '')");
			  
	db::exec("UPDATE modules SET installed=1 WHERE machine_name='guestbook'");
	
  }
  
/***************************************** UNINSTALLATION ROUTINE ***/
  function moduleUninstall()
  {
    db::exec("DELETE FROM permisions WHERE cont_mach_name='guestbook'");
	db::exec("DROP TABLE guestbook");
	db::exec("DROP TABLE guestbook_settings");
	db::exec("DELETE FROM menu_items WHERE path='guestbook/admin'");
	db::exec("DELETE FROM menu_items WHERE path='guestbook'");
	db::exec("UPDATE modules SET installed=0 WHERE machine_name='guestbook'");
  }