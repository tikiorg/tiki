{* $Header: /cvsroot/tikiwiki/_mods/modules/worldclock/mod-worldclock.tpl,v 1.3 2006-09-17 21:04:50 illori Exp $ 
   TikiWiki ticking clock in a module
*}
{tikimodule title="{tr}World Clock{/tr}" name="worldclock" flip=$module_params.flip decorations=$module_params.decorations}
{literal}
<style type="text/css">
<!--
.citystyle{
position:absolute;top:0px;left:0px;
}
#theClockLayer{
position:relative;height:200px;left:20px;text-align:center;
}
.handsanddotsstyle{
margin-top:10px;position:absolute;top:0px;left:0px;width:2px;height:2px;font-size:2px;background-color:#000000;
}
.facestyle{
margin-top:10px;position:absolute;top:0px;left:0px;width:15px;height:15px;text-align:center;font-family:arial,sans-serif;font-size:10px;color:#000000;
}
.datestyle{
margin-top:20px;position:absolute;top:0px;left:0px;width:100px;text-align:center;font-family:arial,sans-serif;font-size:10px;color:#000000;
}
.ampmstyle{
margin-top:23px;position:absolute;top:0px;left:0px;width:20px;text-align:center;font-family:arial,sans-serif;font-size:10px;color:#000000;
}
-->
</style>
<div id="theClockLayer">
  <div id="theCities" class="citystyle">
    <form action="" name="frmtimezone">
      <select id="city" name="city" onchange="lcl(this.selectedIndex,this.options[0].selected)" class="select" style="width:140;position:relative;left:-13px">
        <option value="0" selected>Local time</option>
        <OPTION VALUE="4.30">Afghanistan</OPTION>
        <OPTION VALUE="1">Algeria</OPTION>
        <OPTION VALUE="-3">Argentina</OPTION>
        <OPTION VALUE="9.30">Australia - Adelaide</OPTION>
        <OPTION VALUE="8">Australia - Perth</OPTION>
        <OPTION VALUE="10">Australia - Sydney</OPTION>
        <OPTION VALUE="1">Austria</OPTION>
        <OPTION VALUE="3">Bahrain</OPTION>
        <OPTION VALUE="6">Bangladesh</OPTION>
        <OPTION VALUE="1">Belgium</OPTION>
        <OPTION VALUE="-4">Bolivia</OPTION>
        <OPTION VALUE="-5">Brazil - Andes</OPTION>
        <OPTION VALUE="-3">Brazil - East</OPTION>
        <OPTION VALUE="-4">Brazil - West</OPTION>
        <OPTION VALUE="2">Bulgaria</OPTION>
        <OPTION VALUE="6.30">Burma (Myanmar)</OPTION>
        <OPTION VALUE="-5">Chile</OPTION>
        <OPTION VALUE="-7">Canada - Calgary</OPTION>
        <OPTION VALUE="-3.30">Canada - Newfoundland</OPTION>
        <OPTION VALUE="-4">Canada - Nova Scotia</OPTION>
        <OPTION VALUE="-5">Canada - Toronto</OPTION>
        <OPTION VALUE="-8">Canada - Vancouver</OPTION>
        <OPTION VALUE="-6">Canada - Winnipeg</OPTION>
        <OPTION VALUE="8">China - Mainland</OPTION>
        <OPTION VALUE="8">China - Taiwan</OPTION>
        <OPTION VALUE="-5">Colombia</OPTION>
        <OPTION VALUE="-5">Cuba</OPTION>
        <OPTION VALUE="1">Denmark</OPTION>
        <OPTION VALUE="-5">Ecuador</OPTION>
        <OPTION VALUE="2">Egypt</OPTION>
        <OPTION VALUE="12">Fiji</OPTION>
        <OPTION VALUE="2">Finland</OPTION>
        <OPTION VALUE="1">France</OPTION>
        <OPTION VALUE="1">Germany</OPTION>
        <OPTION VALUE="0">Ghana</OPTION>
        <OPTION VALUE="2">Greece</OPTION>
        <OPTION VALUE="-3">Greenland</OPTION>
        <OPTION VALUE="1">Hungary</OPTION>
        <OPTION VALUE="5.30">India</OPTION>
        <OPTION VALUE="8">Indonesia - Bali, Borneo</OPTION>
        <OPTION VALUE="9">Indonesia - Irian Jaya</OPTION>
        <OPTION VALUE="7">Indonesia - Sumatra, Java</OPTION>
        <OPTION VALUE="3.30">Iran</OPTION>
        <OPTION VALUE="3">Iraq</OPTION>
        <OPTION VALUE="2">Israel</OPTION>
        <OPTION VALUE="1">Italy</OPTION>
        <OPTION VALUE="-5">Jamaica</OPTION>
        <OPTION VALUE="9">Japan</OPTION>
        <OPTION VALUE="3">Kenya</OPTION>
        <OPTION VALUE="9">Korea (North & South)</OPTION>
        <OPTION VALUE="3">Kuwait</OPTION>
        <OPTION VALUE="1">Libya</OPTION>
        <OPTION VALUE="8">Malaysia</OPTION>
        <OPTION VALUE="5">Maldives</OPTION>
        <OPTION VALUE="1">Mali</OPTION>
        <OPTION VALUE="4">Mauritius</OPTION>
        <OPTION VALUE="-6">Mexico</OPTION>
        <OPTION VALUE="0">Morocco</OPTION>
        <OPTION VALUE="5.45">Nepal</OPTION>
        <OPTION VALUE="1">Netherlands</OPTION>
        <OPTION VALUE="12">New Zealand</OPTION>
        <OPTION VALUE="1">Nigeria</OPTION>
        <OPTION VALUE="1">Norway</OPTION>
        <OPTION VALUE="4">Oman</OPTION>
        <OPTION VALUE="5">Pakistan</OPTION>
        <OPTION VALUE="-5">Peru</OPTION>
        <OPTION VALUE="8">Philippines</OPTION>
        <OPTION VALUE="1">Poland</OPTION>
        <OPTION VALUE="1">Portugal</OPTION>
        <OPTION VALUE="3">Qatar</OPTION>
        <OPTION VALUE="2">Romania</OPTION>
        <OPTION VALUE="11">Russia - Kamchatka</OPTION>
        <OPTION VALUE="3">Russia - Moscow</OPTION>
        <OPTION VALUE="9">Russia - Vladivostok</OPTION>
        <OPTION VALUE="4">Seychelles</OPTION>
        <OPTION VALUE="3">Saudi Arabia</OPTION>
        <OPTION VALUE="8">Singapore</OPTION>
        <OPTION VALUE="2">South Africa</OPTION>
        <OPTION VALUE="1">Spain</OPTION>
        <OPTION VALUE="3">Syria</OPTION>
        <OPTION VALUE="5.30">Sri Lanka</OPTION>
        <OPTION VALUE="1">Sweden</OPTION>
        <OPTION VALUE="1">Switzerland</OPTION>
        <OPTION VALUE="7">Thailand</OPTION>
        <OPTION VALUE="12">Tonga</OPTION>
        <OPTION VALUE="2">Turkey</OPTION>
        <OPTION VALUE="3">Ukraine</OPTION>
        <OPTION VALUE="5">Uzbekistan</OPTION>
        <OPTION VALUE="7">Vietnam</OPTION>
        <OPTION VALUE="4">UAE</OPTION>
        <OPTION VALUE="0">UK</OPTION>
        <OPTION VALUE="-9">USA - Alaska</OPTION>
        <OPTION VALUE="-9">USA - Arizona</OPTION>
        <OPTION VALUE="-6">USA - Central</OPTION>
        <OPTION VALUE="-5">USA - Eastern</OPTION>
        <OPTION VALUE="-10">USA - Hawaii</OPTION>
        <OPTION VALUE="-5">USA - Indiana East</OPTION>
        <OPTION VALUE="-7">USA - Mountain</OPTION>
        <OPTION VALUE="-8">USA - Pacific</OPTION>
        <OPTION VALUE="3">Yemen</OPTION>
        <OPTION VALUE="1">Yugoslavia</OPTION>
        <OPTION VALUE="2">Zambia</OPTION>
        <OPTION VALUE="2">Zimbabwe</OPTION>
        <!--
          <option value="0">GMT</option>
          <option value="1">Rome</option>
          <option value="2">Cairo</option>
          <option value="3">Moscow</option>
          <option value="3.30">Tehran</option>
          <option value="5">Karachi</option>
          <option value="5.30">Bombay</option>
          <option value="7">Bangkok</option>
          <option value="8">Hong Kong</option>
          <option value="9">Tokyo</option>
          <option value="9.30">Darwin</option>
          <option value="10">Sydney</option>
          <option value="12">Fiji</option>
          <option value="-10">Hawaii</option>
          <option value="-8">San Francisco</option>
          <option value="-7">Arizona</option>
          <option value="-5">New York</option>
          <option value="-3.30">Newfoundland</option>
          <option value="-3">Greenland</option>
		  -->
      </select>
    </form>
  </div>
  <script type="text/javascript">
