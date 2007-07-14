<?php
//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
    header("location: index.php");
    exit;
}

class TinvoiceLib extends TikiLib {
    /*private*/ var $prefs=NULL;
    /*private*/ var $userid;

    /*protected*/ function TinvoiceLib($db) {
	global $user, $userlib;
	parent::TikiLib($db);
	$this->userid=$userlib->get_user_id($user);
    }

    /*debug
    function query($a, $b=array()) {
	echo "<p>$a {{{ ".implode(', ', $b)." }}}</p>";
	return parent::query($a,$b);
    }
    */
    /*public*/ function get_invoice($id) {
	$query = "select * from `tiki_tinvoice` where `id`=?";
	$result = $this->query($query, array((int)$id));
	if ($res = $result->fetchRow()) {
		return new Tinvoice($this, $res);
	} else {
	    return NULL;
	}
    }

    /*public*/ function new_invoice() {
	return new Tinvoice($this);
    }

    /* all parameters are optional by pair */
    /*public*/ function list_invoices($id_emitter=NULL, $idtype_emitter=NULL,
				      $id_receiver=NULL, $idtype_receiver=NULL) {

	$aquery=array();
	if (($id_emitter > 0) && ($idtype_emitter !== NULL)) {
	    $aquery['id_emitter']=(int)$id_emitter;
	    $aquery['idtype_emitter']=$idtype_emitter;
	}
	if (($id_receiver > 0) && ($idtype_receiver !== NULL)) {
	    $aquery['id_receiver']=(int)$id_receiver;
	    $aquery['idtype_receiver']=$idtype_receiver;
	}
	
	$a="";
	$b=array();
	foreach($aquery as $k => $v) {
		$a.=($a == '' ? 'WHERE ' : ' AND ')."`$k`=?";
		$b[]=$v;
	}
	$query = "select * from `tiki_tinvoice` $a";
	$result = $this->query($query, $b);


	$result = $this->query($query, $b);
	while ($res = $result->fetchRow()) {	    
		$ret[]=new Tinvoice($this, $res);
	}
	return $ret;
    }
   /*public*/ function list_banks($userId) {

	$query = "select * from `tiki_tinvoice_banks` where `userId`=?";
	$result = $this->query($query, array((int)$userId));
	while ($res = $result->fetchRow()) {	    
		$ret[]=$res;
	}
	return $ret;
    }
    /*public*/ function get_bank($id) {
	$query = "select * from `tiki_tinvoice_banks` where `id`=?";
	$result = $this->query($query, array((int)$id));
	if ($res = $result->fetchRow()) {
		$ret=$res;
	}
	return $ret;
    }
    /*public*/ function get_transaction($id) {
	$query = "select * from `tiki_tinvoice_bank_transactions` where `id`=?";
	$result = $this->query($query, array((int)$id));
	if ($res = $result->fetchRow()) {
		$ret=$res;
	}
	return $ret;
    }
    /*public*/ function drop_bank($id) {
	$query = "delete from `tiki_tinvoice_banks` where `id`=?";
	$result = $this->query($query, array((int)$id));
	return $result;
    }
    /*public*/ function drop_transaction($id) {
	$query = "delete from `tiki_tinvoice_bank_transactions` where `id`=?";
	$result = $this->query($query, array((int)$id));
	return $result;
    }
    /*public*/ function list_transactions($userId) {

	$query = "select * from `tiki_tinvoice_bank_transactions` where `userId`=?";
	$result = $this->query($query, array((int)$userId));
	while ($res = $result->fetchRow()) {	    
		$ret[]=$res;
	}
	return $ret;
    }
    /*public*/ function update_bank($userId, $bankId, $data) {

	if (!$bankId) {
		// add new bank
		$query ="INSERT into `tiki_tinvoice_banks` (`userId`, `name`, `bank`, `account_nb`, `rib`, `swift`) VALUES (?, ?, ?, ?, ?, ?)";	
		$result = $this->query($query, array((int)$userId,
							$data["name"],
							$data["bank"],
							$data["account_nb"],
							$data["rib"],
							$data["swift"]));
		$bankId=$this->getOne("SELECT `id` from `tiki_tinvoice_banks` where `userId`=? and `account_nb`=?",array((int)userId,$data["account_nb"]));
	} else {
		// update bank account
		$query="UPDATE `tiki_tinvoice_banks` SET `name`=? , `bank`=? , `account_nb`=?, `rib`=?, `swift`=? WHERE `userId`=? AND `id`=?";
		$result = $this->query($query, array($data["name"],
							$data["bank"],
							$data["account_nb"],
							$data["rib"],
							$data["swift"],
							(int)$userId,
							(int)$bankId));

	}
	return $bankId;
    }
    /*public*/ function update_transaction($userId, $tId, $data) {

	if (!$tId) {
		// add new transaction
		$query ="INSERT into `tiki_tinvoice_bank_transactions` (`userId`, `bankId`, `date`, `operationNb`, `label`, `debit`, `credit`, `status`) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";	
		$result = $this->query($query, array((int)$userId,
							(int)$data["bankId"],
							$data["date"],
							$data["operation_nb"],
							$data["label"],
							$data["debit"],
							$data["credit"],
							$data["status"]));
		$tId=$this->getOne("SELECT `id` from `tiki_tinvoice_bank_transactions` where `userId`=? and `operationNb`=?",array((int)userId,$data["operation_nb"]));
	} else {
		// update transaction
		$query="UPDATE `tiki_tinvoice_bank_transactions` SET `bankId`=? , `date`=? , `operationNb`=?, `label`=?, `debit`=? , `credit`=? , `status`=? WHERE `userId`=? AND `id`=?";
		$result = $this->query($query, array($data["bankId"],
							$data["date"],
							$data["operation_nb"],
							$data["label"],
							$data["debit"],
							$data["credit"],
							$data["status"],
							(int)$userId,
							(int)$tId));

	}
	return $tId;
    }
    /*public*/ function get_period_dates($todate, $graphPeriod ) {
    	$period=array();
	if ($graphPeriod == "week") {
		$weekDay=date('w',$todate);
		$vd=24*60*60;
		for ($i=0; $i<7; $i++) {
			$k0=($i - $weekDay);
			$tdate= $todate + ($k0*$vd);
			$period[$i]['date']=$tdate;
		}
	} else if ($graphPeriod == "month") {
		$vd=24*60*60;
		$m = TikiLib::date_format("%m", $todate);
		$y = TikiLib::date_format("%Y", $todate);
		// get first day of the month
		$firstday=mktime(0,0,0,$m,1,$y);
		//get last day 
		$lastday=mktime(0,0,0,$m+1,0,$y);
		$lastdayNb=TikiLib::date_format("%d", $lastday);
		for ($i=0; $i<=($lastdayNb-1); $i++) {
			$tdate= $firstday + $i*$vd;
			$period[$i]['date']=$tdate;
		}
	} else if ($graphPeriod == "trimester") {
		// remove this later 
		$xtype="day";
		// set trimester dates 
		$m = TikiLib::date_format("%m", $todate);
		$y = TikiLib::date_format("%Y", $todate);
		$T1F= mktime(0,0,0,1,1,$y);
		$T1L= mktime(0,0,0,4,0,$y);
		$T2F= mktime(0,0,0,4,1,$y);
		$T2L =mktime(0,0,0,7,0,$y);
		$T3F= mktime(0,0,0,7,1,$y);
		$T3L= mktime(0,0,0,10,0,$y);
		$T4F= mktime(0,0,0,10,1,$y);
		$T4L= mktime(0,0,0,0,0,$y+1);

		// get trimester dates 
		if ($T1F <= $todate && $T1L >= $todate) {
			$t=1;
			$TF=$T1F;
			$TL=$T1L;
		} else if ($T2F <= $todate && $T2L >= $todate) {
			$t=2;
			$TF=$T2F;
			$TL=$T2L;
		} else if ($T3F <= $todate && $T3L >= $todate) {
			$t=3;
			$TF=$T3F;
			$TL=$T3L;
		} else if ($T4F <= $todate && $T4L >= $todate) {
			$t=4;
			$TF=$T4F;
			$TL=$T4L;
		}

		if ($xtype=="day") {
			$vd=24*60*60;
			$nf= TikiLib::date_format("%j", $TF);
			$nl= TikiLib::date_format("%j", $TL);
			$maxdays=($nl-$nf);
			for ($i=0; $i<=$maxdays; $i++) {
				$tdate= $TF + $i*$vd;
				$period[$i]['date']=$tdate;
			}
		}
	} else {
		$period=FALSE;	
	}
	return $period;
}
    /*public*/ function extract_Invoices($id_emitter=NULL, $idtype_emitter=NULL,
    				      $id_receiver=NULL, $idtype_receiver=NULL,
    				      $todate=NULL,
    				      $period,
    				      $sort_mode="date_asc") {
    
    	$aquery=array();
    	if (($id_emitter > 0) && ($idtype_emitter !== NULL)) {
    	    $aquery['id_emitter']=(int)$id_emitter;
    	    $aquery['idtype_emitter']=$idtype_emitter;
    	}
    	if (($id_receiver > 0) && ($idtype_receiver !== NULL)) {
    	    $aquery['id_receiver']=(int)$id_receiver;
    	    $aquery['idtype_receiver']=$idtype_receiver;
    	}
    	
    	$a="";
    	$b=array();
    	foreach($aquery as $k => $v) {
    		$a.=($a == '' ? 'WHERE ' : ' AND ')."`$k`=?";
    		$b[]=$v;
    	}
    	if (($todate > 0) && ($todate !== NULL)) {
    	    $max= count($period)-1;
    	    $TF= TikiLib::date_format("%Y-%m-%d 00:00:00", $period[0]['date']); 
    	    $TL= TikiLib::date_format("%Y-%m-%d 23:59:59", $period[$max]['date']); 
    	    $b= array($TF,$TL);
    	    $a.="WHERE `date`  >= ? AND `date` <= ? GROUP BY `date` ORDER BY `date` asc";}
    	$query = "select `date`, sum(`amount`) from `tiki_tinvoice` $a";
    	$result = $this->query($query, $b);
    	while ($res = $result->fetchRow()) {	    
    		$ret[]=new Tinvoice($this, $res);
    	}
    	return $ret;
    }
    /*private*/ function init_prefs() {
	$prefs=array();
	$result=$this->query("SELECT * from tiki_tinvoice_prefs WHERE `userId`=?",
			     array($this->userid));
	while ($res = $result->fetchRow()) {
	    $prefs[$res['key']]=$res['value'];
	}
	$this->prefs=$prefs;
    }

