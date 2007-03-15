<h1><a href="tiki-mods.php" class="pagetitle">{tr}Package Config{/tr}</a></h1>

<div class="wikitext">
<h2>Configure <br />
{$type} <i>{$package}</i></h2>
<div class="wikitext">
{$help}
</div>
<form action="tiki-mods.php" method="post">
<input type="hidden" name="action" value="configuration" />
<input type="hidden" name="package" value="{$type}-{$package}" />
<input type="hidden" name="type" value="{$type}" />
<table class="formcolor">
{foreach key=k item=i from=$info->configuration}
<tr><td>{$i[0]}</td><td><input type="text" name="conf[{$i[1]}]" value="{$i[2]}" /></td></tr>
{/foreach}
<tr><td>&nbsp;</td><td><input type="submit" name="go" value="configure" />
</table>
</form>
</div>

