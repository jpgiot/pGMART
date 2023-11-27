 <?php
/**
 * Simple auth class based on work tiny-auth 
 *
 * Tiny safety session based authorization and authentication class
 * Developed by Andrey Gadyukov, 2012
 * http://www.phpclasses.org/tiny-auth
 * initially released in LGPL
 *
 * Author: Jean-Philippe Giot jp@giot.net
 * License: LGPL
 */
 
/**
 * table users has fields 
 *	user_id			internal user id integer,
 *	user_login		login string short without spaces,
 * 	user_hash		hash build with password and 
 * 	user_name		most common user name
 *
 * table roles has fields 
 *	role_id			internal integer,
 * 	role_user_id	user id matching user table,
 *	role_role_name 	associated role (such as 'admin', 'moderator')
 */ 
 
class AuthPDOException extends ErrorException {
}
	
class AuthPDO {

	// old hard coded way. switched to dynamically assigned salts
    //CONST PRE_SALT = 'dEmoS75AUNT?';
    //CONST POST_SALT = 'plOD@pAR14';

    // salting password hash. first one required
    public $pre_salt = null;
	public $post_salt = null;

	// database instance
    public $pdo = null;
	
	// if tables need to be prefixed
	public $table_prefix = "";
	
	// tables containing users
	public $table_users = "users";
	
	// table containing roles
	public $table_roles = "roles";
	

    function __construct() {
    }

	
	/**
	 * generates a hash with one or better two salts
	 * 
	 * posted on php.net hash reference 
	 * kevin at bionichippo dot com
	 */
	public function HashDoubleSalt($login,$toHash){
		// checking that we have at least one salt
		if (is_null($this->pre_salt)){
			throw new AuthPDOException('Missing hashing salt', $errno, 0, __FILE__, __LINE__);
		}
		// spliting password in two
		$password_parts = str_split($toHash,(strlen($toHash)/2)+1);
		
		// hashing with whirlpool
		// double salted in application wide parameters
		// salted with login to add user specific salt
		$hash = hash(
			'whirlpool', 
			$this->pre_salt.
			$password_parts[0].
			$this->post_salt.
			$password_parts[1].
			$login);
		
		// returning hash. with current tehcnologies password cannot be guessed 
		// by analyzing the hash and is safe to be stored in database
		
		return $hash;
	} 	

