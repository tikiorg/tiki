{*
@author: Javier Reyes Gomez (jreyes@escire.com)
@date: 27/01/2006
@copyright (C) 2006 Javier Reyes Gomez (eScire.com)
@license http://www.gnu.org/copyleft/lgpl.html GNU/LGPL
*}
{*{popup_init src="lib/overlib.js"}*}
{php}
include_once("lib/class_calendar.php");
global $dbTiki,$tikilib,$user;
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

$thedate = TikiLib::make_time(23,59,59,$mon,$day,$year);
$_SESSION["thedate"] = $thedate;

// Calculate number of days in month
// The format is S M T W T F S
$c = new Calendar("en");
$v = mb_substr(tra($c->nameOfMonth($mon)),0,3);
$dayofweek = tra($c->dayOfWeekStr($day,$mon,$year));
{/php}
{*get_strings {tr}January{/tr} {tr}February{/tr} {tr}March{/tr} {tr}April{/tr} {tr}May{/tr} {tr}June{/tr} {tr}July{/tr} {tr}August{/tr} {tr}September{/tr} {tr}October{/tr} {tr}November{/tr} {tr}December{/tr}
{tr}Monday{/tr} {tr}Tuesday{/tr} {tr}Wednesday{/tr} {tr}Thursday{/tr} {tr}Friday{/tr} {tr}Saturday{/tr} {tr}Sunday{/tr}-  only tr tags are collected by get_strings in tpl*}
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

