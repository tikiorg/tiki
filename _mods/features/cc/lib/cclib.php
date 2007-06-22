<?php
//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

class CcLib extends UsersLib {

	var $db;
	var $date;
	var $msg = '';

	function CcLib($dbTiki) { 
		$this->db = $dbTiki;
		$this->date = date("U");
		$this->logfile = "mods/features/cc/cc.log.php";
		$this->initlog();
	}

	function initlog() {
		if (!is_file($this->logfile)) {
			$fp = fopen($this->logfile,'w');
			if ($fp) {
				fputs($fp,"<? header('Location: index.php'); die(); ?>\n");
				fclose($fp);
			}
		}
	}

	function tracklog($msg) {
		$fp = fopen($this->logfile,'a');
		fputs($fp,$msg."\n");
		fclose($fp);
	}

	function user_infos($user,$app=false) {
		$info = $this->get_user_info($user);
		$info['registered_cc'] = $this->get_registered_cc($user,$app);
		return $info;
	}

	function get_ledgers($offset=0,$max=-1,$sort_mode='last_tr_date_desc',$user=false,$cc=false,$app=false) {
		$query = 'select * from `cc_ledger`';
		$query_cant = 'select count(*) from `cc_ledger`';
		$bindvars = $mid = array();
		if ($user) {
			$mid[] = "`acct_id`=?";
			$bindvars[] = $user;
		}
		if ($cc) {
			$mid[] = "`cc_id`=?";
			$bindvars[] = $cc;
		}
		if ($app) {
			$mid[] = "`approved`=?";
			$bindvars[] = $app;
		}
		if (count($mid)) {
			$mid = " where ". implode(' and ',$mid);
		} else {
			$mid = '';
		}
		$order = " order by ".$this->convert_sortmode($sort_mode);
		$result = $this->query($query.$mid.$order,$bindvars,$max,$offset);	
		$cant = $this->getOne($query_cant.$mid,$bindvars);
		$ret = array();
		while ($res = $result->fetchRow()) {
			$res['age'] = $this->date - $res['last_tr_date'];
			$ret[] = $res;
		}
		$retval = array();
		$retval['data'] = $ret;
		$retval['cant'] = $cant;
		return $retval;
	}

	function get_transactions($offset=0,$max=-1,$sort_mode='tr_date_desc',$find='',$user=false,$cc=false) {
		$query = 'select * from `cc_transaction`';
		$query_cant = 'select count(*) from `cc_transaction`';
		$bindvars = $mid = array();
		if ($find) {
			$mid[] = "`item`=?";
			$bindvars[] = '%'. $find .'%';
		}
		if ($user) {
			$mid[] = "`acct_id`=?";
			$bindvars[] = $user;
		}
		if ($cc) {
			$mid[] = "`cc_id`=?";
			$bindvars[] = $cc;
		}
		$order = " order by ".$this->convert_sortmode($sort_mode);
		if (count($mid)) {
			$mid = " where ". implode(' and ',$mid);
		} else {
			$mid = '';
		}
		$result = $this->query($query.$mid.$order,$bindvars,$max,$offset);	
		$cant = $this->getOne($query_cant.$mid,$bindvars);
		$ret = array();
		while ($res = $result->fetchRow()) {
			$res['age'] = $this->date - $res['tr_date'];
			$ret[] = $res;
		}
		$retval = array();
		$retval['data'] = $ret;
		$retval['cant'] = $cant;
		return $retval;
	}

