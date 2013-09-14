{* $Id$ *}

<h1>{tr}Set up Wiki environment{/tr}</h1>

{tr}Set up your Wiki environment{/tr}
<div style="float:left; width:60px"><img src="img/icons/large/icon-configuration48x48.png" alt="{tr}Set up your Wiki environment{/tr}"></div>
<div align="left" style="margin-top:1em;">
<fieldset>
	<legend>{tr}Auto TOC{/tr}</legend>
	{preference name=wiki_auto_toc}
	<div class="adminoptionbox clearfix" id="wiki_auto_toc_childcontainer">
		<fieldset>
			<legend>{tr}Auto TOC options{/tr}</legend>
				{preference name=wiki_inline_auto_toc}
				{preference name=wiki_inline_toc_pos}
		</fieldset>
	</div>
	<br>
	<br>
	{tr}See also{/tr} <a href="tiki-admin.php?page=wiki&alt=Wiki#content1" target="_blank">{tr}Wiki admin panel{/tr}</a>
</fieldset>
<br>
<fieldset>
	<legend>{tr}Namespaces{/tr}</legend>
	{preference name=namespace_enabled}
	{preference name=namespace_separator}
	<input type="checkbox" name="hideNamespaceIndicators" {if isset($hideNamespaceIndicators)}checked="checked"{/if} /> {tr}Hide namespace indicators when listing pages.{/tr}
	<br>
	<br>
	{tr}See also{/tr} <a href="tiki-admin.php?page=wiki&alt=Wiki#content2" target="_blank">{tr}Wiki admin feature panel{/tr}</a>
</fieldset>

</div>