<!-- World Clock (No DST, standard time only!)  http://www.btinternet.com/~kurt.grigg/javascript

if (document.getElementById){

fCol='#000000'; //face/number colour.
dCol='#cccccc'; //dot colour.
hCol='#000000'; //hours colour.
mCol='#000000'; //minutes colour.
sCol='#ff0000'; //seconds colour.
cCol='#000000'; //date colour.
aCol='#999999'; //am-pm colour.
bCol='#ffffff'; //select/form background colour.
tCol='#000000'; //select/form text colour.

//Alter nothing below! Alignments will be lost!
y=87;
x=60;
h=4;
m=5;
s=6;
cf=new Array();
cd=new Array();
ch=new Array();
cm=new Array();
cs=new Array();
face="3 4 5 6 7 8 9 10 11 12 1 2";
face=face.split(" ");
n=face.length;
e=360/n;
hDims=7;
zone=0;
isItLocal=true;
ampm="";
daysInMonth=31;
todaysDate="";
var addHours;
var oddMinutes;
var getOddMinutes;
var addOddMinutes;
plusMinus=false;

var mon=new Array("January","February","March","April","May","June","July","August","September","October","November","December");

document.write('<div id="theDate" class="datestyle" style="color:'+cCol+'">\!<\/div>');
document.write('<div id="amOrPm" class="ampmstyle" style="color:'+aCol+'">\!<\/div>');
for (i=0; i < n; i++){
 document.write('<div id="theFace'+i+'" class="facestyle" style="color:'+fCol+'">'+face[i]+'<\/div>');
// if (i==0 || i==3 || i==6 || i==9)
//	document.write('<div id="theFace'+i+'" class="facestyle" style="color:'+fCol+'"><img align="absmiddle" src="/crm/images/clock_face'+face[i]+'.gif"/><\/div>');
// else
//	document.write('<div id="theFace'+i+'" class="facestyle" style="color:'+fCol+'"><\/div>');
 cf[i]=document.getElementById("theFace"+i).style;
 cf[i].top=y-6+30*1.4*Math.sin(i*e*Math.PI/180)+"px";
 cf[i].left=x-6+30*1.4*Math.cos(i*e*Math.PI/180)+"px";
}
for (i=0; i < n; i++){
/*
 document.write('<div id="theDots'+i+'" class="handsanddotsstyle" style="background-color:'+dCol+'"><\/div>');
 cd[i]=document.getElementById("theDots"+i).style;
 cd[i].top=y+30*Math.sin(i*e*Math.PI/180)+"px";
 cd[i].left=x+30*Math.cos(i*e*Math.PI/180)+"px";
*/
}
for (i=0; i < h; i++){
 document.write('<div id="H'+i+'" class="handsanddotsstyle" style="background-color:'+hCol+'"><\/div>');
 ch[i]=document.getElementById("H"+i).style;
}
for (i=0; i < m; i++){
 document.write('<div id="M'+i+'" class="handsanddotsstyle" style="background-color:'+mCol+'"><\/div>');
 cm[i]=document.getElementById("M"+i).style;
}
for (i=0; i < s; i++){
 document.write('<div id="S'+i+'" class="handsanddotsstyle" style="background-color:'+sCol+'"><\/div>');
 cs[i]=document.getElementById("S"+i).style;
}

var dsp1=document.getElementById("amOrPm").style;
var dsp2=document.getElementById("theCities").style;
var dsp3=document.getElementById("theDate").style;
//var dsp4=document.getElementById("city").style;
var dsp5=document.getElementById("theClockLayer").style;
dsp1.top=y+"px";
dsp1.left=x-8+"px";
dsp2.top=y-80+"px";
dsp2.left=x-55+"px";
dsp3.top=y+55+"px";
dsp3.left=x-60+"px";
//dsp4.backgroundColor=bCol;
//dsp4.color=tCol;
//var currSkin="<%=skintype%>"

//var currSkin="aqua"

//dsp5.backgroundImage="url(/crm/images/"+currSkin+"/clock_bg.gif)"
dsp5.backgroundImage="url($image_path/clock_bg.gif)"
dsp5.backgroundRepeat="no-repeat"
dsp5.backgroundPosition="4px 38px"

function lcl(currIndex,localState){
	//zone=z.options[z.selectedIndex].value;
	//isItLocal=(z.options[0].selected)?true:false;
	zone=document.frmtimezone.city.options[currIndex].value;
	isItLocal=localState;
	plusMinus=(zone.charAt(0) == "-")?true:false;
	oddMinutes=(zone.indexOf(".") != -1)?true:false;
	if (oddMinutes){
	 getOddMinutes=zone.substring(zone.indexOf(".")+1,zone.length)
	}
	
	addHours=(oddMinutes)?parseInt(zone.substring(0,zone.indexOf("."))):parseInt(zone)
	if (plusMinus){
	 addOddMinutes=(oddMinutes)?parseInt(-getOddMinutes):0;
	} else{
	 addOddMinutes=(oddMinutes)?parseInt(getOddMinutes):0;
	}
	
	set_cookie("timezone",currIndex)
}

function ClockAndAssign(){
	hourAdjust=0;
	dayAdjust=0;
	monthAdjust=0;
	now=new Date();
	//ofst=now.getTimezoneOffset()/60;
	
	secs=now.getSeconds();
	sec=Math.PI*(secs-15)/30;
	
	mins=(isItLocal)?now.getMinutes():now.getUTCMinutes();
	if (oddMinutes){ 
	 mins=eval(mins+addOddMinutes);
	}
	min=Math.PI*(mins-15)/30;
	if (mins<0){
	 mins+=60;hourAdjust=-1;
	}
	if (mins>59){
	 mins-=60;hourAdjust=1;
	}
	
	//hr=(isItLocal)?now.getHours()+hourAdjust:(now.getHours()+parseInt(ofst))+parseInt(zone)+hourAdjust;
	hr=(isItLocal)?now.getHours()+hourAdjust:now.getUTCHours()+addHours+hourAdjust
	hrs=Math.PI*(hr-3)/6+Math.PI*parseInt(now.getMinutes())/360;
	
	/*
	if (hr<0){
	 hr+=24;
	 dayAdjust=-1;
	}
	if (hr>23){
	 hr-=24;
	 dayAdjust=1;
	}
	*/
	
	if (!isItLocal){
	  if (addHours<0){
		if(now.getUTCHours()+parseInt(addHours)<0)
		  dayAdjust-=1
	  } else{
		if(now.getUTCHours()+parseInt(addHours)>23)
		  dayAdjust+=1
	  }
	}
	
	day=now.getDate()+dayAdjust;
	
	if (day<1){
	 day+=daysInMonth; 
	 monthAdjust=-1;
	}
	if (day>daysInMonth){
	 day-=daysInMonth; 
	 monthAdjust=1;
	}
	
	month=parseInt(now.getMonth()+1+monthAdjust);
	
	if (month==2){
	 daysInMonth=28;
	}
	year=now.getYear();
	if (year<2000){
	 year=year+1900;
	}
	leap_year=(eval(year%4)==0)?true:false;
	if (leap_year&&month==2){
	 daysInMonth=29;
	}
	if (month<1){
	 month+=12;
	 year--;
	}
	if (month>12){
	 month-=12;
	 year++;
	}
	//todaysDate=day+"/"+month+"/"+year;
	todaysDate=mon[month-1]+" "+day+", "+year;
	
	if (hr<0) hr+=24;
	if (hr>23) hr-=24;
	
	ampm=(hr>11)?"PM":"AM";
	
	for (i=0;i<s;i++){
	 cs[i].top=y+(i*hDims)*Math.sin(sec)+"px";
	 cs[i].left=x+(i*hDims)*Math.cos(sec)+"px";
	}
	for (i=0;i<m;i++){
	 cm[i].top=y+(i*hDims)*Math.sin(min)+"px";
	 cm[i].left=x+(i*hDims)*Math.cos(min)+"px";
	}
	for (i=0;i<h;i++){
	 ch[i].top=y+(i*hDims)*Math.sin(hrs)+"px";
	 ch[i].left=x+(i*hDims)*Math.cos(hrs)+"px";
	}
	
	document.getElementById("amOrPm").firstChild.data=ampm;
	
//	if (hr.toString().length==1) hr="0"+hr
	if (hr==0) hr=12
	else if (hr>11) hr-=12;
	
	if (mins.toString().length==1) mins="0"+mins;
	
	document.getElementById("theDate").firstChild.data=todaysDate+" "+hr+":"+mins+" "+ampm;
	setTimeout('ClockAndAssign()',100);
	}
	ClockAndAssign();
}

