<?php
// Initialization

require_once('tiki-setup.php');

if($lang_use_db != 'y') {
  $smarty->assign('msg',tra("This feature is disabled"));
  $smarty->display("styles/$style_base/error.tpl");
  die;  
}

if($tiki_p_edit_languages != 'y') {
  $smarty->assign('msg',tra("Permission denied to use this feature"));
  $smarty->display("styles/$style_base/error.tpl");
  die;  
}


$query="select lang from tiki_languages";
$result=$tikilib->query($query);
$languages=Array();
while ($res=$result->fetchRow()) {
  $languages[]=$res["0"];
}
$smarty->assign_by_ref('languages',$languages);


// Get availible languages from Disk
$languages_files=Array();
$h=opendir("lang/");
while($file=readdir($h)) {
  if($file!='.' && $file!='..' && is_dir('lang/'.$file) && strlen($file)==2) {
    $languages_files[]=$file;
  }
}
closedir($h);
$smarty->assign_by_ref('languages_files',$languages_files);

// Save Variables
if (isset($_REQUEST["imp_language"])) {
  $imp_language=$_REQUEST["imp_language"];
  $smarty->assign('imp_language',$imp_language);
}
if (isset($_REQUEST["exp_language"])) {
  $exp_language=$_REQUEST["exp_language"];
  $smarty->assign('exp_language',$exp_language);
}

// Import

if (isset($_REQUEST["import"])) {
  include_once('lang/'.$imp_language.'/language.php');
  $impmsg=$impmsg."Included lang/".$imp_language."/language.php";
  $query="insert into tiki_languages values ('".$imp_language."','')";
  $result=$tikilib->query($query);
  while (list ($key, $val) = each($lang)) {
    $query="insert into tiki_language values ('".addslashes($key)."','".$imp_language."','".addslashes($val)."')";
    $result=$tikilib->query($query);

  }
      $smarty->assign('impmsg',$impmsg);
}

// Export
if (isset($_REQUEST["export"])) {
  $query="select source, tran from tiki_language where lang='".$exp_language."'";
  $result=$tikilib->query($query);
  $data="<?php\n\$lang=Array(\n";
  while ($res=$result->fetchRow()) {
    $data=$data."\"".$res["0"]."\" => \"".$res["1"]."\",\n";
  }
  $data=$data.");\n?>";
  header("Content-type: application/unknown");
  header( "Content-Disposition: inline; filename=language.php" );
  echo $data;
  exit(0);
  $smarty->assign('expmsg',$expmsg);
}


$smarty->assign('mid','tiki-imexport_languages.tpl');
$smarty->display("styles/$style_base/tiki.tpl");

?>
