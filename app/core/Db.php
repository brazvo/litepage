<?php 
/**
 * PHP Class to create DB connection 
 *
 * Usage:
 * <code><?php
 * require('class.Db.php');
 * // direct params input
 * db::init($driver*, $database**, $host, $user, $password);
 *
 * // fluent
 * $db = db::init()->setDriver($driver*)->setDb($database**)->setHost($host)->setUser($user)->setPassword($password);
 *
 * // There are three static methods to work with database - fetch(), fetchAll(), exec().
 * // fetch() returns an array of one row from table
 * $row = db::fetch("SELECT * FROM table WHERE id = 1");
 *
 * // fetchAll() returns multidimensional array of rows witch you can use in foreach or while cycle
 * $rows = db::fetchAll("SELECT * FROM table");
 *
 * // exec() returns bool of execution INSERT, UPDATE or DELETE
 * $res = db::exec("DELETE FROM table WHERE id = 2");
 *
 * // you can send query with parameters to be replaced
 * $res = db::exec("DELETE FROM %s WHERE id = %d", $table, $id);
 *
 * ? ></code>
 * 
 * note: (*) - driver can be sqlite or mysql
 *       (**) - in case of sqlite - path to sqlite db file
 *            - in case of mysql - database name
 *
 * flags:
 * %i or %d - number
 * %s - string
 * %v - string or number value will be placed to 'value' format
 * %t - table or column name
 * %a - array of values, $key is name of column, useful in queries INSERT and UPDATE,
 *      usage: UPDATE `tablename` SET %a WHERE `id`=%i, or INSERT INTO `tablename` %a
 * 
 * ==============================================================================
 * 
 * @version $Id: class.Db.php,v 0.41 2011/05/20 $
 * @copyright Copyright (c) 2010 Branislav Zvolensky (http://www.mbmartworks.sk)
 * @author Branislav Zvolensky <zvolensky@mbmartworks.sk>
 * @license http://opensource.org/licenses/gpl-license.php GNU General Public License (GPL)
 * 
 * ==============================================================================
 *
 * Usage of this script is on own risk. The autor does not give you any warranty.
 * If you will use this script in your files you must place these header informations with the script.
 * v0.42
 * added alternative for static methods offMc() a setMcTime()
 * flag BZDB_MC_OFF for switching of Memcache
 * flags BZDB_MC_TIME_TINY (10s), BZDB_MC_TIME_SHORT (30s), BZDB_MC_TIME_MEDIUM (90s), BZDB_MC_TIME_LONG (300s)
 *       BZDB_MC_TIME_EXTRALONG (1800s), BZDB_MC_TIME_MEGALONG (18000s)
 *
 * v0.35
 * added new flag %a = array, you can send values in array and it wiil be collected into SQL string for
 * commands UPDATE and INSERT
 * v0.32
 * repaired collecting $query if clear query without argument was sent
 * 
 * v0.31
 * added getTableFields($table)
 *
 * v0.40
 * added memcache usage:
 * - added time constants for memcache
 * - added fluent methods: useMemcache($bool), setMcHost($string), setMcPort($int)
 * - into connect() method added connection to memcache
 * - added methds setMemcache($sql, $result) and getMemcache($sql) for setting and reading
 *   the datas into/from cache
 * - added methods witch should be use before fetch() and fetchAll() - setMcTime($int) (in seconds) and offMc()
 *   If you use offMc(), the Memcache will be switched off for one run of fetch() or fetchAll()
 *   If you use setMcTime(300), the Memcache will set the cache time for one run of fetch() or fetchAll(). The
 *   default time is set to 90 seconds.
 */
class db {

	// constants
        const TINY = 10;
        const SHORT = 30;
        const MEDIUM = 90;
        const LONG = 300;
        const EXTRALONG = 1800;
        const MEGALONG = 18000;

        // Properties
	/**
	 * @var string Database driver
	 */
	private static $driver;

	/**
	 * @var string Host
	 */
	private static $host;

	/**
	 * @var string Username
	 */
	private static $user;

	/**
	 * @var string Password
	 */
	private static $password;

	/**
	 * @var string Database name
	 */
	private static $database;

        /**
	 * @var string Memcache host
	 */
	private static $mcHost;

