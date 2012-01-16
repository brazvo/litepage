<?php
class UserModel
{
    public function find($vid)
    {
        return db::fetch("SELECT `us`.`id`, `us`.`name`, `us`.`surname`,
                         `ur`.`isstaff`, `ur`.`staff_position`,
                         `ur`.`profile`, `ur`.`staff_comment`,
						 `usp`.`staff_desc`
                         FROM `users` `us`
                         LEFT JOIN `users_registration` `ur` ON `ur`.`uid` = `us`.`id`
						 LEFT JOIN `users_staff_positions` `usp` ON `usp`.`staff_position` = `ur`.`staff_position`
                         WHERE `us`.`user` = %v", $vid);
    }
    
    
    public function findBySession()
    {
        return db::fetch("SELECT `us`.`id`, `us`.`name`, `us`.`surname`,
                         `ur`.`email`, `ur`.`profile`, `ur`.`newsletter`, `ur`.`events_reminder`
                         FROM `users` `us`
                         LEFT JOIN `users_registration` `ur` ON `ur`.`uid` = `us`.`id`
                         WHERE `us`.`session_id` = %v", SESSIONID);
    }
    
    
    public function save($values)
    {
        $uid = db::fetchSingle("SELECT `id` FROM `users` WHERE `session_id` = %v", SESSIONID);
        
        $email = db::fetchSingle("SELECT `email` FROM `users_registration` WHERE `uid` = %i", $uid);
        
        if($email === $values['email'] && $email !== $values['oldemail']) return false;
        
        $res = db::exec("UPDATE users SET `name`=%v, `surname`=%v WHERE `session_id`=%v",
                        $values['name'], $values['surname'], SESSIONID);
        
        if($res) {
            unset($values['name']); unset($values['surname']); unset($values['oldemail']);
            db::exec("UPDATE users_registration SET %a WHERE `uid`=%i", $values, $uid);
        }
        
        return true;
        
    }
}