    /**
     * @param str $login
     * @param str $pass
     */
    public function login($login, $pass) {
		// will perform a auth test based on provided data from user
		// will be protected by the prepared statement in auth function
        $auth = $this->auth($login, $pass);
		
		// checking auth status
        if ($auth != false) {
            $_SESSION['login'] = $login . ',' . md5(md5(uniqid()) . $login . md5(uniqid()));
            $_SESSION['name'] = $auth->user_names;
            // $_SESSION['role_id'] = $auth['role_id'];
            // $_SESSION['role_name'] = $this -> getRole($auth['role_id']);
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param str $login
     * @param str $pass
     */
     
    private function auth($login, $pass) {
		
		// preparing a salted hash of the password
        $saltedPass = $this->HashDoubleSalt($login, $pass);
		
		// preparing request with table name and prefix
		$sql = sprintf(
			'SELECT '.
			'%1$s.user_name,%1$s.user_login '.
			'FROM '.
			'%1$s '.
			'WHERE '.
			'%1$s.user_login=:login '.
			'AND %1$s.user_hash=:hash ',
			$this->table_prefix.$this->table_users);
		//die($sql);
	
		// prepared statement
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':login', $login, PDO::PARAM_STR);
        $stmt->bindValue(':hash', $saltedPass, PDO::PARAM_STR);
        $stmt->execute();
		
		$result = $stmt -> fetchAll(PDO::FETCH_OBJ);
        
		if (empty($result)) {
			// no user matching these parameters
            return false;
        } elseif (count($result) > 1) {
			throw new AuthPDOException('System failure, impossible to log user', $errno, 0, __FILE__, __LINE__);
		} else {
			// returning user name and login as object
            return $result[0];
        }
    }

	
    /**
     * Getting authorization status
     * @return bool true - logined; false - logouted
     */
    public function getAuthStatus() {
        return isset($_SESSION['login']) ? true : false;
    }
    
    /**
     * Getting user name
     * @return str logined usr name
     */
    public function getName() {
        return isset($_SESSION['name']) ? $_SESSION['name'] : false;
    }	

    /**
     * Destroys all data registered to a session
     */
    public function logout() {
        session_destroy();
    }

    /**
     * @param int $id role id
     * @return id and role name
     */
	 /*
    public function getRole($id = null) {
		
        $this->PDO prepare('SELECT
                                    name
                                FROM
                                    roles
                                WHERE id = :id');
        if (isset($_SESSION['role_id'])) {
            $sth -> bindValue(':id', $_SESSION['role_id'], PDO::PARAM_INT);
        } else {
            $sth -> bindValue(':id', $_SESSION['role_id'], $id);
        }
        $sth -> execute();
        return $sth -> fetchAll(PDO::FETCH_ASSOC)[0];
    }
	*/
    
	function createTables()
	{
		$sql = sprintf(
			'CREATE TABLE IF NOT EXISTS `%1$s%2$s` ('.
			'`user_id` int(11) NOT NULL AUTO_INCREMENT,'.
			'`user_login` varchar(20) CHARACTER SET utf8 NOT NULL,'.
			'`user_hash` varchar(128) CHARACTER SET utf8 NOT NULL,'.
			'`user_name` varchar(200) CHARACTER SET utf8 NOT NULL,'.
			'PRIMARY KEY (`user_id`),'.
			'UNIQUE KEY `user_login` (`user_login`)'.
			') ENGINE=MyISAM DEFAULT CHARSET=utf8 '.
			'COLLATE=utf8_unicode_ci AUTO_INCREMENT=1;',
			$this->table_prefix,
			$this->table_users);
		
		// echo $sql."<br />\n";
        $stmt = $this->pdo->prepare($sql);
        if (!$stmt->execute())
			throw new AuthPDOException('Failed creating user table', $errno, 0, __FILE__, __LINE__);
			
		$sql = sprintf(
			'CREATE TABLE IF NOT EXISTS `%1$s%2$s` ('.
			'`role_id` int(11) NOT NULL AUTO_INCREMENT,'.
			'`user_id` int(11) NOT NULL,'.
			'`role_name` char(20) CHARACTER SET utf8 NOT NULL,'.
			'PRIMARY KEY (`role_id`)'.
			') ENGINE=MyISAM DEFAULT CHARSET=utf8 '.
			'COLLATE=utf8_unicode_ci AUTO_INCREMENT=1;',
			$this->table_prefix,
			$this->table_roles);
			
		//echo $sql."<br />\n";
        $stmt = $this->pdo->prepare($sql);
        if (!$stmt->execute())
			throw new AuthPDOException('Failed creating role table', $errno, 0, __FILE__, __LINE__);		
		
		return true;
	}
	
	function userExists($user_login){
		
		$sql = sprintf(
			'SELECT COUNT(*) FROM `%1$s%2$s` WHERE user_login=:login;',
			$this->table_prefix,
			$this->table_users);
			
		$stmt = $this->pdo->prepare($sql);
		$stmt->bindValue(':login', $user_login, PDO::PARAM_STR);	
		
		if ($this->pdo->query("SELECT FOUND_ROWS()")->fetchColumn() == 0)
			return false;
		else
			return true;
	}
	
	function createUser($user_login,$password,$user_name){
	
		// you can code here the policy you want to control the creation of new users
	
		// checking that a valid user is logged
		//if (!$this->getAuthStatus())
		//	throw new AuthPDOException('Impossible to create new user', $errno, 0, __FILE__, __LINE__);
			
		$saltedPass = $this->HashDoubleSalt($user_login, $password);
		$sql = sprintf(
			'INSERT INTO `%1$s%2$s` '.
			'(`user_login`, `user_hash`, `user_name`) '.
			'VALUES (:login, :hash, :username);',
			$this->table_prefix,
			$this->table_users);
			
		//echo $sql."<br />\n";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':login', $user_login, PDO::PARAM_STR);
        $stmt->bindValue(':hash', $saltedPass, PDO::PARAM_STR);		
        $stmt->bindValue(':username', $user_name, PDO::PARAM_STR);		
        if ($stmt->execute())
			return true;	
			
		throw new AuthPDOException('Failed creating new user', $errno, 0, __FILE__, __LINE__);
	}
	
	function updatePassword($user_login,$oldpassword,$newpassword){
		
		$oldsaltedPass = $this->HashDoubleSalt($user_login, $oldpassword);
		$newsaltedPass = $this->HashDoubleSalt($user_login, $newpassword);
		
		/*
		
		$sql = sprintf(
			'INSERT INTO `pgmart`.`pgmart_users` '.
			'(`user_login`, `user_hash`, `user_name`) '.
			'VALUES (:login, :hash, :username);',
			$this->table_prefix,
			$this->table_users);
			
		//echo $sql."<br />\n";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':login', $user_login, PDO::PARAM_STR);
        $stmt->bindValue(':pass', $saltedPass, PDO::PARAM_STR);		
        $stmt->bindValue(':username', $user_name, PDO::PARAM_STR);		
        if ($stmt->execute())
			return true;	
			
		throw new AuthPDOException('Failed creating new user', $errno, 0, __FILE__, __LINE__);
		*/
	}
}
