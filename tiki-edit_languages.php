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


// Create a language

if(isset($_REQUEST["createlang"])) {
  $_REQUEST["cr_lang_short"]=addslashes($_REQUEST["cr_lang_short"]);
  if (strlen($_REQUEST["cr_lang_short"]) != 2) {
    $crerror=true;
    $smarty->assign('crmsg',tra("Shortname must be 2 Characters"));
  }
  elseif (strlen($_REQUEST["cr_lang_long"]) == 0) {
    $crerror=true;
    $smarty->assign('crmsg',tra("You must provide a longname"));
  } else {
  // Now we can create it
    $_REQUEST["cr_lang_long"]=addslashes(strip_tags($_REQUEST["cr_lang_long"]));
    $query="insert into tiki_languages values ('".$_REQUEST["cr_lang_short"]."','".$_REQUEST["cr_lang_long"]."')";
    $result=$tikilib->query($query);
  }
  if ($crerror) { // todo: error handling
    $smarty->assign('cr_lang_short',$_REQUEST["cr_lang_short"]);
    $smarty->assign('cr_lang_long',$_REQUEST["cr_lang_long"]);
  } else {
    $smarty->assign('crmsg',tra("Language created").": ".$_REQUEST["cr_lang_short"]." ".$_REQUEST["cr_lang_long"]);
    $smarty->assign('edit_language',$_REQUEST["cr_lang_short"]);
  }
}



//Editing things

// Get availible languages from DB

$query="select lang from tiki_languages";
$result=$tikilib->query($query);
$languages=Array();
while ($res=$result->fetchRow()) {
  $languages[]=$res["0"];
}
$smarty->assign_by_ref('languages',$languages);





// preserving variables

if (isset($_REQUEST["edit_language"])) {
  $smarty->assign('edit_language',$_REQUEST["edit_language"]);
  $edit_language=$_REQUEST["edit_language"];
}

if (!isset($edit_language)) {$edit_language=$language;}

if (isset($_REQUEST["whataction"]) ) {
  $smarty->assign('whataction',$_REQUEST["whataction"]);
  }



// Adding strings

if (isset($_REQUEST["add_tran"])) {
  $add_tran_source=$_REQUEST["add_tran_source"];
  $add_tran_tran=$_REQUEST["add_tran_tran"];
  if (strlen($add_tran_source) != 0 && strlen($add_tran_tran) != 0) {
    $add_tran_source=addslashes(htmlentities(strip_tags($add_tran_source),ENT_NOQUOTES));
    $add_tran_tran=addslashes(htmlentities(strip_tags($add_tran_tran),ENT_NOQUOTES));
    $query="insert into tiki_language values ('".$add_tran_source."','".$edit_language."','".$add_tran_tran."')";
    $result=$tikilib->query($query);
    // remove from untranslated Table
    $query="delete from tiki_untranslated where source='".$add_tran_source."' and lang='".$edit_language."'";
    $result=$tikilib->query($query); 
  }
}



//Selection for untranslated Strings and edit translations

if (isset($_REQUEST["whataction"])) {
  $whataction=$_REQUEST["whataction"];
} else {
  $whataction="";
}

