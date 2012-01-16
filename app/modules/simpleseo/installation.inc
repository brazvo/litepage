<?php
/***************************************** INSTALLATION ROUTINE ***/
  function moduleInstall()
  {
    	db::exec("INSERT INTO permisions (content, cont_mach_name, editor_view, editor_add, editor_edit, editor_delete, user_view, user_add, user_edit, user_delete, visitor_view, visitor_add, visitor_edit, visitor_delete)
	                                 VALUES ('Simpleseo', 'simpleseo', 1,1,1,1,1,0,0,0,1,0,0,0)");
        $sql = "CREATE TABLE `content_seo` (
	          `id` INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
		  `cid` INTEGER,
		  `keywords` VARCHAR(256),
		  `description` VARCHAR(256),
		  `robots` VARCHAR(128))";
	// if mysql replace some strings
	if(DB_DRIVER == 'mysql') $sql = sqliteToMysql($sql);
	db::exec($sql);
        
        // create trigger
        $sql = "CREATE TRIGGER delete_content_seo
                AFTER DELETE ON content
                FOR EACH ROW
                BEGIN 
                DELETE FROM content_seo WHERE cid = old.id;
                END";
        if(DB_DRIVER == 'mysql') {
            $sql = sqliteToMysql($sql);
        }
        db::exec($sql);
			  
	db::exec("UPDATE modules SET installed=1 WHERE machine_name='simpleseo'");
	
  }
  
/***************************************** UNINSTALLATION ROUTINE ***/
  function moduleUninstall()
  {
        db::exec("DELETE FROM permisions WHERE cont_mach_name='simpleseo'");
	db::exec("DROP TABLE content_seo");
        db::exec("DROP TRIGGER delete_content_seo");
	db::exec("UPDATE modules SET installed=0 WHERE machine_name='simpleseo'");
  }