<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  die("This script cannot be called directly");
}

class BackupLib extends TikiLib {
	function BackupLib($db) {
		# this is probably uneeded now
		if (!$db) {
			die ("Invalid db object passed to BAckupsLib constructor");
		}
		$this->db = $db;
	}

	function restore_database($filename) {
		// Get the password before it's too late
		$query = "select `hash` from `users_users` where `login`=?";
		$pwd = $this->getOne($query,array("admin"));

		// Before anything read tiki.sql from db and run it
		$fp = fopen("db/tiki.sql", "r");
		$data = fread($fp, filesize("db/tiki.sql"));
		fclose ($fp);
		// Drop all the tables
		preg_match_all("/DROP ([^;]+);/i", $data, $reqs);

		foreach ($reqs[0] as $query) {
			//print("q: $query<br/>");
			$result = $this->query($query);
		}

		// Create all the tables
		preg_match_all("/create table ([^;]+);/i", $data, $reqs);

		foreach ($reqs[0] as $query) {
			//print("q: $query<br/>");
			$result = $this->query($query);
		}

		$query = "update `users_users` set `hash`=? where `login`=?";
		$result = $this->query($query,array($pwd,'admin'));
		@$fp = fopen($filename, "rb");

		if (!$fp) return false;

		while (!feof($fp)) {
			$rlen = fread($fp, 4);
			if (feof($fp)) break;

			$len = unpack("L", $rlen);
			$len = array_pop($len);
			//print("leer: $len bytes<br/>");
			$line = fread($fp, $len);
			$line = $this->RC4($pwd, $line);
			// EXECUTE SQL SENTENCE HERE
			//print("q: $line <br/>");
			$result = $this->query($line,array());
		}

		fclose ($fp);
	}

	function RC4($pwd, $data) {
		$key[] = "";

		$box[] = "";
		$temp_swap = "";
		$pwd_length = 0;
		$pwd_length = strlen($pwd);

		for ($i = 0; $i <= 255; $i++) {
			$key[$i] = ord(substr($pwd, ($i % $pwd_length) + 1, 1));

			$box[$i] = $i;
		}

		$x = 0;

		for ($i = 0; $i < 255; $i++) {
			$x = ($x + $box[$i] + $key[$i]) % 256;

			$temp_swap = $box[$i];
			$box[$i] = $box[$x];
			$box[$x] = $temp_swap;
		}

		$temp = "";
		$k = "";
		$cipherby = "";
		$cipher = "";
		$a = 0;
		$j = 0;

		for ($i = 0; $i < strlen($data); $i++) {
			$a = ($a + 1) % 256;

			$j = ($j + $box[$a]) % 256;
			$temp = $box[$a];
			$box[$a] = $box[$j];
			$box[$j] = $temp;
			$k = $box[(($box[$a] + $box[$j]) % 256)];
			$cipherby = ord(substr($data, $i, 1)) ^ $k;
			$cipher .= chr($cipherby);
		}

		return $cipher;
	}

	// Functions to backup the database (mysql?)
	function backup_database($filename) {
		ini_set("max_execution_time", "3000");

		$query = "select `hash` from `users_users` where `login`=?";
		$pwd = $this->getOne($query,array("admin"));
		@$fp = fopen($filename, "w");

		if (!$fp)
			return false;

		$query = "show tables";
		$result = $this->query($query);
		$sql = '';
		$part = '';

		while ($res = $result->fetchRow()) {
			list($key, $val) = each($res);

			if (!strstr($val, 'babl')) {
				// Now dump the table
				$query2 = "select * from `$val`";

				$result2 = $this->query($query2);

				while ($res2 = $result2->fetchRow()) {
					$sentence = "values(";

					$first = 1;

					foreach ($res2 as $field => $value) {
						if ($first) {
							$sentence .= "'" . addslashes($value). "'";
							$first = 0;
							$fields = '(' . $field;
						} else {
							$sentence .= ",'" . addslashes($value). "'";
							$fields .= ",$field";
						}
					}

					$fields .= ')';
					$sentence .= ")";
					$part = "insert into $val $fields $sentence;";
					$len = pack("L", strlen($part));
					fwrite($fp, $len);
					$part = $this->RC4($pwd, $part);
					fwrite($fp, $part);
				}
			}
		}
		// And now print!
		fclose ($fp);
		return true;
	}
}

$backuplib = new BackupLib($dbTiki);

?>
