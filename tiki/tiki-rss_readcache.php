<?php

$rss_version = 1; // default is: rss v1.0 - TODO: make this configurable
if (isset($_REQUEST["ver"]))
	if (substr($_REQUEST["ver"],0,1) == '2') {
		$rss_version = 2;
	}

$query = "select * from `tiki_rss_feeds` where `name`=? and `rssVer`=?";
$bindvars=array($feed, $rss_version);
$result = $tikilib->query($query, $bindvars);

$changes = "";
$output = "EMPTY";

if (!$result->numRows())
{
  // nothing found, then insert row for this feed+rss_ver
  $now = date("U");
  $query = "insert into `tiki_rss_feeds`(`name`,`rssVer`,`refresh`,`lastUpdated`,`cache`) values(?,?,?,?,?)";
  $bindvars=array($feed,(int) $rss_version,1000000,(int) $now, $output);
  $result = $tikilib->query($query, $bindvars);
}
else
{
  // entry found in db:
  $res = $result->fetchRow();
  $output = $res["cache"];
  $lastUpdated = $res["lastUpdated"];
  // up to date? if not, then set trigger to reload data:
  if ($lastUpdated + 7*60 < $now) { $output="EMPTY"; } // TODO: make timeout configurable (is 7 minutes now)
}

?>