    /*public*/ function set_pref($key, $value) {
	$this->query("DELETE FROM `tiki_tinvoice_prefs` WHERE `userId`=? AND `key`=?",
		     array($this->userid, $key));
	if (($value !== NULL) && (strlen($value) > 0)) {
	    $this->query("INSERT INTO `tiki_tinvoice_prefs` (`userId`, `key`, `value`) VALUES (?, ?, ?)",
			 array($this->userid, $key, $value));
	    if ($this->prefs !== NULL) $this->prefs[$key]=$value;
	} else {
	    if ($this->prefs !== NULL) unset($this->prefs[$key]);
	}
    }

    /*public*/ function get_pref($key) {
	if ($this->prefs === NULL) $this->init_prefs();
	if (isset($this->prefs[$key]))
	    return $this->prefs[$key];
	else
	    return NULL;
    }

    /*public*/ function get_pref_emitter_rib() {
	return tinvoiceLib::fromdb_emitter_rib($this->get_pref("emitter_rib"));
    }
    
    /*public*/ function set_pref_emitter_rib($domiciliation, $code_banque, $code_guichet, $numero_compte, $cle_rib,
					     $iban, $bic) {
	$this->set_pref("emitter_rib",
			tinvoiceLib::todb_emitter_rib($domiciliation, $code_banque, $code_guichet,
						      $numero_compte, $cle_rib, $iban, $bic));
    }

