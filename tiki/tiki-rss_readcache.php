<?php

// get default rss feed version from database or set to 1.0 if none in there
$rss_version = $tikilib->get_preference("rssfeed_default_version",1);

// override version if set as request parameter
if (isset($_REQUEST["ver"])) {
		$rss_version = $_REQUEST["ver"];
	};

$query = "select * from `tiki_rss_feeds` where `name`=? and `rssVer`=?";
$bindvars=array($uniqueid, $rss_version);
$result = $tikilib->query($query, $bindvars);

$changes = "";
$output = "EMPTY";

if (!$result->numRows())
{
  // nothing found, then insert row for this feed+rss_ver
  $now = date("U");
  $query = "insert into `tiki_rss_feeds`(`name`,`rssVer`,`refresh`,`lastUpdated`,`cache`) values(?,?,?,?,?)";
  
  // default value for cache timeout is 300 (5 minutes)
  $bindvars=array($uniqueid,(int) $rss_version,(int) 300 ,(int) $now, $output);
  $result = $tikilib->query($query, $bindvars);
}
else
{
  // entry found in db:
  $res = $result->fetchRow();
  $output = $res["cache"];
  $refresh = $res["refresh"];
  $lastUpdated = $res["lastUpdated"];
  // up to date? if not, then set trigger to reload data:
  if ($lastUpdated + $refresh < $now) { $output="EMPTY"; } // TODO: make timeout configurable (is 5 minutes now)
}

?>