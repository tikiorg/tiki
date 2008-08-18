<?php

/* from 
   http://www.spiration.co.uk/post/1333/PHP 5 sessions in mysql database with PDO db objects
*/

class Session {
	public $db;
	// public $maxlifetime = get_cfg_var("session.gc_maxlifetime");
	public $maxlifetime = 1800; /* 30 mins */
	public $expiry;

	public function __construct($dbTiki){
		$this->db = $dbTiki;
	}

	public function __destruct(){
		session_write_close();
	}

	public function open( $path, $name ) {
		$this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		return true;
	}

	public function close() {
		return true;
	}

	public function read($sesskey){
		$qry = "select data from sessions where sesskey = '$sesskey' and expiry > " . time();
		$sth = $this->db->prepare($qry);
		$sth->execute();
		$result = $sth->fetch(PDO::FETCH_ASSOC);
		return $result['data'];
	}

	public function write($sesskey, $data){
		$this->expiry = time() + $this->maxlifetime;
		try {
			$qry= "insert into sessions (sesskey, data, expiry) values('$sesskey', '$data', $this->expiry)";
			$sth = $this->db->prepare($qry);
			$sth->execute();
		} catch (PDOException $e) {
			$qry= "update sessions set data='$data', expiry=$this->expiry where sesskey='$sesskey'";
			$sth = $this->db->prepare($qry);

			$sth->execute();
		}
	}

	public function destroy($sesskey){
		$qry = "delete from sessions where sesskey ='$sesskey'";
		$sth = $this->db->prepare($qry);
		$tot= $sth->execute();
		return ($tot);
	}

	public function gc($maxlifetime){
		$qry = "delete from sessions where expiry < ".time();
		$sth = $this->db->prepare($qry);
		$tot= $sth->execute();
		return ($tot);
	}
}

$session = new Session($dbTiki);
ini_set('session.save_handler','user');
session_set_save_handler(
		array(&$session, "open"),
		array(&$session, "close"),
		array(&$session, "read"),
		array(&$session, "write"),
		array(&$session, "destroy"),
		array(&$session, "gc")
		);

