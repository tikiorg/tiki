{* $Id$ *}

<h1>{tr}Set up Wiki environment{/tr}</h1>

{tr}Set up your Wiki environment{/tr}
<div style="float:left; width:60px"><img src="img/icons/large/icon-configuration48x48.png" alt="{tr}Set up your Wiki environment{/tr}"></div>
<div align="left" style="margin-top:1em;">
<fieldset>
	<legend>{tr}Wiki environment{/tr}</legend>
	Auto TOC will automatically generate 2 Table Of Contents, One in the wiki page and one floating when scrolling down the page. Enable fast(!) header navigation. 
	{preference name=wiki_auto_toc}
	{tr}See also{/tr} <a href="tiki-admin.php?page=wiki&alt=Wiki#content1" target="_blank">{tr}Wiki admin panel{/tr}</a>
<br>
<br>
	Enable using the same wiki page name in different contexts.
	{preference name=namespace_enabled}
	{preference name=namespace_separator}
	{tr}See also{/tr} <a href="tiki-admin.php?page=wiki&alt=Wiki#content2" target="_blank">{tr}Wiki admin feature panel{/tr}</a>
<br>
<br>
	jCapture enables recording of screen capture or screen casts (video), directly into the wiki page. Look for the <img src="img/icons/camera.png" /> icon in the editor toolbar. Requires Java.
	{preference name=feature_jcapture}
</fieldset>

</div>