    /*protected static*/ function fromdb_emitter_rib($value) {
	if ($value === NULL) return NULL;
	$rib=explode("\x01", $value);
	return array("domiciliation" => $rib[0],
		     "code_banque" => $rib[1],
		     "code_guichet" => $rib[2],
		     "numero_compte" => $rib[3],
		     "cle_rib" => $rib[4],
		     "iban" => $rib[5],
		     "bic" => $rib[6]);
    }
    
    /*protected static*/ function todb_emitter_rib($domiciliation, $code_banque, $code_guichet, $numero_compte, $cle_rib,
					$iban, $bic) {
	return "$domiciliation\x01$code_banque\x01$code_guichet\x01$numero_compte\x01$cle_rib\x01$iban\x01$bic";
    }
}

class Tinvoice {
    /*private*/ var $tinvoicelib;
    /*private*/ var $id_invoice;
    /*private*/ var $invoice;
    /*private*/ var $infos;
    /*private*/ var $lines;

    /*protected*/ function Tinvoice($tinvoicelib, $row=NULL) {
	global $user, $userlib;

	$this->tinvoicelib=$tinvoicelib;

	if (!is_null($row)) {
	    $invoice=array();
	    foreach($row as $k => $v) {
		$invoice[$k]['value']=$v;
		$invoice[$k]['_state']='clean';
	    }
	    $this->invoice=$invoice;
	    $this->loadInvoice((int)$row['id']);
	} else {
	    $this->invoice=array('id_emitter' => array('value' => $userlib->get_user_id($user), '_state' => 'updated'),
				 'idtype_emitter' => array('value' => 'tiki', '_state' => 'updated'));
	    $this->infos=array();
	    $this->lines=array();
	    $this->id_invoice=0;
	}

    }