{tiki_workspaces_module title="{tr}Workspace Calendar{/tr}" name="workspaces_calendar" flip=$module_params.flip decorations=$module_params.decorations style_title=$style_title style_data=$style_data}
{include file="tiki-workspaces_module_error.tpl" error=$error_msg}
    <table  border="0" cellspacing="0" cellpadding="0">
    <!-- THIS ROW DISPLAYS THE YEAR AND MONTH -->
    <tr>
      <td align="center">
{php}
        $pmonth = $mon -1;
	$nmonth = $mon +1;
	$pyear = $year;
	$nyear = $year;
	if ($pmonth == 0) {$pyear -= 1; $pmonth += 12;}
	if ($nmonth == 13) {$nyear += 1; $nmonth -= 12;}
        $url="$father"."day=$day&amp;mon=$pmonth&amp;year=$pyear";
        print( "<a class=\"nav\" href=\"".$url."\"> &lt; </a>" );
        print( "<label>".$v."</label>" );
        $url="$father"."day=$day&amp;mon=$nmonth&amp;year=$nyear";
        print( "<a class=\"nav\" href=\"".$url."\"> &gt; </a>" );
        print( "&nbsp;" );
        $mong=$year-1;
        $url="$father"."day=$day&amp;mon=$mon&amp;year=$mong";
        print( "<a class=\"nav\" href=\"".$url."\"> &lt; </a>" );
        print( "<label>".$year."</label>" );
        $mong=$year+1;
        $url="$father"."day=$day&amp;mon=$mon&amp;year=$mong";
        print( "<a class=\"nav\" href=\"".$url."\"> &gt; </a>" );
{/php}         
      </td>
    </tr>
{php}
    $mat = $c->getDisplayMatrix($day,$mon,$year);
    $pmat = $c->getPureMatrix($day,$mon,$year);
    $listevents = array();
    
    if (isset($_SESSION["currentWorkspace"])){
	    global $calendarlib;
	  	include_once ('lib/calendar/calendarlib.php'); 
	  	require_once ('lib/workspaces/resourceslib.php'); 
	  	$viewstart = TikiLib::make_time(0,0,0, $mon, 1, $year);
	  	$viewend = TikiLib::make_time(0,0,0,$mon + 1, 1, $year);
    	/*$calendarData = $calendarlib->list_calendars(0, -1, 'created_desc', "WSCALENDAR".$_SESSION["currentWorkspace"]["code"]);
		$calendarId = '0';
		foreach ($calendarData['data'] as $key => $val) {
			$calendarId = $key;
		}*/
		$resourcesLib = new WorkspaceResourcesLib($dbTiki);
		$calendars = $resourcesLib->get_category_objects($_SESSION["currentWorkspace"]["categoryId"],null,"calendar");

		$calendarIds = array();
		$calIds="";
		foreach ($calendars as $key => $val) {
			$calendarIds[] = $val["objId"];
			$calIds .= "&calIds%5B%5D=".$val["objId"];
		}
    	//$listevents = $calendarlib->list_items(array($calendarId), "admin", $viewstart, $viewend, 0, 50);
    	if (count($calendarIds)>0){
	    	$listevents = $calendarlib->list_items($calendarIds, "admin", $viewstart, $viewend, 0, 50);
	    }
    	//print_r($listevents);
    }
    $eventsBody =array();
    $desplaza = ($c->dayOfWeek(1, $mon, $year))-2;

    foreach ($listevents as $key=>$le) {
    	$day = intval(date("d",$key));
 	  	$mat[$day+$desplaza]="E".$mat[$day+$desplaza];
    	
    	foreach ($le as $keyevent=>$event) {
    	 $eventsBody[$day] = $eventsBody[$day]." *".$event["result"]["name"];
    	}
    }
{/php}
    <tr>
      <td align="center">
        <table  border="0" cellspacing="0" cellpadding="0">
        <!-- DAYS OF THE WEEK -->
        <tr>
{php}
          for ($i=0;$i<7;$i++) {
            $dayW = tra($c->dayOfWeekStrFromNo($i+1));
            $dayp = mb_substr($dayW,0,1);
            print("<td class=\"date\" align=\"center\">$dayp</td>");
          }
{/php}
        </tr>
        <!-- TRs WITH DAYS -->
{php}
          for ($i=0;$i<6;$i++) {
            print("<tr>");
            for ($j=0;$j<7;$j++) {
              $in = $i*7+$j;
              $pval = $pmat[$in];
              $val = $mat[$in];
              if (substr($val,0,1)=='+') {
                $val = substr($val,1,strlen($val)-1);
                $classval = "wstoday";
              }elseif(substr($val,0,1)=='E') {
              	$val = substr($val,1,strlen($val)-1);
              	$val = str_replace ("+", "", $val); 
                $classval = "wsevent";
              } else {
                $classval = "wsday";
              }
              print( "<td class=\"$classval\" align=\"center\">" );
              $url = $father."day=$pval&amp;mon=$mon&amp;year=$year";
              //print( "<a class=\"$classval\" href=\"tiki-calendar.php?viewmode=day&mon=$mon&day=$pval&year=$year&calIds%5B%5D=$calendarId\" title=\"$eventsBody[$in]\">$val</a></td>");
              print( "<a class=\"$classval\" href=\"tiki-calendar.php?viewmode=day&mon=$mon&day=$pval&year=$year$calIds\" title=\"$eventsBody[$in]\">$val</a></td>");
            }
            print("</tr>");
          }
{/php}
        </table>
      </td>
    </tr>
    <tr>
      <td align="center">

<label>{tr}List events{/tr}</label><img src="img/icons2/event-attendees.gif">
<a id="flipperideventos" class="link" href="javascript:flipWithSign('ideventos')">[+]</a>
<div id="ideventos" style="display:none;">
<table class="wscalevents">
{php}
    foreach ($listevents as $key=>$le) {
    	$day = date("d-H:i",$key);
    	foreach ($le as $keyevent=>$event) {
    	$eventname = $event["result"]["name"];
    	print("<tr><td class=\"wscaleventday\" >$day</td><td class=\"wscaleventname\">$eventname</td></tr>");
   		}
    }
{/php}
</table>
</div>
      </td>
    </tr>
    </table>
{/tiki_workspaces_module}
