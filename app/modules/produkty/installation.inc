<?php
/***************************************** INSTALLATION ROUTINE ***/
  function moduleInstall()
  {
    // create tables
	$sql= "CREATE TABLE IF NOT EXISTS products_settings 
	          (`id` INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
			   `title` VARCHAR(128),
			   `constant` VARCHAR(50),
			   `value` VARCHAR(50))";
	// if mysql replace some strings
	if(DB_DRIVER == 'mysql') $sql = sqliteToMysql($sql);
	db::exec($sql);
	
	$sql = "CREATE TABLE IF NOT EXISTS products
	          (`id` INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
			   `category_id` INTEGER,
			   `name` VARCHAR(128),
			   `description` TEXT,
			   `attributes` TEXT,
			   `image` VARCHAR(128),
			   `price` DOUBLE(10,2),
			   `vat` REAL,
			   `new` BOOL,
			   `inaction` BOOL,
			   `discount` REAL,
			   `priority` INTEGER)";
	// if mysql replace some strings
	if(DB_DRIVER == 'mysql') $sql = sqliteToMysql($sql);
	db::exec($sql);
	
	// insert records to tables
	db::exec("INSERT INTO `products_settings` VALUES ('1','Zobrazovať cenu','DISPLAY_PRICE','0')");
	db::exec("INSERT INTO `products_settings` VALUES ('2','Sadzby DPH','VATS','10:19')");
	db::exec("INSERT INTO `products_settings` VALUES ('3','Ceny budú zadávané s DPH','PRICES_WITH_VAT','0')");
	db::exec("INSERT INTO `products_settings` VALUES ('4','Sme platcami DPH','VAT_PAYER','0')");
	db::exec("INSERT INTO `products_settings` VALUES ('5','Zobraziť cenu len prihlásenému užívateľovi','PRICES_FOR_LOGGED_ONLY','0')");
	db::exec("INSERT INTO `products_settings` VALUES ('6','Veľkosť obrázku produktu šírka','IMAGE_SIZE','640')");
	db::exec("INSERT INTO `products_settings` VALUES ('7','Veľkosť ikony produktu šírka','THUMB_SIZE','175')");
	db::exec("INSERT INTO `products_settings` VALUES ('8','Pri nahratí obrázku sa má ikona:','THUMB_CREATE','ratio')");
	db::exec("INSERT INTO `products_settings` VALUES ('9','Zoradenie produktov v kategóriach katalógu:','ORDER_BY','name')");
	
	// create menu
	db::exec("INSERT INTO `menus` (`name`, `machine_name`, `system`) VALUES ('Produkty','produkty','1')");
	
	// Insert into admin menu
	$row = db::fetch("SELECT * FROM menus WHERE machine_name='administration'");
	$menu_id = $row['id'];
	$row = db::fetch("SELECT id FROM menu_items WHERE menu_id=$menu_id AND path='admin/modules'");
	$child_of = $row['id'];
	db::exec("INSERT INTO menu_items (title, path, allowed, expanded, priority, child_of, menu_id, name, image)
	          VALUES ('Produkty', 'produkty/admin', 1, 0, 0, $child_of, $menu_id, 'Produkty', '')");
			  
	// Insert into editors menu
	$row = db::fetch("SELECT * FROM menus WHERE machine_name='editors_menu'");
	$menu_id = $row['id'];
	$row = db::fetch("SELECT id FROM menu_items WHERE menu_id=$menu_id AND path='admin/modules'");
	$child_of = $row['id'];
	db::exec("INSERT INTO menu_items (title, path, allowed, expanded, priority, child_of, menu_id, name, image)
	          VALUES ('Produkty', 'produkty/admin', 1, 0, 0, $child_of, $menu_id, 'Produkty', '')");
	
	// create products subdirectory
	if(!is_dir('images/products')){
	  $res = @mkdir('images/products',0777);
	  if(!$res){
	    echo 'Vytvorenie adresara products v adresary images nebolo mozne. Pre spravny chod modulu je potrebne vytvorit v adresary images podadresar products a nastavit mu prava na 777.';
	  }
	}
	
	// set permisions
	db::exec("INSERT INTO permisions (content, cont_mach_name, editor_view, editor_add, editor_edit, editor_delete, user_view, user_add, user_edit, user_delete, visitor_view, visitor_add, visitor_edit, visitor_delete)
	                                 VALUES ('Produkty', 'produkty', 1,1,1,1,1,0,0,0,1,0,0,0)");
	
	// update modules table
	db::exec("UPDATE modules SET installed=1 WHERE machine_name='produkty'");
  };
  
/***************************************** UNINSTALLATION ROUTINE ***/
  function moduleUninstall()
  {
    db::exec("UPDATE modules SET installed=0 WHERE machine_name='produkty'");
	db::exec("DELETE FROM permisions WHERE cont_mach_name='produkty'");
	db::exec("DROP TABLE products");
	db::exec("DROP TABLE products_settings");
	db::exec("DELETE FROM menu_items WHERE path='produkty/admin'");
	$row = db::fetch("SELECT id FROM menus WHERE machine_name='produkty'");
	$menu_id = $row['id'];
	db::exec("DELETE FROM menu_items WHERE menu_id=$menu_id");
	db::exec("DELETE FROM menus WHERE machine_name='produkty'");
  }