<h1>{tr}Edit Translation:{/tr}&nbsp;
{if $type == "wiki page"}
	<a href="tiki-index.php?page={$name|escape:url}">{$name}</a>
	{assign var="title" value="{tr}Pages{/tr}"}
{else}
	<a href="tiki-read_article.php?articleId={$id}">{$name}</a>
	{assign var="title" value="{tr}Articles{/tr}"}
{/if}
</h1>
{if $feature_help eq 'y'}
<a href="http://tikiwiki.org/tiki-index.php?page=TranslationDoc" target="tikihelp" class="tikihelp" title="{tr}Tikiwiki.org help{/tr}: {tr}edit translations{/tr}"><img border="0" alt="{tr}Help{/tr}" src="img/icons/help.gif" /></a>
{/if}
{if $feature_view_tpl eq 'y'}
<a href="tiki-edit_templates.php?template=templates/tiki-edit_translations.tpl" target="tikihelp" class="tikihelp" title="{tr}View template{/tr}: {tr}edit translations template{/tr}"><img border="0"  alt="{tr}Edit template{/tr}" src="img/icons/info.gif" /></a>
{/if}

{if $error}
	<div class="error">
	{if $error == "traLang"}
		{tr}You must specify the object language{/tr}
	{elseif $error == "srcExists"}
		{tr}The object doesn't exist{/tr}
	{elseif $error == "srcLang"}
		{tr}The object doesn't have a language{/tr}
	{elseif $error == "alreadyTrad"}
		{tr}The object has already a translation for this language{/tr}
	{elseif $error == "alreadySet"}
		{tr}The object is already in the set of translations{/tr}
	{/if}
	</div>
	<br />
{/if}

<form action="tiki-edit_translation.php" method="post">
<input type="hidden" name="id" value="{$id}" />
<input type="hidden" name="type" value="{$type|escape}" />

<h2>{tr}Language{/tr}</h2>
<table>
<tr>
	<td><select name="lang" size="1">
	{if !$lang || $lang == "NULL"}
	<option value="">{tr}Unknown{/tr}</option>
	{/if}
	{section name=ix loop=$languages}
	<option value="{$languages[ix].value|escape}"{if $lang eq $languages[ix].value} selected="selected"{/if}>{$languages[ix].name}</option>
	{/section}</select>
	</td>
	{if count(trads)}
		<td class="form">
		<input type="submit" value="{tr}Save{/tr}" /><br />
		</td>
	{/if}
</tr>
</table>
<br />

<h2>{tr}Set of Translations{/tr}</h2>

{if count($trads) > 1}
	<table class="normal">
	<tr><td class="heading">{tr}Language{/tr}</td><td class="heading">{$title}</td><td class="heading">Actions</td></tr>
	{cycle values="odd,even" print=false}
	{section name=i loop=$trads}
	<tr class="{cycle}">
		<td>{$trads[i].langName}</td>
		<td>{if $type == 'wiki page'}<a href="tiki-index.php?page={$trads[i].objName|escape:url}">{else}<a href="tiki-read_article.php?articleId={$trads[i].objId|escape:url}">{/if}{$trads[i].objName}</a></td>
		<td><a class="link" href="tiki-edit_translation.php?detach&amp;id={$id|escape:url}&amp;srcId={$trads[i].objId|escape:url}&amp;type={$type|escape:url}"><img src='img/icons2/delete.gif' border='0' alt='{tr}detach{/tr}' title='{tr}detach{/tr}' /></a>
	</td></tr>
	{/section}
	</table>
	<table><tr><td>

	{if $articles}
		<select name="srcId">{section name=ix loop=$articles}<option value="{$articles[ix].articleId|escape}" {if $articles[ix].articleId == $srcId}chacked="checked"{/if}>{$articles[ix].title|truncate:40:"(...)":true}</option>{/section}</select>
	{else}
		<input name="srcName" size="60" type="text" value="{$srcName}" />
	{/if}
	&nbsp;<input type="submit" class="wikiaction"  value="{tr}add to the set{/tr}"/>
	</td></tr></table>

{else} {* first translation *}
	{tr}Translation of:{/tr}&nbsp;
	{if $articles}
		<select name="srcId">{section name=ix loop=$articles}<option value="{$articles[ix].articleId|escape}">{$articles[ix].title|truncate:40:"(...)":true}</option>{/section}</select>
	{else}
		<input name="srcName" size="60" type="text" value="{$srcName}" />
	{/if}
	&nbsp;<input type="submit" class="wikiaction"  value="{tr}go{/tr}"/>
{/if}
</form>
