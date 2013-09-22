{* $Id$ *}

<h1>{tr}Set up Wiki environment{/tr}</h1>

{tr}Set up your Wiki environment{/tr}
<div style="float:left; width:60px"><img src="img/icons/large/wikipages48x48.png" alt="{tr}Set up your Wiki environment{/tr}" /></div>
<div align="left" style="margin-top:1em;">
<fieldset>
	<legend>{tr}Wiki environment{/tr}</legend>
	{tr}Auto TOC will automatically generate 2 Table Of Contents, One in the wiki page and one floating when scrolling down the page. Enable fast(!) header navigation{/tr}. 
	{preference name=wiki_auto_toc}
	{tr}See also{/tr} <a href="tiki-admin.php?page=wiki&alt=Wiki#content1" target="_blank">{tr}Wiki admin panel{/tr}</a>
<br>
<br>
	{tr}Tiki content categories are powerful. You can group objects by a common topic (category). You can add permissions to them. Content access can be controlled, and e.g. only granted to a certain group{/tr}
	{preference name=feature_categories}
	{tr}See also{/tr} <a href="https://doc.tiki.org/Category" target="_blank">{tr}Category{/tr} @ doc.tiki.org</a>
<br>
<br>
	{tr}Structures allow grouping many wiki pages together with several extra options{/tr}.
	{preference name=feature_wiki_structure}
	{tr}See also{/tr} <a href="tiki-admin.php?page=wiki&cookietab=2" target="_blank">{tr}Wiki admin feature panel{/tr}</a>
<br>
<br>
	{tr}jCapture enables recording of screen capture or screen casts (video), directly into the wiki page. Look for the <img src="img/icons/camera.png" /> icon in the editor toolbar{/tr}.<br> 
	{tr}Requires Java{/tr}. <a href="https://www.java.com/verify/" target="_blank">Verify your Java installation</a>.<br>
	
	{preference name=feature_jcapture}
	{tr}See also{/tr} <a href="tiki-admin.php?page=features&alt=Features" target="_blank">{tr}Features admin panel{/tr}</a>
	
</fieldset>

</div>
