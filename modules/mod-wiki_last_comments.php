<?php
//
// $Header: /cvsroot/tikiwiki/tiki/modules/mod-wiki_last_comments.php,v 1.1 2003-11-24 02:40:47 zaufi Exp $
// \brief Show last comments on wiki pages
//
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

?>