    /*private*/ function loadInvoice($id_invoice) {
	$infos=array();
	$query = "select * from `tiki_tinvoice_infos` where `id_invoice`=?";
	$result = $this->tinvoicelib->query($query, array((int)$id_invoice));
	while ($res = $result->fetchRow()) {
	    $infos[$res['type']]=array('_state' => 'clean', 'value' => $res['value']);
	}
	$items=array();
	$query = "select * from `tiki_tinvoice_items` where `id_invoice`=? order by id";
	$result = $this->tinvoicelib->query($query, array((int)$id_invoice));
	while ($res = $result->fetchRow()) {
	    $res['_state']='clean';
	    $items[]=$res;
	}

	$this->id_invoice=$id_invoice;
	$this->infos=$infos;
	$this->lines=$items;
    }

    /*public*/ function delete() {
	if ($this->id_invoice > 0) {
	    $this->tinvoicelib->query("delete from tiki_tinvoice_items where `id_invoice`=?", array($this->id_invoice));
	    $this->tinvoicelib->query("delete from tiki_tinvoice_infos where `id_invoice`=?", array($this->id_invoice));
	    $this->tinvoicelib->query("delete from tiki_tinvoice where `id`=?", array($this->id_invoice));
	    $this->id_invoice=0;
	}
    }

