<a class="pagetitle" href="tiki-config_pdf.php">{tr}Create PDF{/tr}</a><br /><br />
<div class="tiki">
<div class="tiki-title">
{tr}PDF Settings{/tr}
</div>
<div class="tiki-content">
<form method="post" action="tiki-config_pdf.php">
<input type="hidden" name="convertpages" value="{$form_convertpages|escape}" />
<input type="hidden" name="find" value="{$find|escape}" />
<table>
<tr><td>{tr}Font{/tr}:</td><td><input type="text" name="font" value="{$font|escape}" /></td></tr>
<tr><td>{tr}Textheight{/tr}:</td><td><input type="text" name="textheight" value="{$textheight|escape}" /></td></tr>
<tr><td>{tr}Height of top Heading{/tr}:</td><td><input type="text" name="h1height" value="{$h1height|escape}" /></td></tr>
<tr><td>{tr}Height of mid Heading{/tr}:</td><td><input type="text" name="h2height" value="{$h2height|escape}" /></td></tr>
<tr><td>{tr}Height of inner Heading{/tr}:</td><td><input type="text" name="h3height" value="{$h3height|escape}" /></td></tr>
<tr><td>{tr}tbheight{/tr}:</td><td><input type="text" name="tbheight" value="{$tbheight|escape}" /></td></tr>
<tr><td>{tr}imagescale{/tr}:</td><td><input type="text" name="imagescale" value="{$imagescale|escape}" /></td></tr>
<tr><td>{tr}Automatic Page Breaks{/tr}:</td><td><input type="checkbox" {if $autobreak eq 'on'}checked="checked"{/if} name="autobreak" /></td></tr>
<tr><td align="center" colspan="2"><input type="submit" name="send" value="{tr}send{/tr}" /></td></tr>
</table>
</form>
</div>
</div>
<br />

<div class="tiki">
<div class="tiki-title">
{tr}Filter{/tr}
</div>
<div class="tiki-content">
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
{tr}filter{/tr}:<input type="text" name="find" value="{$find|escape}" /><input type="submit" name="filter" value="{tr}filter{/tr}" /><br />
</form>
</div>
</div>
<br />

<div class="tiki">
<div class="tiki-title">
{tr}Select Wiki Pages{/tr}
</div>
<div class="tiki-content">
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
<table>
<tr><td align="center">
<select name="addpageName[]" size="10" multiple="multiple">
{section name=ix loop=$pages}
<option value="{$pages[ix].pageName|escape}">{$pages[ix].pageName|truncate:60:"..."}</option>
{/section}
</select>
</td><td align="center">
<input type="submit" name="addpage" value="{tr}add page{/tr} ---&gt;" /><br />
<input type="submit" name="rempage" value="&lt;--- {tr}remove page{/tr}" /><br />
<input type="submit" name="clearpages" value="{tr}reset{/tr}" />
</td><td align="center">
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

<div class="tiki">
<div class="tiki-title">
{tr}Create PDF{/tr}
</div>
<div class="tiki-content" align="center">
<form action="tiki-export_pdf.php" method="post">
<input type="hidden" name="convertpages" value="{$form_convertpages|escape}" />
<input type="hidden" name="font" value="{$font|escape}" />
<input type="hidden" name="textheight" value="{$textheight|escape}" />
<input type="hidden" name="find" value="{$find|escape}" />
<input type="hidden" name="h1height" value="{$h1height|escape}" />
<input type="hidden" name="h2height" value="{$h2height|escape}" />
<input type="submit" name="create" value="{tr}create{/tr}" />
<input type="hidden" name="h3height" value="{$h3height|escape}" />
<input type="hidden" name="tbheight" value="{$tbheight|escape}" />
<input type="hidden" name="imagescale" value="{$imagescale|escape}" />
<input type="hidden" name="autobreak" value="{$autobreak|escape}" />
</form>
</div>
</div>

<br />
