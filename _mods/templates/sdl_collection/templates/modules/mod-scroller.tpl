<div class="box">
<div class="box-title">
{tr}Link Scroller{/tr}
</div>
<div class="box-data">
{literal}
<script language="JavaScript1.2">

//Translucent scroller- By Dynamic Drive
//For full source code and more DHTML scripts, visit http://www.dynamicdrive.com
//This credit MUST stay intact for use

var scroller_width='160px'
var scroller_height='50px'
var bgcolor='#ffffff'
var pause=3000 //SET PAUSE BETWEEN SLIDE (3000=3 seconds)

var scrollercontent=new Array()
//Define scroller contents. Extend or contract array as needed
scrollercontent[0]='<a class="link" href="http://www.nycris.org.uk" target="_new"><b>www.nycris.org.uk</b></a><br />Our main community site.'
scrollercontent[1]='<a class="link" href="http://lthweb" target="_new"><b>lthweb</b></a><br />The Leeds teaching hospital intranet.'
scrollercontent[2]='<a class="link" href="http://www.nhs.uk" target="_new"><b>www.nhs.uk</b></a><br />The National Health Service public site.'
scrollercontent[3]='<a class="link" href="http://www.nhsdirect.nhs.uk/" target="_new"><b>www.nhsdirect.nhs.uk</b></a><br />The NHS Direct site for information on health.'
scrollercontent[4]='<a class="link" href="http://doc.tikiwiki.org/tiki-index.php?page=Documentation" target="_new"><b>doc.tikiwiki.org</b></a><br />TikiWiki user documentation site.'
//scrollercontent[5]='<a class="link" href="http://" target="_new"><b></b></a><br />'
//scrollercontent[6]='<a class="link" href="http://" target="_new"><b></b></a><br />'
//scrollercontent[7]='<a class="link" href="http://" target="_new"><b></b></a><br />'
//scrollercontent[8]='<a class="link" href="http://" target="_new"><b></b></a><br />'
////NO need to edit beyond here/////////////

var ie4=document.all
var dom=document.getElementById&&navigator.userAgent.indexOf("Opera")==-1

if (ie4||dom)
document.write('<div style="position:relative;width:'+scroller_width+';height:'+scroller_height+';overflow:hidden"><div id="canvas0" style="position:absolute;background-color:'+bgcolor+';width:'+scroller_width+';height:'+scroller_height+';top:'+scroller_height+';filter:alpha(opacity=20);-moz-opacity:0.2;"></div><div id="canvas1" style="position:absolute;background-color:'+bgcolor+';width:'+scroller_width+';height:'+scroller_height+';top:'+scroller_height+';filter:alpha(opacity=20);-moz-opacity:0.2;"></div></div>')
else if (document.layers){
document.write('<ilayer id=tickernsmain visibility=hide width='+scroller_width+' height='+scroller_height+' bgColor='+bgcolor+'><layer id=tickernssub width='+scroller_width+' height='+scroller_height+' left=0 top=0>'+scrollercontent[0]+'</layer></ilayer>')
}

var curpos=scroller_height*(1)
var degree=10
var curcanvas="canvas0"
var curindex=0
var nextindex=1

function moveslide(){
if (curpos>0){
curpos=Math.max(curpos-degree,0)
tempobj.style.top=curpos+"px"
}
else{
clearInterval(dropslide)
if (crossobj.filters)
crossobj.filters.alpha.opacity=100
else if (crossobj.style.MozOpacity)
crossobj.style.MozOpacity=1
nextcanvas=(curcanvas=="canvas0")? "canvas0" : "canvas1"
tempobj=ie4? eval("document.all."+nextcanvas) : document.getElementById(nextcanvas)
tempobj.innerHTML=scrollercontent[curindex]
nextindex=(nextindex<scrollercontent.length-1)? nextindex+1 : 0
setTimeout("rotateslide()",pause)
}
}

function rotateslide(){
if (ie4||dom){
resetit(curcanvas)
crossobj=tempobj=ie4? eval("document.all."+curcanvas) : document.getElementById(curcanvas)
crossobj.style.zIndex++
if (crossobj.filters)
document.all.canvas0.filters.alpha.opacity=document.all.canvas1.filters.alpha.opacity=20
else if (crossobj.style.MozOpacity)
document.getElementById("canvas0").style.MozOpacity=document.getElementById("canvas1").style.MozOpacity=0.2
var temp='setInterval("moveslide()",50)'
dropslide=eval(temp)
curcanvas=(curcanvas=="canvas0")? "canvas1" : "canvas0"
}
else if (document.layers){
crossobj.document.write(scrollercontent[currindex])
crossobj.document.close()
}
curindex=(curindex<scrollercontent.length-1)? curindex+1 : 0
}

function resetit(what){
curpos=parseInt(scroller_height)*(1)
var crossobj=ie4? eval("document.all."+what) : document.getElementById(what)
crossobj.style.top=curpos+"px"
}

function startit(){
// Find random starting place
curindex=(Math.round(Math.random()*(scrollercontent.length-1)))
// set next slide ready
nextindex=curindex
nextindex=(nextindex<scrollercontent.length-1)? nextindex+1 : 0

crossobj=ie4? eval("document.all."+curcanvas) : dom? document.getElementById(curcanvas) : document.tickernsmain.document.tickernssub
if (ie4||dom){
crossobj.innerHTML=scrollercontent[curindex]
rotateslide()
}
else{
document.tickernsmain.visibility='show'
curindex++
setInterval("rotateslide()",pause)
}
}

if (ie4||dom||document.layers)
window.onload=startit

</script>
{/literal}
</div>
</div>