if ($whataction == "edit_rec_sw" || $whataction == "edit_tran_sw") {

  //check if user has translated something
  if (isset($_REQUEST["tr_recnum"])) {
    $tr_recnum=$_REQUEST["tr_recnum"];
    for ($i=$tr_recnum; $i<=$tr_recnum+$maxRecords; $i++) {
      // Handle edits in translate recorded
      if (isset($_REQUEST["edit_rec_$i"])) {
        if (strlen($_REQUEST["edit_rec_tran_$i"]) >0 && strlen($_REQUEST["edit_rec_source_$i"]) >0){
          $_REQUEST["edit_rec_source_$i"]=addslashes(htmlentities(strip_tags($_REQUEST["edit_rec_source_$i"]),ENT_NOQUOTES));
          $_REQUEST["edit_rec_tran_$i"]=addslashes(htmlentities(strip_tags($_REQUEST["edit_rec_tran_$i"]),ENT_NOQUOTES));
          $query="insert into tiki_language values('".$_REQUEST["edit_rec_source_$i"]."','".$edit_language."','".$_REQUEST["edit_rec_tran_$i"]."')";
          $result=$tikilib->query($query);
          $query="delete from tiki_untranslated where source='".$_REQUEST["edit_rec_source_$i"]."' and lang='".$edit_language."'";
          $result=$tikilib->query($query);
	  // No error checking necessary
        }
      } elseif (isset($_REQUEST["edt_tran_$i"])) { 
        // Handle edits in edit translations
        if (strlen($_REQUEST["edit_edt_tran_$i"]) >0 && strlen($_REQUEST["edit_edt_source_$i"]) >0) {
          $_REQUEST["edit_edt_tran_$i"]=addslashes(htmlentities(strip_tags($_REQUEST["edit_edt_tran_$i"]),ENT_NOQUOTES));
          $_REQUEST["edit_edt_source_$i"]=addslashes(htmlentities(strip_tags($_REQUEST["edit_edt_source_$i"]),ENT_NOQUOTES));
	  $query="update tiki_language set tran='".$_REQUEST["edit_edt_tran_$i"]."' where source='".$_REQUEST["edit_edt_source_$i"]."' and lang='".$edit_language."'";
          $result=$tikilib->query($query);
          //if ($result->numRows()== 0 ) 
          if (!isset($result)) {
            $query="insert into tiki_language values('".$_REQUEST["edit_edt_source_$i"]."','".$edit_language."','".$_REQUEST["edit_edt_tran_$i"]."')";
            $result=$tikilib->query($query);
          }
        }
      } elseif (isset($_REQUEST["del_tran_$i"])) {
        // Handle deletes here
        if (strlen($_REQUEST["edit_edt_source_$i"]) >0) {
          $_REQUEST["edit_edt_source_$i"]=addslashes(htmlentities(strip_tags($_REQUEST["edit_etd_source_$i"]),ENT_NOQUOTES));
          $query="delete from tiki_language where source='".$_REQUEST["edit_edt_source_$i"]."' and lang='".$edit_language."'";
          $result=$tikilib->query($query);
        }
      }
    } // end of for ...
  } 
  
  

  //show only a selection of maxRecords records
  if (!isset($_REQUEST["tr_recnum"]) || isset($_REQUEST["tran_search_sm"]) || isset($_REQUEST["langaction"])) {
    $smarty->assign('tr_recnum',0);
    $tr_recnum=0;
  } else {
    $tr_recnum=$_REQUEST["tr_recnum"];
    if (isset($_REQUEST["morerec"])) {
      $tr_recnum+=$maxRecords;
    }
    if (isset($_REQUEST["lessrec"])) {
      $tr_recnum-=$maxRecords;
    }

    $smarty->assign('tr_recnum',$tr_recnum);
  }
  $smarty->assign('maxRecords',$maxRecords);
  


  //Handle searches
  $squery="";
  $squeryedit="";
  $squeryrec="";
  if (isset($_REQUEST["tran_search"])) {
    $tran_search=htmlentities(strip_tags($_REQUEST["tran_search"]),ENT_NOQUOTES);
    if (strlen($tran_search)>0) {
      $smarty->assign('tran_search',$tran_search);
      $squeryedit=" and (source like '%".$tran_search."%' or tran like '%".$tran_search."%')";
      $squeryrec=" and source like '%".$tran_search."%'";
    }
  }

  //get array from db
  if (!isset($tr_recnum)) $tr_recnum=0;
  $aquery=sprintf(" order by source limit %d,%d",$tr_recnum,$maxRecords);
  if ($whataction == "edit_tran_sw") {
    $query="select source,tran from tiki_language where lang='".$edit_language."'".$squeryedit.$aquery;
    $nquery="select count(*) from tiki_language where lang='".$edit_language."'".$squeryedit; 
  } elseif ($whataction == "edit_rec_sw") {
    $query="select source from tiki_untranslated where lang='".$edit_language."'".$squeryrec.$aquery;
    $nquery="select count(*) from tiki_untranslated where lang='".$edit_language."'".$squeryrec; 
  }
  $result=$tikilib->query($nquery);
  $res=$result->fetchRow(); 
  $untr_numrows=$res["0"];
  $smarty->assign('untr_numrows',$untr_numrows);
  

  $result=$tikilib->query($query);
  
  //$i=$tr_recnum;
  if ($whataction=="edit_rec_sw") {
    $untranslated=Array();
    $i=0;
    while ($res=$result->fetchRow(DB_FETCHMODE_ASSOC)) {
      $untranslated[]=$res["source"];
      $i++;
    }
    $smarty->assign_by_ref('untranslated',$untranslated);
  } elseif ($whataction=="edit_tran_sw") {
    $untranslated=Array();
    $translation=Array();
    $i=0;
    while ($res=$result->fetchRow(DB_FETCHMODE_ASSOC)) {
      $untranslated[]=$res["source"];
      $translation[]=$res["tran"];
      $i++;
    }
    $smarty->assign_by_ref('untranslated',$untranslated);
    $smarty->assign_by_ref('translation',$translation);
  }
}



$smarty->assign('mid','tiki-edit_languages.tpl');
$smarty->display("styles/$style_base/tiki.tpl");

?>
