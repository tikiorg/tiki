{* $Header: /cvsroot/tikiwiki/tiki/templates/modules/mod-calendar.tpl,v 1.26 2003-09-15 13:28:17 sylvieg Exp $ *}

{php}
include_once("lib/class_calendar.php");
global $dbTiki,$tikilib,$user;
if(isset($_SESSION["thedate"])) {
  $day=date("d",$_SESSION["thedate"]);
  $mon=date("m",$_SESSION["thedate"]);
  $year=date("Y",$_SESSION["thedate"]);
} else {
	$day=date("d",$tikilib->server_time_to_site_time(time(),$user));
	$mon=date("m",$tikilib->server_time_to_site_time(time(),$user));
	$year=date("Y",$tikilib->server_time_to_site_time(time(),$user));
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

$thedate = mktime(23,59,59,$mon,$day,$year);
$_SESSION["thedate"] = $thedate;

// Calculate number of days in month
// The format is S M T W T F S
$c = new Calendar("en");
$v = mb_substr(tra($c->nameOfMonth($mon)),0,4);
$dayofweek = tra($c->dayOfWeekStr($day,$mon,$year));


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

if (!strstr($father,"?")) {
  $todaylink=$father."day=".date("d")."&amp;mon=".date("m")."&amp;year=".date("Y");
} else {
  $todaylink=$father."day=".date("d")."&amp;mon=".date("m")."&amp;year=".date("Y");
}
{/php}
<div class="box">
<div class="box-title" style="margin:0px;">
{include file="modules/module-title.tpl" module_title="{tr}Calendar{/tr}-{tr}Filter{/tr}" module_name="calendar"}
</div>
<div class="box-data" style="margin:0px;padding-right:4px;">

    <table width="100%" border="0" cellspacing="0" cellpadding="0">
    <!-- THIS ROW DISPLAYS THE YEAR AND MONTH -->
    <tr>
      <td align="center">
{php}
        $mong=$mon-1;
        $url="$father"."day=$day&amp;mon=$mong&amp;year=$year";
        print( "<a class=\"nav\" href=\"".$url."\"> &lt; </a>" );
        print( $v );
        $mong=$mon+1;
        $url="$father"."day=$day&amp;mon=$mong&amp;year=$year";
        print( "<a class=\"nav\" href=\"".$url."\"> &gt; </a>" );
        print( "&nbsp;" );
        $mong=$year-1;
        $url="$father"."day=$day&amp;mon=$mon&amp;year=$mong";
        print( "<a class=\"nav\" href=\"".$url."\"> &lt; </a>" );
        print( $year );
        $mong=$year+1;
        $url="$father"."day=$day&amp;mon=$mon&amp;year=$mong";
        print( "<a class=\"nav\" href=\"".$url."\"> &gt; </a>" );
{/php}         
      </td>
    </tr>
{php}
    $mat = $c->getDisplayMatrix($day,$mon,$year);
    $pmat = $c->getPureMatrix($day,$mon,$year);
{/php}
    <tr>
      <td align="center">
        <table width="95%" border="0" cellspacing="0" cellpadding="0">
        <!-- DAYS OF THE WEEK -->
        <tr>
{php}
          for ($i=0;$i<7;$i++) {
            $dayW = $c->dayOfWeekStrFromNo($i+1);
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
                $classval = "today";
              } else {
                $classval = "day";
              }
              print( "<td class=\"fc\" align=\"center\">" );
              $url = $father."day=$pval&amp;mon=$mon&amp;year=$year";
              print( "<a class=\"$classval\" href=\"$url\">$val</a></td>");
            }
            print("</tr>");
          }
{/php}
        </table>
      </td>
    </tr>
    <tr>
      <td align="center">
{php}
         print( "<a class=\"today\" href=\"".$todaylink."\">".tra("Today")."</a>" );
{/php}
      </td>
    </tr>
    </table>
  </div>
</div>

