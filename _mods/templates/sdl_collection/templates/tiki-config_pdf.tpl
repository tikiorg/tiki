<a class="pagetitle" href="tiki-config_pdf.php">{tr}Create PDF{/tr}</a><br /><br />
<div class="cbox">
<div class="cbox-title">
{tr}PDF Settings{/tr}
</div>
<div class="cbox-data">
<form method="post" action="tiki-config_pdf.php">
<input type="hidden" name="convertpages" value="{$form_convertpages|escape}" />
<input type="hidden" name="find" value="{$find|escape}" />
<table>
<tr><td class="form">{tr}Font{/tr}:</td><td class="form"><input type="text" name="font" value="{$font|escape}" /></td></tr>
<tr><td class="form">{tr}Text Height{/tr}:</td><td class="form"><input type="text" name="textheight" value="{$textheight|escape}" /></td></tr>
<tr><td class="form">{tr}Height of Top Heading{/tr}:</td><td class="form"><input type="text" name="h1height" value="{$h1height|escape}" /></td></tr>
<tr><td class="form">{tr}Height of Mid Heading{/tr}:</td><td class="form"><input type="text" name="h2height" value="{$h2height|escape}" /></td></tr>
<tr><td class="form">{tr}Height of Inner Heading{/tr}:</td><td class="form"><input type="text" name="h3height" value="{$h3height|escape}" /></td></tr>
<tr><td class="form">{tr}Tb Height{/tr}:</td><td class="form"><input type="text" name="tbheight" value="{$tbheight|escape}" /></td></tr>
<tr><td class="form">{tr}Imagescale{/tr}:</td><td class="form"><input type="text" name="imagescale" value="{$imagescale|escape}" /></td></tr>
<tr><td class="form">{tr}Automatic Page Breaks{/tr}:</td><td class="form"><input type="checkbox" {if $autobreak eq 'on'}checked="checked"{/if} name="autobreak" /></td></tr>
<tr><td align="center" colspan="2" class="form"><input type="submit" name="send" value="{tr}Send{/tr}" /></td></tr>
</table>
</form>
</div>
</div>
<br />

<div class="cbox">
<div class="cbox-title">
{tr}Filter{/tr}
</div>
<div class="cbox-data">
<form action="tiki-config_pdf.php" method="post">
<input type="hidden" name="convertpages" value="{$form_convertpages|escape}" />
<input type="hidden" name="font" value="{$font|escape}" />
<input type="hidden" name="textheight" value="{$textheight|escape}" />
<input type="hidden" name="find" value="{$find|escape}" />
<input type="hidden" name="h1height" value="{$h1height|escape}" />
<input type="hidden" name="h2height" value="{$h2height|escape}" />
<input type="hidden" name="h3height" value="{$h3height|escape}" />
<input type="hidden" name="tbheight" value="{$tbheight|escape}" />
<input type="hidden" name="imagescale" value="{$imagescale|escape}" />
<input type="hidden" name="autobreak" value="{$autobreak|escape}" />
{tr}Search{/tr}:<input type="text" name="find" value="{$find|escape}" /><input type="submit" name="filter" value="{tr}Go{/tr}" /><br />
</form>
</div>
</div>
<br />

<div class="cbox">
<div class="cbox-title">
{tr}Select Wiki Pages{/tr}
</div>
<div class="cbox-data">
<br />
<form action="tiki-config_pdf.php" method="post">
<input type="hidden" name="convertpages" value="{$form_convertpages|escape}" />
<input type="hidden" name="font" value="{$font|escape}" />
<input type="hidden" name="textheight" value="{$textheight|escape}" />
<input type="hidden" name="find" value="{$find|escape}" />
<input type="hidden" name="h1height" value="{$h1height|escape}" />
<input type="hidden" name="h2height" value="{$h2height|escape}" />
<input type="hidden" name="h3height" value="{$h3height|escape}" />
<input type="hidden" name="tbheight" value="{$tbheight|escape}" />
<input type="hidden" name="imagescale" value="{$imagescale|escape}" />
<input type="hidden" name="autobreak" value="{$autobreak|escape}" />
<table class="normal">
<tr><td class="normal" align="center">
<select name="addpageName[]" size="10" multiple="multiple">
{section name=ix loop=$pages}
<option value="{$pages[ix].pageName|escape}">{$pages[ix].pageName|truncate:60:"..."}</option>
{/section}
</select>
</td><td class="normal" align="center">
<input type="submit" name="addpage" value="{tr}Add Page{/tr} ---&gt;" /><br />
<input type="submit" name="rempage" value="&lt;--- {tr}Remove Page{/tr}" /><br />
<input type="submit" name="clearpages" value="{tr}Reset{/tr}" />
</td><td class="normal" align="center">
<select name="rempageName[]" size="10" multiple="multiple">
{foreach from=$convertpages item=ix}
<option value="{$ix|escape}">{$ix}</option>
{/foreach}
</select>
</td></tr>
</table>
</form>
</div>
</div>

<div class="cbox">
<div class="cbox-title">
{tr}Create PDF{/tr}
</div>
<div class="cbox-data" align="center">
<form action="tiki-export_pdf.php" method="post">
<input type="hidden" name="convertpages" value="{$form_convertpages|escape}" />
<input type="hidden" name="font" value="{$font|escape}" />
<input type="hidden" name="textheight" value="{$textheight|escape}" />
<input type="hidden" name="find" value="{$find|escape}" />
<input type="hidden" name="h1height" value="{$h1height|escape}" />
<input type="hidden" name="h2height" value="{$h2height|escape}" />
<input type="submit" name="create" value="{tr}Create{/tr}" />
<input type="hidden" name="h3height" value="{$h3height|escape}" />
<input type="hidden" name="tbheight" value="{$tbheight|escape}" />
<input type="hidden" name="imagescale" value="{$imagescale|escape}" />
<input type="hidden" name="autobreak" value="{$autobreak|escape}" />
</form>
</div>
</div>

<br />
