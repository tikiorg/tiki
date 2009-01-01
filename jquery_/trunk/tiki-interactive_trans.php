<?php 
require_once ('tiki-setup.php');
if ($prefs['lang_use_db'] != 'y') {
	$smarty->assign('msg', tra("This feature is disabled").": lang_use_db");
	$smarty->assign('error', 'y');
	$smarty->display("tiki-interactive_trans.tpl");
	die;
}

if ($tiki_p_edit_languages != 'y') {
	$smarty->assign('errortype', 401);
	$smarty->assign('msg', tra("Permission denied to use this feature"));
	$smarty->assign('error', 'y');
	$smarty->display("tiki-interactive_trans.tpl");
	die;
}


if (isset($_REQUEST['src']))	$_REQUEST['content']=$_REQUEST['src'];
$smarty->assign('analysed_word', $_REQUEST['content']);


function update_trans($add_tran_source,$add_tran_tran,$edit_language){
	global $tikilib,$smarty;
	$i=0;
	$query="select * from `tiki_language` where `lang`=? and `source` = ?";
	$result=$tikilib->query($query,array($edit_language,$add_tran_source));
	if (!$result->numRows()){
		$query = "insert into `tiki_language` values(binary ?,?,binary ? )";
		$result= $tikilib->query($query,array($add_tran_source,$edit_language,$add_tran_tran));
	}else {
		if (strlen($add_tran_tran)==0){
			$query="delete from `tiki_language` where `source`=binary ? and `lang`=?";
			$result=$tikilib->query($query,array($add_tran_source,$edit_language));
		}else{
			$query = "update `tiki_language` set `tran`=binary ? where `source`=binary ? and `lang`=?";
			$result=$tikilib->query($query,array($add_tran_tran,$add_tran_source,$edit_language));
		}
		
	}
	if (!isset($result)){
		$smarty->assign('msg', tra("Error writing in the databse: $query"));
		$smarty->assign('error', 'y');
		$smarty->display("tiki-interactive_trans.tpl");
	}	
}


function getTrans($trans,$lang){
	global $tikilib,$smarty,$entries;
	//First do the exact matching
	$query="select * from `tiki_language` where `lang`=? and (`source` = ? or `tran` = ?) order by lang";
	$result=$tikilib->query($query,array($lang,$trans,$trans));
	$compteur=0;
	$exact=false;
	while ($res = $result->fetchRow()) {
		$compteur++;	
		$exact=true;
		$entry=array();
		$entry["lang"] = $res["lang"];
		$entry["source"] = $res["source"];
		$entry["urlsource"] = urlencode($res["source"]);
		$entry["trans"] = $res["tran"]."";
		$entries[]=$entry;
	}	
	
	$query="select * from `tiki_language` where `lang`=? and (`source` like ? or `tran` like ?) order by lang";
	$result=$tikilib->query($query,array($lang,"%".$trans."%","%".$trans."%"));
	while ($res = $result->fetchRow())
		if (strlen($res["source"])!=strlen($trans)){
		$compteur++;
		$entry=array();
		$entry["lang"] = $res["lang"];
		$entry["source"] = $res["source"];
		$entry["urlsource"] = urlencode($res["source"]);
		$entry["trans"] = $res["tran"];
		$entries[]=$entry;
	}	
	
	if (!$exact){
		$compteur++;
		$entry=array();
		$entry["lang"] = $lang;
		$entry["source"] = $trans;
		$entry["urlsource"] = urlencode($trans);
		$entry["trans"] = "";
		$entries[]=$entry;
		
	}
}

function getLanguages(){
	global $tikilib;
	$query = "select `lang` from `tiki_languages`";
	$result=$tikilib->query($query);
	$languages = array();
	while ($row = $result->fetchRow())
		$languages[] = $row["lang"];
	return $languages;
}

//Update the translation
if (!isset($_REQUEST['dst']))$_REQUEST['dst']="";
if (isset($_REQUEST['src'])&&isset($_REQUEST['lang'])){
	$_REQUEST['src']=urldecode($_REQUEST['src']);
	$_REQUEST['dst']=urldecode($_REQUEST['dst']);
	$_REQUEST['src'] = htmlentities($_REQUEST['src'], ENT_NOQUOTES, "UTF-8");
	$_REQUEST['dst'] = htmlentities($_REQUEST['dst'], ENT_NOQUOTES, "UTF-8");
	update_trans( $_REQUEST['src'],$_REQUEST['dst'],$_REQUEST['lang']);
	$smarty->assign('update', 'y');
	$smarty->display("tiki-interactive_trans.tpl");
	die;
}

//Main windows 
$languages= getLanguages();
$entries=array();
foreach ($languages as $key => $value)
	getTrans(urldecode($_REQUEST['content']),$value);

$smarty->assign('languages', $languages);
$smarty->assign('entries', $entries);
$smarty->display("tiki-interactive_trans.tpl");
?>
