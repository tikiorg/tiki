{* $Header$ *}

{php}
include_once("lib/class_calendar.php");
global $calendarlib; include_once ('lib/calendar/calendarlib.php');
global $dbTiki,$tikilib,$user;

// get date for which to display the calendar view:
if(isset($_SESSION["thedate"])) {
	$day=TikiLib::date_format("%d",$_SESSION["thedate"]);
	$mon=TikiLib::date_format("%m",$_SESSION["thedate"]);
	$year=TikiLib::date_format("%Y",$_SESSION["thedate"]);
} else {
	$day=TikiLib::date_format("%d");
	$mon=TikiLib::date_format("%m");
	$year=TikiLib::date_format("%Y");
}
if(isset($_REQUEST["day"])) {
	$day = $_REQUEST["day"];
}

if(isset($_REQUEST["mon"])) {
	$mon = $_REQUEST["mon"];
}

if(isset($_REQUEST["year"])) {
	$year = $_REQUEST["year"];
}

$thedate = TikiLib::make_time(23,59,59,intval($mon),intval($day),intval($year));
$_SESSION["thedate"] = $thedate;

$calids=$calendarlib->list_user_calIds();
if (isset($module_params["calendarId"])) { $calids = array($module_params["calendarId"]); }
if ($calids=="") $calids=array(0);

// get all calendar entries for user from thedate-32 days to thedate+32 days, maximum of 90 entries...
// that are not private or somehow else locked:
$items = $calendarlib->list_items($calids, $user, $thedate - 60*60*24*16, $thedate + 60*60*24*31, 0, 90 );

// Calculate number of days in month
// The format is S M T W T F S
$c = new Calendar("en");
$v = mb_substr(tra($c->nameOfMonth($mon)),0,3);
$dayofweek = tra($c->dayOfWeekStr($day,$mon,$year));
{/php}
{*get_strings {tr}January{/tr} {tr}February{/tr} {tr}March{/tr} {tr}April{/tr} {tr}May{/tr} {tr}June{/tr} {tr}July{/tr} {tr}August{/tr} {tr}September{/tr} {tr}October{/tr} {tr}November{/tr} {tr}December{/tr}
{tr}Monday{/tr} {tr}Tuesday{/tr} {tr}Wednesday{/tr} {tr}Thursday{/tr} {tr}Friday{/tr} {tr}Saturday{/tr} {tr}Sunday{/tr}-	only tr tags are collected by get_strings in tpl*}
{php}

$parsed = parse_url($_SERVER["REQUEST_URI"]);
if (!isset($parsed["query"])) {
	$parsed["query"]='';
}
parse_str($parsed["query"],$query);
unset($query["day"]);
unset($query["mon"]);
unset($query["year"]);
$father=$parsed["path"];
if (count($query)>0) {
	$first=1;
	foreach ($query as $name => $val) {
	if ($first) {
		$first=false;
		$father.='?'.$name.'='.$val;
	} else {
		$father.='&amp;'.$name.'='.$val;
	}
	}
	$father.='&amp;';
} else {
	$father.='?';
}

$todaylink=$father."day=".date("d")."&amp;mon=".date("m")."&amp;year=".date("Y");
{/php}