    /*public*/ function commit() {
	if ($this->id_invoice == 0) { // create a new invoice
	    $this->tinvoicelib->query("INSERT INTO tiki_tinvoice () values ()");
	    $this->id_invoice=$this->tinvoicelib->getOne("SELECT LAST_INSERT_ID()"); // this is specific to mysql, but tikidb don't seem to have a generic method for this (!)
	}

	// invoice (inline)
	foreach($this->invoice as $k => $info) {
	    switch($info['_state']) {
	    case 'clean':
		break;
	    case 'updated':
		$this->tinvoicelib->query("UPDATE tiki_tinvoice SET `$k`=? WHERE id=?",
					  array($info['value'], $this->id_invoice));
		$this->invoice[$k]['_state']='clean';
		break;
	    }
	}

	// infos
	foreach($this->infos as $k => $info) {
	    switch($info['_state']) {
	    case 'clean':
		break;
	    case 'removed':
		$this->tinvoicelib->query("DELETE FROM tiki_tinvoice_infos WHERE id_invoice=? AND type=?",
					  array($this->id_invoice, $k));
		unset($this->infos[$k]);
		break;
	    case 'new':
		if ($info['value'] !== NULL)
		    $this->tinvoicelib->query("INSERT INTO tiki_tinvoice_infos (id_invoice, type, value) VALUES (?, ?, ?)",
					      array($this->id_invoice, $k, $info['value']));
		$this->infos[$k]['_state']='clean';
		break;
	    case 'updated':
		if (($info['value'] === NULL) || ($info['value'] === ''))
		    $this->tinvoicelib->query("DELETE FROM tiki_tinvoice_infos WHERE id_invoice=? AND type=?",
					      array($this->id_invoice, $k));
		else
		    $this->tinvoicelib->query("UPDATE tiki_tinvoice_infos SET value=? WHERE id_invoice=? AND type=?",
					      array($info['value'], $this->id_invoice, $k));
		$this->infos[$k]['_state']='clean';
		break;
	    }
	}

	// lines
	foreach($this->lines as $k => $line) {
	    switch($line['_state']) {
	    case 'clean':
		break;
	    case 'removed':
		$this->tinvoicelib->query("DELETE FROM tiki_tinvoice_items WHERE id=? AND id_invoice=?",
					  array($line['id'], $this->id_invoice));
		unset($this->lines[$k]);
		break;
	    case 'new':
		$this->tinvoicelib->query("INSERT INTO tiki_tinvoice_items (id_invoice, ref, designation, vat, quantity, unitprice)".
					  " VALUES (?, ?, ?, ?, ?, ?)",
					  array($this->id_invoice, $line['ref'], $line['designation'], $line['vat'], $line['quantity'], $line['unitprice']));
		$this->lines[$k]['id']=$this->tinvoicelib->getOne("SELECT LAST_INSERT_ID()");
		$this->lines[$k]['_state']='clean';
		break;
	    case 'updated':
		$this->tinvoicelib->query("UPDATE tiki_tinvoice_items SET ref=?, designation=?, vat=?, quantity=?, unitprice=?".
					  " WHERE id=? AND id_invoice=?",
					  array($line['ref'], $line['designation'], $line['vat'], $line['quantity'], $line['unitprice'], $line['id'], $this->id_invoice));
		$this->lines[$k]['_state']='clean';
		break;
	    }
	}
    }
    
    /*public*/ function get_id() {
	return $this->id_invoice;
    }

    /*public*/ function add_line($ref, $designation, $vat, $quantity, $unitprice) {
	$this->lines[]=array('_state' => 'new',
			     'ref' => $ref, 'designation' => $designation, 'vat' => $vat, 'quantity' => $quantity, 'unitprice' => $unitprice);
	$this->resync_amount();
    }

    /*public*/ function update_line($id, $ref, $designation, $vat, $quantity, $unitprice) {
	foreach($this->lines as $k => $line) {
	    if (isset($line['id']) && ($line['id'] == $id)) {
		$this->lines[$k]=array('_state' => 'updated', 'id' => $id,
				       'ref' => $ref, 'designation' => $designation, 'vat' => $vat, 'quantity' => $quantity, 'unitprice' => $unitprice);
		break;
	    }
	}
	$this->resync_amount();
    }

    /*public*/ function remove_line($id) {
	foreach($this->lines as $k => $line) {
	    if (isset($line['id']) && ($line['id'] == $id)) {
		$this->lines[$k]['_state']='removed';
		break;
	    }
	}
	$this->resync_amount();
    }

    /*private*/ function resync_amount() {
	$lines=$this->get_lines();
	$amount=0.00;
	$amount_vat=0.00;
	foreach($lines as $line) {
	    $amount+=$line['quantity']*$line['unitprice'];
	    $amount_vat+=$line['quantity']*$line['unitprice']*($line['vat']/100.0);
	}
	$this->set_inline_info('amount', $amount);
	$this->set_inline_info('amount_vat', $amount_vat);
    }

    /*public*/ function get_lines() {
	$lines=array();
	foreach($this->lines as $line) {
	    if ($line['_state'] == 'removed') continue;
	    unset($line['_state']);
	    $lines[]=$line;
	}
	return $lines;
    }

    /*private*/ function set_inline_info($type, $value) {
	$this->invoice[$type]=array('value' => $value, '_state' => 'updated');
    }

    /*private*/ function get_inline_info($type) {
	if (isset($this->invoice[$type])) return $this->invoice[$type]['value'];
	else return NULL;
    }

