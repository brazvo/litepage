<?php
/***************************************** INSTALLATION ROUTINE ***/
  function moduleInstall()
  {
    	db::exec("INSERT INTO permisions (content, cont_mach_name, editor_view, editor_add, editor_edit, editor_delete, user_view, user_add, user_edit, user_delete, visitor_view, visitor_add, visitor_edit, visitor_delete)
	                                 VALUES ('Ratings And Views', 'ratingsandviews', 1,0,0,0,1,0,0,0,1,0,0,0)");
        $sql = "CREATE TABLE `ratingsandviews_settings` (
	           `constant` VARCHAR(128),
			   `title` VARCHAR(128),
			   `description` VARCHAR(128),
			   `value` TEXT,
			   `frm_name` VARCHAR(128))";
		
		// if mysql replace some strings
		if(DB_DRIVER == 'mysql') $sql = sqliteToMysql($sql);
		db::exec($sql);
		
		db::exec("INSERT INTO `ratingsandviews_settings` VALUES('RAV_RATINGS_AFFECTED_CONTENT_TYPES', 'RAV_RATINGS_AFFECTED_CT_LABEL', 'RAV_RATINGS_AFFECTED_CT_DESC', '', 'ratings_affected')");
		db::exec("INSERT INTO `ratingsandviews_settings` VALUES('RAV_VIEWS_AFFECTED_CONTENT_TYPES', 'RAV_VIEWS_AFFECTED_CT_LABEL', 'RAV_VIEWS_AFFECTED_CT_DESC', '', 'views_affected')");
		db::exec("INSERT INTO `ratingsandviews_settings` VALUES('RAV_RATINGS_GENERAL_ON', 'RAV_RATINGS_GENERAL_ON_LABEL', 'RAV_RATINGS_GENERAL_ON_DESC', '1', 'ratings_on')");
		db::exec("INSERT INTO `ratingsandviews_settings` VALUES('RAV_VIEWS_GENERAL_ON', 'RAV_VIEWS_GENERAL_ON_LABEL', 'RAV_VIEWS_GENERAL_ON_DESC', '1', 'views_on')");
		
		$sql = "CREATE TABLE `ratingsandviews` (
	           `id` INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
			   `cid` INTEGER,
			   `views` INTEGER DEFAULT 0,
			   `rate_amount` INTEGER DEFAULT 0,
			   `raters` INTEGER DEFAULT 0,
			   `rating_on` BOOL DEFAULT 1,
			   `view_on` BOOL DEFAULT 1)";
		
		// if mysql replace some strings
		if(DB_DRIVER == 'mysql') $sql = sqliteToMysql($sql);
		db::exec($sql);
        
        // create trigger
        $sql = "CREATE TRIGGER delete_ratingsandviews
                AFTER DELETE ON content
                FOR EACH ROW
                BEGIN 
                DELETE FROM ratingsandviews WHERE cid = old.id;
                END";
        if(DB_DRIVER == 'mysql') {
            $sql = sqliteToMysql($sql);
        }
        db::exec($sql);
			  
		db::exec("UPDATE modules SET installed=1 WHERE machine_name='ratingsandviews'");
		
		// Insert into admin menu
		$row = db::fetch("SELECT * FROM menus WHERE machine_name='administration'");
		$menu_id = $row['id'];
		$row = db::fetch("SELECT id FROM menu_items WHERE menu_id=$menu_id AND path='admin/modules'");
		$child_of = $row['id'];
		db::exec("INSERT INTO menu_items (title, path, allowed, expanded, priority, child_of, menu_id, name, image)
	          VALUES ('Ratings and Views', 'ratingsandviews', 1, 0, 0, $child_of, $menu_id, 'Ratings and Views', '')");
	
  }
  
/***************************************** UNINSTALLATION ROUTINE ***/
  function moduleUninstall()
  {
        db::exec("DELETE FROM permisions WHERE cont_mach_name='ratingsandviews'");
		db::exec("DROP TABLE `ratingsandviews_settings`");
		db::exec("DROP TABLE `ratingsandviews`");
		db::exec("DELETE FROM menu_items WHERE path='ratingsandviews'");
        db::exec("DROP TRIGGER delete_ratingsandviews");
		db::exec("UPDATE modules SET installed=0 WHERE machine_name='ratingsandviews'");
  }