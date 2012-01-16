<?php
class RegistrationModel
{
    public function saveNew($values)
    {
        
        $newuid = db::fetchSingle("SELECT MAX(id)+1 as id FROM users");
        
		if( trim($values['email']) ==='' ) {
			$email = db::fetchSingle("SELECT `email` FROM users_registration WHERE `email` = %v", $values['email']);
		}
		else {
			$email = null;
		}

        if(!empty($email)) {
            return 'email';
        }
        
        $res1 = db::exec("INSERT INTO `users` (`id`, `user`, `password`, `role`, `name`, `surname`, `last_login`, `session_id`)
                          VALUES (%i, %v, %v, 'user', %v, %v, %v, %v)",
                          $newuid, $values['user'], md5($values['password']), $values['name'],
                          $values['surname'], date("Y-m-d H:i:s"), SESSIONID);
        $res2 = db::exec("INSERT INTO `users_registration` (`uid`, `email`, `newsletter`, `events_reminder`)
                           VALUES (%i, %v, %i, %i)", $newuid, $values['email'],
                           $values['newsletter'], $values['events_reminder']);
        
        if($res1 && $res2) return true;
            else return false;
    }
}