    /*private*/ function set_info($type, $value) {
	$state='bug';
	if (isset($this->infos[$type])) {
	    if ($value === NULL) {
		$state=$this->infos[$type]['_state'];
		if ($state === 'new') {
		    unset($this->infos[$type]);
		    return;
		} else $state='removed';
	    } else {
		$state=$this->infos[$type]['_state'];
		if ($state == 'clean') $state='updated';
		else if ($state == 'removed') $state='updated';
	    }
	} else {
	    if ($value === NULL) return;
	    $state='new';
	}
	$this->infos[$type]=array('_state' => $state, 'value' => $value);
    }

    /*private*/ function get_info($type) {
	if (isset($this->infos[$type])) {
	    if ($this->infos[$type]['_state'] == 'removed') {
		return NULL;
	    } else {
		return $this->infos[$type]['value'];
	    }
	} else {
	    return NULL;
	}
    }

    /*public*/ function get_ref() {
	return $this->get_inline_info("ref");
    }

    /*public*/ function set_ref($ref) {
	$this->set_inline_info("ref", $ref);
    }

    /*public*/ function set_emitter($id_emitter, $idtype) {
	$this->set_inline_info("id_emitter", $id_emitter);
	$this->set_inline_info("idtype_emitter", $idtype);
    }

    /*public*/ function get_emitter() {
	return array('id_emitter' => $this->get_inline_info('id_emitter'),
		     'idtype_emitter' => $this->get_inline_info('idtype_emitter'));
    }

    /*public*/ function set_receiver($id_receiver, $idtype) {
	$this->set_inline_info("id_receiver", $id_receiver);
	$this->set_inline_info("idtype_receiver", $idtype);
    }

    /*public*/ function get_receiver() {
	return array('id_receiver' => $this->get_inline_info('id_receiver'),
		     'idtype_receiver' => $this->get_inline_info('idtype_receiver'));
    }

    /*public*/ function get_amount() {
	return $this->get_inline_info("amount");
    }

    /*public*/ function get_amount_vat() {
	return $this->get_inline_info("amount_vat");
    }

    /*public*/ function get_status() {
	return $this->get_inline_info("status");
    }

    /*public*/ function set_status($status) {
	$oldstatus=$this->get_inline_info("status");
	if (($oldstatus == 'draft') && ($status == 'emitted')) { // create a new invoice number

	    // TODO: verify that the invoice date is equal or after the last emitted invoice.

	    $ts=$this->get_date_as_timestamp();
	    
	    // update new annual_id
	    $query = "select max(annual_id) as theid from `tiki_tinvoice` where `date` > ?";
	    $result = $this->tinvoicelib->query($query, array(date("Y")+"-01-01 00:00:00", $ts));
	    $res = $result->fetchRow();
	    $annual_id=$res['theid'] + 1;
	    $this->set_inline_info('annual_id', $annual_id);

	    // make a new invoice number (nice graphical art isn't it?)
	    $format=$this->tinvoicelib->get_pref('numberingformat');
	    $ref=strftime($format, $ts);
	    $ref=str_replace('#########', sprintf('%09d', $annual_id));
	    $ref=str_replace('########', sprintf('%08d', $annual_id));
	    $ref=str_replace('#######', sprintf('%07d', $annual_id));
	    $ref=str_replace('######', sprintf('%06d', $annual_id));
	    $ref=str_replace('#####', sprintf('%05d', $annual_id));
	    $ref=str_replace('####', sprintf('%04d', $annual_id));
	    $ref=str_replace('###', sprintf('%03d', $annual_id));
	    $ref=str_replace('##', sprintf('%02d', $annual_id));
	    $ref=str_replace('#', sprintf('%01d', $annual_id));
	    $this->set_inline_info("ref", $ref);
	}
	
	$this->set_inline_info("status", $status);
    }

    /* curious, buggy thing ! */
    /*public*/ function get_sum_amount() {
	return $this->get_inline_info("sum(`amount`)");
    }

    /*public*/ function get_paid() {
	return $this->get_inline_info("paid");
    }

    /*public*/ function set_paid($paid) {
	return $this->set_inline_info("paid", $paid);
    }

    /*public*/ function get_libelle() {
	return $this->get_inline_info("libelle");
    }

    /*public*/ function set_libelle($libelle) {
	$this->set_inline_info("libelle", $libelle);
    }
    
