{* $Header: /cvsroot/tikiwiki/_mods/modules/clock/mod-clock.tpl,v 1.3 2005-09-08 10:41:02 damosoft Exp $ 
   TikiWiki ticking clock in a module

*}
<div align="center">
{literal}
<script language="JavaScript">
<!--
day = new Date();
miVisit = day.getTime();
function clock() {
dayTwo = new Date();
hrNow = dayTwo.getHours();
mnNow = dayTwo.getMinutes();	
scNow = dayTwo.getSeconds();
miNow = dayTwo.getTime();
if (hrNow == 0) {
hour = 12;
ap = " AM";
} else if(hrNow <= 11) {
ap = " AM";
hour = hrNow;
} else if(hrNow == 12) {
ap = " PM";
hour = 12;
} else if (hrNow >= 13) {
hour = (hrNow - 12);
ap = " PM";
}
if (hrNow >= 13) {
hour = hrNow - 12;
}
if (mnNow <= 9) {
min = "0" + mnNow;
}
else (min = mnNow)
if (scNow <= 9) {
secs = "0" + scNow;
} else {
secs = scNow;
}
time = hour + ":" + min + ":" + secs + ap;
document.form.button.value = time;
self.status = time;
setTimeout('clock()', 1000);
}
function timeInfo() {
milliSince = miNow;
milliNow = miNow - miVisit;
secsVisit = Math.round(milliNow / 1000);
minsVisit = Math.round((milliNow / 1000) / 60);
alert("There have been " + milliSince + " milliseconds since midnight, January 1, 1970.  "
+ "You have spent " + milliNow + " of those milliseconds on this page.  "
+ ".... About " + minsVisit + " minutes, and "
+ secsVisit + " seconds.");
}
document.write("<form name=\"form\">"
+ "<input type=button value=\"Click for info!\""
+ " name=button onClick=\"timeInfo()\"></form>");
onError = null;
clock();
// End -->
</SCRIPT>
{/literal}
</div>