	function get_currencies($all=false,$offset=0,$max=-1,$sort_mode='cc_name_asc',$find='',$owner=false,$app=false,$reg=false) {
		$bindvars = $mid = array();
		if ($reg) {
			$query = "select *,count(*) as population from `cc_ledger` as ccl left join `cc_cc` as cc on ccl.`cc_id`=cc.`id` left join `cc_ledger` as cclc on cc.`id`=cclc.`cc_id` ";
			$query.= " where ccl.`acct_id`=? group by cclc.`cc_id` order by ";
			$query.= $this->convert_sortmode($sort_mode);
			$query_cant = "select count(*) from `cc_ledger` as ccl  where ccl.`acct_id`=?";
			$bindvars[] = $reg;
			$result = $this->query($query,$bindvars,$max,$offset);	
			$cant = $this->getOne($query_cant,$bindvars);
		} else {
			$query = 'select cc.*,count(*) as population from `cc_cc` as cc left join `cc_ledger` as ccl on cc.`id`=ccl.`cc_id`';
			$query_cant = 'select count(*) from `cc_cc` as cc left join `cc_ledger` as ccl on cc.`id`=ccl.`cc_id`';
			if ($find) {
				$mid[] = "cc.`cc_name`=?";
				$bindvars[] = '%'. $find .'%';
			}
			if ($owner) {
				$mid[] = "cc.`owner_id`=?";
				$bindvars[] = $owner;
			}
			if (!$all) {
				$mid[] = "cc.`listed`=?";
				$bindvars[] = 'y';
			}
			if ($app) {
				$mid[] = "ccl.`approved`=?";
				$bindvars[] = 'y';
			}
			$order = " group by cc_id order by ";
			$order.= $this->convert_sortmode($sort_mode);
			if (count($mid)) {
				$mid = " where ". implode(' and ',$mid);
			} else {
				$mid = '';
			}
			$result = $this->query($query.$mid.$order,$bindvars,$max,$offset);	
			$cant = $this->getOne($query_cant.$mid,$bindvars);
		}
		$ret = array();
		while ($res = $result->fetchRow()) {
			$ret["{$res['id']}"] = $res;
		}
		$retval = array();
		$retval['data'] = $ret;
		$retval['cant'] = $cant;
		return $retval;
	}

	function is_currency($id,$cpun='') {
		return $this->getOne('select count(*) from `cc_cc` where `id`=? and `cpun`=?',array($id,$cpun));
	}

	function replace_currency($owner,$id,$name,$description,$approval='n',$listed='y',$seq='',$cpun='') {
		global $user;
		if ($seq) {
			$query = "update `cc_cc` set `cc_name`=?,`cc_description`=?,`owner_id`=?,`requires_approval`=?,`listed`=?,`cpun`=? where `seq`=?";
			$this->query($query,array($name,$description,$owner,$approval,$listed,$cpun,$seq));
			$this->tracklog("$user changed $id");
			return true;
		} else {
			if ($this->is_currency($id,$cpun)) {
				$this->msg = "That currency already exists.";
				return false;
			} else {
				$query = "insert into `cc_cc`(`id`,`cc_name`,`cc_description`,`owner_id`,`requires_approval`,`listed`,`cpun`) values(?,?,?,?,?,?,?)";
				$this->query($query,array($id,$name,$description,$owner,$approval,$listed,$cpun));
				return true;
			}
		}
	}

	function get_currency($cc) {
		if (!$this->getOne("select count(*) from `cc_cc` where `id`=?",array($cc))) {
			return false;
		}
		$query = "select * from `cc_cc` where `id`=?";
		$result = $this->query($query,array($cc));
		return $result->fetchRow();
	}

	function get_ledger($cc,$user,$app='y') {
		if (!$this->getOne("select count(*) from `cc_ledger` where `acct_id`=? and `cc_id`=? and `approved`=?",array($user,$cc,$app))) {
			return false;
		}
		$query = "select * from `cc_ledger` where `acct_id`=? and `cc_id`=? and `approved`=?";
		$result = $this->query($query,array($user,$cc,$app));
		return $result->fetchRow();
	}

	function is_moderated($cc) {
		return $this->getOne("select `requires_approval` from `cc_cc` where `id`=?",array($cc));
	}