{tikimodule title="{tr}Current events{/tr}" name="events" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox}
	<table	border="0" cellspacing="0" cellpadding="0" class="caltable">
	<!-- THIS ROW DISPLAYS THE YEAR AND MONTH -->
	<tr>
	<td align="center" colspan="2">
{php}
	$pmonth = $mon -1;
	$nmonth = $mon +1;
	$pyear = $year;
	$nyear = $year;
	if ($pmonth == 0) {$pyear -= 1; $pmonth += 12;}
	if ($nmonth == 13) {$nyear += 1; $nmonth -= 12;}
	$url="$father"."day=$day&amp;mon=$pmonth&amp;year=$pyear";
	print( "<a class=\"nav\" href=\"".$url."\" title=\"{tr}Prev{/tr}\"> &lt; </a>" );
	$url="$father"."viewlist=table&amp;viewmode=month&amp;mon=$mon&amp;year=$year";
	print( "<a href=\"$url\">$v</a>" );
	$url="$father"."day=$day&amp;mon=$nmonth&amp;year=$nyear";
	print( "<a class=\"nav\" href=\"".$url."\" title=\"{tr}Next{/tr}\"> &gt; </a>" );
	print( "&nbsp;" );
	$mong=$year-1;
	$url="$father"."day=$day&amp;mon=$mon&amp;year=$mong";
	print( "<a class=\"nav\" href=\"".$url."\" title=\"{tr}Prev{/tr}\"> &lt; </a>" );
	print( $year );
	$mong=$year+1;
	$url="$father"."day=$day&amp;mon=$mon&amp;year=$mong";
	print( "<a class=\"nav\" href=\"".$url."\" title=\"{tr}Next{/tr}\"> &gt; </a>" );
{/php}		 
	</td>
	</tr>
{php}
	$mat = $c->getDisplayMatrix($day,$mon,$year);
	$pmat = $c->getPureMatrix($day,$mon,$year);
{/php}
	<tr>
	<td align="center" colspan="2">
		<table	border="0" cellspacing="0" cellpadding="0">
		<!-- DAYS OF THE WEEK -->
		<tr>
{php}
	$todaymouseover="";
	for ($i=0;$i<7;$i++) {
	$dayW = tra($c->dayOfWeekStrFromNo($i+1));
	$dayp = mb_substr($dayW,0,2);
	print("<td class=\"date\" align=\"center\">$dayp</td>");
		}
{/php}
		</tr>
		<!-- TRs WITH DAYS -->
{php}
		$lastval=0; $dateIsIn="past";
		for ($i=0;$i<6;$i++) {
			print("<tr>");
			for ($j=0;$j<7;$j++) {
				$in = $i*7+$j;
				$pval = $pmat[$in];
				$fc="";
				$val = $mat[$in];
				if ($val<$lastval) {
					$mon++;
					if ($mon>12) {
						$mon=1; $year++;
					}
				}
				$lastval=$val;

				if (substr($val,0,1)=='+') {
					$val = substr($val,1,strlen($val)-1);
					$classval = "today";
					$dateIsIn = "future";
				} else {
					$classval = "day";
				}
				$newval="";
				unset($tmp);
				$valtime = intval($val);
				if (array_key_exists(mktime(0, 0, 0, intval($mon), intval($valtime), intval($year)),$items)) {
					$tmp = $items[mktime(0, 0, 0, intval($mon), intval($valtime), intval($year))];
				}
				if (isset($tmp)) if (is_array($tmp)) {
					unset($items[mktime(0, 0, 0, intval($mon), intval($valtime), intval($year))]);



					foreach ($tmp as $xx) {
						// TODO: put more data in the mouseover window
						$newval .= $xx['name']."<br />";
					}
				}
				$mouseover="";
				if ($newval <> "") {
					$mouseover="onmouseover=\"return overlib('".$newval."',HAUTO,VAUTO,CAPTION,'<div align=\'center\'>".tra("Events")."</div>');\" onmouseout=\"nd()\" ";
					$fc="event";
					if ($classval=="today") {
						$todaymouseover=$mouseover;
						$classval="todayevent";
					} else if ($dateIsIn<>"future") {
						$classval="oldevent";
						$fc="oldevent";
					} else {
						$classval="event";
					}
				}
				print( "<td class=\"$fc\" $mouseover align=\"center\">" );
				$url = "tiki-calendar.php?viewlist=list&amp;viewmode=day&amp;day=$val&amp;mon=$mon&amp;year=$year";
				if (is_numeric($val))
				   print( "<a class=\"$classval\" href=\"$url\">$val</a>");
				print("</td>\n");
			}
			print("</tr>");
		}
		print"</table>";
		print"</td>";
		print"</tr>";
		print"<tr>";
		print"<td $todaymouseover align=\"center\" rowspan=\"7\">";
		print( "<a class=\"today\" href=\"".$todaylink."\">".tra("Today")."</a>" );
{/php}
		</td>
	</tr>
	</table>
{/tikimodule}
