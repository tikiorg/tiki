{php}
include("lib/class_calendar.php");
if(!isset($_SESSION["thedate"])) {
  $day = date("d");
  $mon = date("m");
  $year = date("Y");
}
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
$father="tiki-index.php";
$todaylink=$father."?day=".date("d")."&mon=".date("m")."&year=".date("Y")
?>
<div class="boxnm">
<div class="box-title">
Calendar
</div>
<div class="box-datanm">
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td>
      <!-- THE DAYS -->
      <table width="100%" cellspacing="1" cellpadding="0"  border="0">
        <tr>
	  <td colspan="7">
       	    <table width="100%" cellspacing="0" cellpadding="0" border="0">
              <tr class="changedate" bgcolor="#FFFFFF"> <!-- THIS ROW DISPLAYS THE YEAR AND MONTH -->
                <td align="left"><a class="link" href="<?$mong=$year-1;print("$father?day=$day&mon=$mon&year=$mong");?>">&lt;</a><?=$year?><a class="link" href="<?$mong=$year+1;print("$father?day=$day&mon=$mon&year=$mong");?>">&gt;</a></td>
                <td align="center"><a class="link" href="<?=$todaylink?>">Today</a></td>
                <td align="right"><a class="link" href="<?$mong=$mon-1;print("$father?day=$day&mon=$mong&year=$year");?>">&lt;</a><?=$v?><a class="link" href="<?$mong=$mon+1;print("$father?day=$day&mon=$mong&year=$year");?>">&gt;</a></td>
              </tr> <!-- ROW WITH YEAR AND MONTH ENDS -->
            </table>
          </td>
	</tr>
        <?
          $mat=$c->getDisplayMatrix($day,$mon,$year);
          $pmat=$c->getPureMatrix($day,$mon,$year);
        ?>
        <tr> <!-- DAYS OF THE WEEK -->
          <?
            for($i=0;$i<7;$i++) {
              $dayW=$c->dayOfWeekStrFromNo($i+1);
              $dayp=Substr($dayW,0,1);
              print("<td class='date' align='right'>$dayp</td>");
            }
          ?>
        </tr>
        <!-- TRs WITH DAYS -->
        <?
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
              print("<td class='fc' align='right'><a class='$classval' href='$father?day=$pval&mon=$mon&year=$year'>$val</a></td>");
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