	function get_registered_cc($user,$app=false) {
		$query = "select * from `cc_ledger` left join `cc_cc` on `cc_ledger`.cc_id=`cc_cc`.id where `acct_id`=?";
		$bindvars = array($user);
		if ($app) {
			$bindvars[] = $app;
			$query.= " and `approved`=?";
		}
		$result = $this->query($query,$bindvars);
		$ret = array();
		while ($res = $result->fetchRow()) {
			$ret["{$res['cc_id']}"] = $res;
		}
		return $ret;
	}

	function is_registered($user,$cc,$app='y') {
		$back = $this->getOne("select count(*) from `cc_ledger` where `acct_id`=? and `cc_id`=? and `approved`=?",array($user,$cc,$app));
		return $back;
	}

	function update_ledger($cc,$type,$user,$amount,$ledger=false,$date=false) {
		if (!$ledger) {
			$ledger = $this->get_ledger($cc,$user);
		}
		if ($ledger and $amount) {
			if (!$date) $date = $this->date;
			$balance = $ledger['balance'] + $amount;
			if ($type == 'record') $tr_total = $ledger['tr_total'] + abs($amount);
			if ($type == 'revert') 	$tr_total = $ledger['tr_total'] - abs($amount);
			$tr_count = $ledger['tr_count'] + 1;
			$query = "update `cc_ledger` set `balance`=?,`tr_total`=?,`tr_count`=?,`last_tr_date`=? where `acct_id`=? and `cc_id`=? and `approved`=?";
			$this->query($query,array($balance,$tr_total,$tr_count,$date,$user,$cc,'y'));
			return true;
		} else {
			return false;
		}
	}

	function register_cc($cc,$user) {
		if ($this->is_registered($user,$cc)) {
			$this->msg = "User $user is already registered to $cc";
			return false;
		} elseif ($this->is_registered($user,$cc,'n')) {
			$this->msg = "User $user is waiting for approval for registration to $cc";
			return false;
		} elseif ($this->is_registered($user,$cc,'c')) {
			$approval = $this->is_moderated($cc);
			if ($approval == 'y') {
				$approved = 'n';
			} else {
				$approved = 'y';
			}
			$query = "update `cc_ledger` set `approved`=? where `acct_id`=? and `cc_id`=?";
			$this->query($query,array($approved,$user,$cc));
			return true;
		} else {
			$query = "insert into `cc_ledger`(`acct_id`,`cc_id`,`last_tr_date`,`approved`) values(?,?,?,?)";
			$approval = $this->is_moderated($cc);
			if ($approval == 'y') {
				$approved = 'n';
			} else {
				$approved = 'y';
			}
			$this->query($query,array($user,$cc,$this->date,$approved));
			$last = $this->getOne("select max(seq) from `cc_ledger`");
			return $last;
		}
	}
	
	function unregister_cc($cc,$user) {
		$query = "update `cc_ledger` set `approved`=? where `acct_id`=? and `cc_id`=?";
		$this->query($query,array('c',$user,$cc));
	}

	function moderate_cc($cc,$user,$app) {
		$query = "update `cc_ledger` set `approved`=? where `acct_id`=? and `cc_id`=?";
		$this->query($query,array($app,$user,$cc));
	}

	function record_transaction($cc,$type,$from_user,$to_user,$amount,$item,$date=false) {
		if (!$date) $date = $this->date;
		$from_ledger = $this->get_ledger($cc,$from_user);
		$to_ledger = $this->get_ledger($cc,$to_user);
		$query = "insert into `cc_transaction`(`tr_date`,`acct_id`,`other_id`,`cc_id`,`amount`,`item`,`balance`)";
		$query.= " values(?,?,?,?,?,?,?)";
		if ($type == 'record') {
			$result = $this->query($query,array($date,$from_user,$to_user,$cc,-$amount,$item,$from_ledger['balance']-$amount));
			$result = $this->query($query,array($date,$to_user,$from_user,$cc,$amount,$item,$to_ledger['balance']+$amount));
			$this->update_ledger($cc,$type,$from_user,-$amount,$from_ledger,$date);
			$this->update_ledger($cc,$type,$to_user,$amount,$to_ledger,$date);
		}
		if ($type == 'revert') {
			$result = $this->query($query,array($date,$from_user,$to_user,$cc,$amount,$item,$from_ledger['balance']+$amount));
			$result = $this->query($query,array($date,$to_user,$from_user,$cc,-$amount,$item,$to_ledger['balance']-$amount));
			$this->update_ledger($cc,$type,$from_user,$amount,$from_ledger,$date);
			$this->update_ledger($cc,$type,$to_user,-$amount,$to_ledger,$date);
		}
	}

