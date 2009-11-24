<?php

/* from 
   http://www.spiration.co.uk/post/1333/PHP 5 sessions in mysql database with PDO db objects
*/

class Session {
	public $db;
	// public $maxlifetime = get_cfg_var("session.gc_maxlifetime");
	public $maxlifetime = 1800; /* 30 mins */
	public $expiry;

	public function __destruct(){
		session_write_close();
	}

	public function open( $path, $name ) {
		return true;
	}

	public function close() {
		return true;
	}

	public function read($sesskey){
		$qry = "select data from sessions where sesskey = '$sesskey' and expiry > " . time();
		$sth = TikiDb::get()->query($qry);
		$result = $sth->fetchRow();
		return $result['data'];
	}

	public function write($sesskey, $data){
		$this->expiry = time() + $this->maxlifetime;
		try {
			$qry= "insert into sessions (sesskey, data, expiry) values( ?, ?, ? )";
			TikiDb::get()->query($qry, array( $sesskey, $data, $this->expiry ) );
		} catch (PDOException $e) {
			$qry= "update sessions set data=?, expiry=? where sesskey=?";
			TikiDb::get()->query($qry, array( $data, $this->expiry, $sesskey ) );
		}
	}

	public function destroy($sesskey){
		$qry = "delete from sessions where sesskey = ?";
		TikiDb::get()->query($qry, array( $sesskey ) );
		return 1;
	}

	public function gc($maxlifetime){
		$qry = "delete from sessions where expiry < ?";
		TikiDb::get()->query($qry, array( time() ) );
		return 1;
	}
}

$session = new Session;
ini_set('session.save_handler','user');
session_set_save_handler(
		array($session, "open"),
		array($session, "close"),
		array($session, "read"),
		array($session, "write"),
		array($session, "destroy"),
		array($session, "gc")
		);

