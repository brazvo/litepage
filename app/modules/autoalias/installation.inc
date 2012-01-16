<?php
/***************************************** INSTALLATION ROUTINE ***/
  function moduleInstall()
  {
    	db::exec("INSERT INTO permisions (content, cont_mach_name, editor_view, editor_add, editor_edit, editor_delete, user_view, user_add, user_edit, user_delete, visitor_view, visitor_add, visitor_edit, visitor_delete)
	                                 VALUES ('Autoaliasy', 'autoalias', 1,1,1,1,1,0,0,0,1,0,0,0)");
        $sql = "CREATE TABLE `autoalias_setting` (
	          `id` INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
		  `cid` INTEGER,
		  `autoalias_on` BOOL)";
	// if mysql replace some strings
	if(DB_DRIVER == 'mysql') $sql = sqliteToMysql($sql);
	db::exec($sql);
        
        // create trigger
        $sql = "CREATE TRIGGER delete_autoalias_setting
                AFTER DELETE ON content
                FOR EACH ROW
                BEGIN 
                DELETE FROM autoalias_setting WHERE cid = old.id;
                END";
        if(DB_DRIVER == 'mysql') {
            $sql = sqliteToMysql($sql);
        }
        db::exec($sql);
			  
	db::exec("UPDATE modules SET installed=1 WHERE machine_name='autoalias'");
	
  }
  
/***************************************** UNINSTALLATION ROUTINE ***/
  function moduleUninstall()
  {
        db::exec("DELETE FROM permisions WHERE cont_mach_name='autoalias'");
	db::exec("DROP TABLE autoalias_setting");
        db::exec("DROP TRIGGER delete_autoalias_setting");
	db::exec("UPDATE modules SET installed=0 WHERE machine_name='autoalias'");
  }