	function decsv($str) {
		$ar = split('","',substr($str,1,-1));
	}

	function list_providers($url,$refresh=false) {
		global $cc_cpun;
		if ($refresh) {
			if (!checkdnsrr($url)) {
				$this->msg = sprintf(tra('DNS check failed for %s'),$url);
			} else {
				$fp = @ fsockopen($url, 80, $errno, $errstr, 30);	
				if (!$fp) {
					$this->msg = sprintf(tra('Request failed for %s'),$url)."<br />".$errstr;
				} else {
					$req = "GET /ccsp.txt HTTP/1.1\r\rHost: $url\r\nConnection: Close\r\n";
					fwrite($fp,$req);
					while (!feof($fp)) {
						$pr[] = split(',',fgets($fp,1024));
						if ($pr[0] != $cc_cpun and count($pr) == 3) {
							$pr[3] = time();
							$pr[4] = 'y';
							$data[2] = preg_replace(array('/:/','/\//'),array('@','.'),$data[2]);
							$prov["{$pr[0]}"] = $pr;
						}
					}
					fclose($fp);
					$result = $this->query('select `cpun`,`email`,`url`,`lastupdate`,`status` from `cc_providers`',array());
					while ($res = $result->fetchRow()) {
						if (isset($prov["{$res['cpun']}"])) {
							$dr = $prov["{$res['cpun']}"];
							$query = "update `cc_providers` set `url`=?,`email`=?,`lastupdate`=?,`status`=? where `cpun`=?";
							$this->query($query,array($dr[1],$dr[2],$dr[3],'y',$dr[0]));
							$prov["{$res['cpun']}"] = array();
						} else {
							$query = "update `cc_providers` set `status`=?,`lastupdate`=? where `cpun`=?";
							$this->query($query,array('n',$now,$res['cpun']));
						}
					}
					foreach ($prov as $proo) {
						if (count($prov)) {
							$query = "insert into `cc_providers`(`cpun`,`email`,`url`,`lastupdate`,`status`) values (?,?,?,?,?)";
							$this->query($query,$prov);
						}
					}
					$allp = array();
					foreach ($prov as $p) {
						$fp = @ fsockopen($p[1], 80, $errno, $errstr, 30);
						if ($fp) {
							$req = "GET /ccsp.php HTTP/1.1\r\rHost: ".$p[1]."\r\nConnection: Close\r\n";
							fwrite($fp,$req);
							while (!feof($fp)) {
								$a = split('","',substr(fgets($fp,1025),1,-1));
								$this->replace_currency($a[0],$a[1],$a[2],stripslashes($a[3]),$a[4],'y',false,$p[0]);
							}
							fclose($fp);
						}
					}
				}
			}
			if (!empty($allp) && count($allp)) {
				foreach ($allp as $url=>$data) {
					$data[2] = preg_replace(array('/:/','/\//'),array('@','.'),$data[2]);
				}
			}
		}
		$result = $this->query('select `cpun`,`email`,`url`,`lastupdate`,`status` from `cc_providers`',array());
		$ret = array();
		while ($res = $result->fetchRow()) {
			$ret[] = $res;
		}
		return $ret;
	}
	
}

$cclib = new CcLib($dbTiki);

?>
