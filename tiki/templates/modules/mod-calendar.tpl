{php}
include_once("lib/class_calendar.php");
if(isset($_SESSION["thedate"])) {
  $day=date("d",$_SESSION["thedate"]);
  $mon=date("m",$_SESSION["thedate"]);
  $year=date("Y",$_SESSION["thedate"]);
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
$c=new Calendar("en");
$v=substr($c->nameOfMonth($mon),0,3);
$dayofweek=$c->dayOfWeekStr($day,$mon,$year);

$parsed=parse_url($_SERVER["REQUEST_URI"]);
if(!isset($parsed["query"])) {
  $parsed["query"]='';
}
parse_str($parsed["query"],$query);
unset($query["day"]);
unset($query["mon"]);
unset($query["year"]);
$father=httpPrefix().$parsed["path"];
if(count($query)>0) {
  $first=1;
  foreach($query as $name => $val) {
    if($first) {
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

if(!strstr($father,"?")) {
  $todaylink=$father."day=".date("d")."&amp;mon=".date("m")."&amp;year=".date("Y");
} else {
  $todaylink=$father."day=".date("d")."&amp;mon=".date("m")."&amp;year=".date("Y");
}
?>
<div class="box">
<div class="box-title" style="margin:0px;">
{tr}Calendar{/tr}
</div>
<div class="box-data" style="margin:0px;padding-right:4px;">
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td>
      <!-- THE DAYS -->
      <table width="100%" cellspacing="1" cellpadding="0"  border="0">
        <tr>
	  <td colspan="7">
       	    <table width="100%" cellspacing="0" cellpadding="0" border="0">
              <tr class="changedate" bgcolor="#FFFFFF"> <!-- THIS ROW DISPLAYS THE YEAR AND MONTH -->
                <td align="left"><a class="nav" href="<?php $mong=$year-1;print("$father"."day=$day&amp;mon=$mon&amp;year=$mong");?>">&lt;</a><span class="nav"><?php print($year);?></span><a class="nav" href="<?php $mong=$year+1;print("$father?day=$day&amp;mon=$mon&amp;year=$mong");?>">&gt;</a></td>
                <td align="center"><a class="today" href="<?php print($todaylink);?>">{tr}Today{/tr}</a></td>
                <td align="right"><a class="nav" href="<?php $mong=$mon-1;print("$father"."day=$day&amp;mon=$mong&amp;year=$year");?>">&lt;</a><span class="nav"><?php print($v);?></span><a class="nav" href="<?php $mong=$mon+1;print("$father?day=$day&amp;mon=$mong&amp;year=$year");?>">&gt;</a></td>
              </tr> <!-- ROW WITH YEAR AND MONTH ENDS -->
            </table>
          </td>
	</tr>
        <?php
          $mat=$c->getDisplayMatrix($day,$mon,$year);
          $pmat=$c->getPureMatrix($day,$mon,$year);
        ?>
        <tr> <!-- DAYS OF THE WEEK -->
          <?php
            for($i=0;$i<7;$i++) {
              $dayW=$c->dayOfWeekStrFromNo($i+1);
              $dayp=Substr($dayW,0,1);
              print("<td class='date' align='right'>$dayp</td>");
            }
          ?>
        </tr>
        <!-- TRs WITH DAYS -->
        <?php
          for($i=0;$i<6;$i++) {
            print("<tr>");
            for($j=0;$j<7;$j++) {
              $in=$i*7+$j;$pval=$pmat[$in];$val=$mat[$in];
              if(substr($val,0,1)=='+') {
                $val=substr($val,1,strlen($val)-1);
                $classval="today";
              } else {
                $classval="day";
              }
              print("<td class='fc' align='right'><a class='$classval' href='$father"."day=$pval&amp;mon=$mon&amp;year=$year'>$val</a></td>");
            }
            print("</tr>");
          }
        ?>
      </table>
    </td>
  </tr>
</table>
</div>
</div>
<?
{/php}