// Setting cookies
function set_cookie ( name, value, exp_y, exp_m, exp_d, path, domain, secure )
{
  var cookie_string = name + "=" + escape ( value );

  if (exp_y) //delete_cookie(name)
  {
    var expires = new Date ( exp_y, exp_m, exp_d );
    cookie_string += "; expires=" + expires.toGMTString();
  }

  if (path) cookie_string += "; path=" + escape ( path );
  if (domain) cookie_string += "; domain=" + escape ( domain );
  if (secure) cookie_string += "; secure";

  document.cookie = cookie_string;
}

// Retrieving cookies
function get_cookie(cookie_name)
{
  var results = document.cookie.match(cookie_name + '=(.*?)(;|$)');
  if (results) return (unescape(results[1]));
  else return null;
}

// Delete cookies 
function delete_cookie( cookie_name )
{
  var cookie_date = new Date ( );  // current date & time
  cookie_date.setTime ( cookie_date.getTime() - 1 );
  document.cookie = cookie_name += "=; expires=" + cookie_date.toGMTString();
}

if (get_cookie("timezone")==null || get_cookie("timezone")==false || get_cookie("timezone")<0 || get_cookie("timezone")=="1") {
	lcl(0,true)
} else {
	lcl(get_cookie("timezone"),false)
	document.frmtimezone.city.options[get_cookie("timezone")].selected=true
}


//-->
</script>
</div>
{/literal}
{/tikimodule}