        /**
	 * @var array Memcache hosts
	 */
	private static $mcHosts;

        /**
	 * @var integer Memcache port
	 */
	private static $mcPort;

        /**
	 * @var integer Memcache time
	 */
	private static $mcTime;

        /**
	 * @var bool
	 */
	private static $offMc = false;

        /**
	 * @var bool use memcache
	 */
	private static $useMemcache;

        /**
	 * @var object $MC
	 */
        public static $MC;

        /**
	 * @var string
	 */
	private static $account;

        private static $conection;

	// Constructor
	protected function __construct() {


	}

	// Methods

	/**
	 * @title connect
	 * connect to db
	 */
	public static function connect() {
	    if(self::$driver == 'mysql') {
		self::$conection = new PDO( 'mysql:host=' . self::$host . ';dbname=' . self::$database, self::$user, self::$password, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8") );
	    }

	    if(self::$driver == 'sqlite') {
		self::$conection = new PDO( 'sqlite:' . APP_DIR . '/db/'. self::$database );
                //self::$conection = new SQLite3( APP_DIR . '/db/'. self::$database );
	    }

            if(self::$useMemcache && !self::$MC) {
                self::$MC = new Memcache();
                self::$MC->connect(self::$mcHost, self::$mcPort);
            }

	}

	/**
	 * Initialize db
	 * @param string $driver [sqlite | mysql]
	 * @param string $database ( sqlite:path to file | mysql: db name)
	 * @param string $host
	 * @param string $user
	 * @param string $password
         * @param bool $useMemcache [true|false]
         * @param string $mcHost
         * @param integer $mcPort
	 */
	public static function init()
	{
		$dbconf = Config::database();
                
                $obj = new self();

		$obj->setDriver($dbconf['driver']);
		$obj->setDb($dbconf['dbname']);
		$obj->setHost($dbconf['dbhost']);
		$obj->setUser($dbconf['dbuser']);
		$obj->setPassword($dbconf['dbpassword']);
                
                //init memcache
                if( isset( $dbconf['mc_use'] ) && $dbconf['mc_use'] ) {
                    $obj->useMemcache( true );
                    $obj->setMcHost( $dbconf['mc_host'] );
                    $obj->setMcPort( $dbconf['mc_port'] );
                }
                else {
                    $obj->useMemcache( false );
                }

                // define flags
                define('DB_MC_OFF', 'memcache_off');
                define('DB_MC_TIME_TINY', 'mctime_10');
                define('DB_MC_TIME_SHORT', 'mctime_30');
                define('DB_MC_TIME_MEDIUM', 'mctime_90');
                define('DB_MC_TIME_LONG', 'mctime_300');
                define('DB_MC_TIME_EXTRALONG', 'mctime_1800');
                define('DB_MC_TIME_MEGALONG', 'mctime_18000');
                
                self::connect();

		//return $obj;
	}

	public function setDriver($str)
	{
		self::$driver = $str;
		return $this;
	}

	public function setDb($str)
	{
		self::$database = $str;
		return $this;
	}

	public function setHost($str)
	{
		self::$host = $str;
		return $this;
	}

	public function setUser($str)
	{
		self::$user = $str;
		return $this;
	}

	public function setPassword($str)
	{
		self::$password = $str;
		return $this;
	}

        /******************************* MEM CACHE SETTERS **********/

        public function useMemcache($bool)
	{
		self::$useMemcache = $bool;
		return $this;
	}
        
        public function setMcHost($string) {
            self::$mcHost = $string;
            return $this;
        }

        public function setMcHosts($array) {
            self::$mcHosts = $array;
            return $this;
        }

        public function setMcPort($int) {
            self::$mcPort = $int;
            return $this;
        }

        public function setMcObject($obj) {
            self::$MC = $obj;
            return $this;
        }

        public static function setMcTime($int) {
            self::$mcTime = $int;
        }

        public static function offMc() {
            self::$offMc = true;
        }

        public static function flushMc() {
            if(self::$MC && self::$useMemcache) self::$MC->flush();
        }
        
        /******************************* end of MEM CACHE SETTERS****/

        /**
         * Fetches one row of sql query
         * @param string $sql
         * @return boolean|array
         */
        public static function fetch($sql)
	{
	    $obj = new self;

	    // Get arguments
	    $arg_list = func_get_args();

            $arg_list = self::checkForFlags($arg_list);

	    $sql = $obj->collectQuery($arg_list);

            $row = $obj->getMemcache($sql);

            if(!$row) {

                self::$conection->beginTransaction();

                $query = self::$conection->prepare($sql) or errorWrite('fetch QUERY: '.$sql);
                $result = $query->execute();
                if($result)
                    $row = $query->fetch(PDO::FETCH_ASSOC);
                else
                    $row = array();
                $query->closeCursor();
                unset($query);

                self::$conection->commit();

                /*
                $result = self::$conection->query($sql) or errorWrite('fetch QUERY: '.$sql);
                $row = array();
                if($result)
                    $row = $result->fetchArray(SQLITE3_ASSOC);
                else
                    $row = array();
                */
                if($row && self::$useMemcache && !self::$offMc) $obj->setMemcache ($sql, $row);
                self::$offMc = false;
            
            }

	    return $row;
	}
        
        /**
         * Fetches one row of sql query and returns first column of result
         * @param string $sql
         * @return boolean|array
         */
        public static function fetchSingle($sql)
	{
	    $obj = new self;

	    // Get arguments
	    $arg_list = func_get_args();

            $arg_list = self::checkForFlags($arg_list);

	    $sql = $obj->collectQuery($arg_list);

            $row = $obj->getMemcache("fetchSingle".$sql);

            if(!$row) {

                self::$conection->beginTransaction();

                $query = self::$conection->prepare($sql) or errorWrite('fetchSingle QUERY: '.$sql);
                $result = $query->execute();
                if($result)
                    $row = $query->fetch(PDO::FETCH_NUM);
                else
                    $row = array();
                $query->closeCursor();
                unset($query);

                self::$conection->commit();

                /*
                $result = self::$conection->query($sql) or errorWrite('fetch QUERY: '.$sql);
                $row = array();
                if($result)
                    $row = $result->fetchArray(SQLITE3_ASSOC);
                else
                    $row = array();
                */
                if($row && self::$useMemcache && !self::$offMc) $obj->setMemcache ("fetchSingle".$sql, $row);
                self::$offMc = false;
            
            }

	    return $row[0];
	}

	/**
         * Return array of rows
         * @param string $sql
         * @return boolean | array
         */
        public static function fetchAll($sql)
	{

	    $obj = new self;

	    // Get arguments
	    $arg_list = func_get_args();

            $arg_list = self::checkForFlags($arg_list);

	    $sql = $obj->collectQuery($arg_list);

            $rows = $obj->getMemcache($sql);

            if(!$rows) {

                self::$conection->beginTransaction();

                $query = self::$conection->prepare($sql) or errorWrite('fetchAll QUERY: '.$sql);
                $result = $query->execute();
                if($result)
                    $rows = $query->fetchAll(PDO::FETCH_ASSOC);
                else
                    $rows = array();
                $query->closeCursor();
                unset($query);

                self::$conection->commit();
           
                /*
                $result = self::$conection->query($sql) or errorWrite('fetchAll QUERY: '.$sql);
                $rows = array();
                if($result) {
                    while($row = $result->fetchArray(SQLITE3_ASSOC) ) {
                        $rows[] = $row;
                    }
                }
                else{
                    $rows = array();
                }
                */
                
                if($rows && self::$useMemcache && !self::$offMc) $obj->setMemcache ($sql, $rows);
                self::$offMc = false;
            
            }

	    return $rows;

	}

	public static function exec($sql)
	{
	    $obj = new self;

	    // Get arguments
	    $arg_list = func_get_args();

	    $sql = $obj->collectQuery($arg_list);

	    self::$conection->beginTransaction();

	    $query = self::$conection->prepare($sql) or errorWrite('exec QUERY: '.$sql);
	    $result = $query->execute();
	    if($result)
		$exec = true;
	    else
		$exec = false;
	    $query->closeCursor();
	    unset($query);

	    self::$conection->commit();

            /*
	    $result = self::$conection->exec($sql) or errorWrite('exec QUERY: '.$sql);
            if($result)
		$exec = true;
	    else
		$exec = false;
            */
            return $exec;

	}
	
	/**
	 * @title Collect Query
	 * @param array $arg_list
	 * @return string SQL query
	 */
	private function collectQuery($arg_list)
	{
		
		// first must be query definition
		$query = array_shift($arg_list);
                $command = substr($query, 0, 6);
		// replace the type specifiers (%s, %d, ...) with arguments
		// $query = vsprintf($query, $arg_list);
		
		
		// save all flags from query into array
		// flags have to be in the same order as arguments
		preg_match_all('/%\w+/', $query, $matches);
		// save these flags into array like /%flagg%/ string
		if($matches[0]) {
			foreach($matches[0] as $match){
				// save replace strings into array
				$replaces[] = "/$match/";
			}
			
			// check inputs
			if(count($replaces) > count($arg_list)) die('Error: There are too much flags in query: '.$query);
			if(count($replaces) < count($arg_list)) die('Error: There are too much arguments for query: '.$query);
			
			for ($i = 0; $i < count($replaces); $i++) {
				
				if($replaces[$i] == '/%v/') $arg_list[$i] = "'" . $arg_list[$i] . "'";
				if($replaces[$i] == '/%t/') $arg_list[$i] = "`" . $arg_list[$i] . "`";
                                if($replaces[$i] == '/%a/') {
                                    if(!is_array($arg_list[$i])) die('Argument for flag %a must be an array');
                                    $str = '';
                                    $count = count($arg_list[$i]);
                                    $counter = 1;
                                    $keys = "";
                                    $vals = "";
                                    foreach($arg_list[$i] as $key => $val) {
                                        if('UPDATE' == strtoupper($command)){
                                            if($count == $counter) $str .= "`$key`='$val'";
                                                else $str .= "`$key`='$val',";
                                        }
                                        if('INSERT' == strtoupper($command)){
                                            if($count == $counter){
                                                $keys .= "`$key`";
                                                $vals .= "'$val'";
                                            }
                                            else {
                                                $keys .= "`$key`,";
                                                $vals .= "'$val',";
                                            }
                                        }
                                        $counter++;
                                    }
                                    if('UPDATE' == strtoupper($command)) $arg_list[$i] = $str;
                                    if('INSERT' == strtoupper($command)) $arg_list[$i] = "($keys) VALUES ($vals)";
                                }
				
				// replace the flags with arguments
				// arguments have to be in the same order as flags
				$query = preg_replace($replaces[$i], $arg_list[$i], $query, 1);
				
			}
		}
		
                // if SQL tracker is enabled
                if(defined('SQL_BROWSER') && SQL_BROWSER) Application::setQuery ($query);
                
		return $query;

	}


        private function getMemcache($sql) {

            if(!self::$useMemcache) return false;

            $key = md5($sql);
            if (!self::$MC->getServerStatus(self::$mcHost) || !($result = self::$MC->get( $key )) ) {
                return false;
            }
            else {
                return $result;
            }
        }

        private function setMemcache($sql, $result) {

            if(!self::$mcTime) {
                $time = self::MEDIUM;
            }
            else {
                $time = self::$mcTime;
                self::$mcTime = null;
            }

            $key = md5($sql);
            if ( self::$MC->getServerStatus(self::$mcHost) ) {
		self::$MC->set( $key, $result, 0, $time );
            }
        }
        
        
        public static function getConection()
        {
            return self::$conection;
        }
	
	
	public static function getTableFields($table)
	{
		if(self::$driver == 'mysql') {
			$rows = self::fetchAll("SHOW COLUMNS FROM $table");
			foreach($rows as $row){
				$result[$row['Field']]=$row['Default'];
			}
		}
		
		if(self::$driver == 'sqlite') {
			$rows = db::fetchAll("PRAGMA table_info($table)");
			
			
			foreach($rows as $row){
			  $result[$row['name']]=$row['dflt_value'];
			}
		}
		
		return $result;
	}


        private static function checkForFlags($arg_list)
        {

            foreach ($arg_list as $key => $arg) {

                if($arg === 'memcache_off') {
                    self::offMc();
                    unset($arg_list[$key]);
                }

                if('mctime_' === substr($arg, 0 , 7)) {
                    list($a, $time) = explode('_', $arg);
                    self::setMcTime((int)$time);
                    unset($arg_list[$key]);
                }
            }

            return $arg_list;

        }
	
}