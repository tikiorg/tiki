<?php
/* wikiplugin_wantedpages.php
 * TikiWiki plugin to display wanted Wiki pages
 * <grk@ducked.net> and <gray@ritualmagick.org>
 * Minor tweaks by avgasse <amedee@amedee.be>
 */
require_once "lib/wiki/pluginslib.php";

function wikiplugin_wantedpages_help() {
        return tra("Lists ''wanted'' Wiki pages").":<br />~np~{WANTEDPAGES(ignore=>IgnoreThisPage+IgnoreAnotherPage+...)}{WANTEDPAGES}~/np~";
}

function wikiplugin_wantedpages($data, $params) {
  $plugin = new WikiPluginWantedPages();
  return $plugin->wantedpages($data, $params);
}

class WikiPluginWantedPages extends PluginsLib {

  function wantedpages($data, $params) {

    // Grab and handle our Tiki parameters...
    extract($params, EXTR_SKIP);
    if(!isset($ignore)) $ignore = '';
    $ignorepages = explode('+',$ignore);

    // Currently we only look in wiki pages.
    // Wiki links in articles, blogs, etc are ignored.
    $query = "select tl.`toPage`, tl.`fromPage` from tiki_links tl";
    $query .= " left join tiki_pages tp on (tl.`toPage` = tp.`pageName`)";
    $query .= " where tp.`pageName` is null;";
    $result = $this->query($query,array());
    $out = array();
    while ($row = $result->fetchRow()) {
      $skip = false;
      foreach($ignorepages as $ipage)
        if(strtolower($row['toPage']) == strtolower($ipage)) $skip = true;
      if(!$skip)
            $out[] = '(('.$row['toPage'].")) | ((${row['fromPage']}))";
    }
    sort($out);
    // Some i18n would be nice here.
    return '||__Wanted Page__|__Referenced By Page__'."\n".implode("\n",$out).'||';
  }
}
?> 
