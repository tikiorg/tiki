<?php
//
// $Header: /cvsroot/tikiwiki/tiki/modules/mod-wiki_last_comments.php,v 1.4 2004-03-29 21:26:42 mose Exp $
// \brief Show last comments on wiki pages
//

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
}

function wiki_last_comments($limit)
{
    $query = "select `object`,`title`,`commentDate`,`userName`
              from `tiki_comments` where `objectType`='wiki page' order by `commentDate` desc";
    global $tikilib;
    $result = $tikilib->query($query, array(), $limit, 0);
    $ret = array();

    while ($res = $result->fetchRow())
    {
        $aux["page"] = $res["object"];
        $aux["title"]= $res["title"];
        $aux["commentDate"] = $res["commentDate"];
        $aux["user"] = $res["userName"];
        $ret[] = $aux;
    }
    return $ret;
}

$comments = wiki_last_comments($module_rows);
$smarty->assign('comments', $comments);
$smarty->assign('nonums', isset($module_params["nonums"]) ? $module_params["nonums"] : 'n');
$smarty->assign('moretooltips', isset($module_params["moretooltips"]) ? $module_params["moretooltips"] : 'n');

?>