    /*public*/ function get_date() {
	return $this->get_inline_info("date");
    }
    /*public*/ function get_date_as_timestamp() {
    	$a= $this->get_inline_info("date");
    	$t=explode(" ",$a);
    	$t1=explode("-",$t[0]);
    	$t2=explode(":",$t[1]);
    	$ta= mktime($t2[0],$t2[1],$t2[2],$t1[1],$t1[2],$t1[0]);
    	return $ta;
    }

    /*public*/ function set_date($date) {
	$this->set_inline_info("date", $date);
    }

    /*public*/ function get_datelimit() {
	return $this->get_info("datelimit");
    }

    /*public*/ function set_datelimit($datelimit) {
	$this->set_info("datelimit", $datelimit);
    }

    /*public*/ function get_refdevis() {
	return $this->get_info("refdevis");
    }
    
    /*public*/ function set_refdevis($refdevis) {
	$this->set_info("refdevis", $refdevis);
    }
    
    /*public*/ function get_refbondecommande() {
	return $this->get_info("refbondecommande");
    }

    /*public*/ function set_refbondecommande($refbondecommande) {
	$this->set_info("refbondecommande", $refbondecommande);
    }

    /*public*/ function get_emitter_tvanumber() {
	return $this->get_info("emitter_tvanumber");
    }

    /*public*/ function set_emitter_tvanumber($tvanumber) {
	$this->set_info("emitter_tvanumber", $tvanumber);
    }

    /*public*/ function get_receiver_tvanumber() {
	return $this->get_info("receiver_tvanumber");
    }

    /*public*/ function set_receiver_tvanumber($tvanumber) {
	$this->set_info("receiver_tvanumber", $tvanumber);
    }

    /*public*/ function get_emitter_address() {
	return $this->get_info("emitter_address");
    }

    /*public*/ function set_emitter_address($address) {
	$this->set_info("emitter_address", $address);
    }

    /*public*/ function get_receiver_address() {
	return $this->get_info("receiver_address");
    }

    /*public*/ function set_receiver_address($address) {
	$this->set_info("receiver_address", $address);
    }

    /*public*/ function get_emitter_rib() {
	return tinvoiceLib::fromdb_emitter_rib($this->get_info("emitter_rib"));
    }

    /*public*/ function set_emitter_rib($domiciliation, $code_banque, $code_guichet, $numero_compte, $cle_rib,
					$iban, $bic) {
	$this->set_info(tinvoiceLib::fromdb_emitter_rib($domiciliation, $code_banque, $code_guichet,
						       $numero_compte, $cle_rib, $iban, $bic));
    }

    /*public*/ function get_paymode($paymodes) {
	return explode("\x01", $this->get_info("paymode"));
    }

    /*public*/ function set_paymode($paymodes) {
	$this->set_info("paymode", implode("\x01", $paymodes));
    }

    /*public*/ function get_acomptes() {
	$value=$this->get_info("acomptes");
	if ($value === NULL) return NULL;
	$result=array();
	$acomptes=explode("\x01", $value);
	foreach($acomptes as $acompte) {
	    $tmp=explode("@", $acompte);
	    $result[]=array("somme" => $tmp[0], "date" => $tmp[1]);
	}
	return $result;
    }

    /*public*/ function add_acompte($somme, $date) {
	$acomptes=explode("\x01", $this->get_info("acomptes"));
	$acomptes[]="$somme@$date";
	$this->set_info("acomptes", implode("\x01", $acomptes));
    }

    /*public*/ function get_footer() {
	return $this->get_info("footer");
    }

    /*public*/ function set_footer($foooter) {
	$this->set_info("footer", $footer);
    }

    /*public*/ function get_image() {
	$line=$this->get_info("image");
	if ($image === NULL) return NULL;
	$datas=explode("\x01", $image);
	return array("x" => $datas[0],
		     "y" => $datas[1],
		     "w" => $datas[2],
		     "h" => $datas[3],
		     "url" => $datas[4]);
    }

    /*public*/ function set_image($x, $y, $w, $h, $url) {
	$this->set_info("image", "$x\x01$y\x01$w\x01$h\x01$url");
    }

}

?>
