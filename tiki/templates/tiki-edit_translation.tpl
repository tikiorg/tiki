<h1>{tr}Edit translation: {/tr}<a href="tiki-index.php?page={$page|escape:url}">{$page}</a></h1>
{if $feature_help eq 'y'}
<a href="http://tikiwiki.org/tiki-index.php?page=TranslationEdit" target="tikihelp" class="tikihelp" title="{tr}Tikiwiki.org help{/tr}: {tr}edit translations{/tr}"><img border="0" alt="{tr}Help{/tr}" src="img/icons/help.gif" /></a>
{/if}
{if $feature_view_tpl eq 'y'}
<a href="tiki-edit_templates.php?template=templates/tiki-edit_translations.tpl" target="tikihelp" class="tikihelp" title="{tr}View template{/tr}: {tr}edit translations template{/tr}"><img border="0"  alt="{tr}Edit template{/tr}" src="img/icons/info.gif" /></a>
{/if}

{if $error}
	<div class="error">
	{if $error == "traLang"}
		{tr}You must specify the page language{/tr}
	{elseif $error == "srcExists"}
		{tr}The page doesn't exist{/tr}
	{elseif $error == "srcLang"}
		{tr}The page doesn't have a language{/tr}
	{elseif $error == "alreadyTrad"}
		{tr}The page has already a translation for this language{/tr}
	{elseif $error == "alreadySet"}
		{tr}The page is already in the set of translations{/tr}
	{/if}
	</div>
	<br />
{/if}

<form action="tiki-edit_translation.php" method="post">
<input type="hidden" name="page" value="{$page|escape}" />

<table class="normal">
<tr>
	<td class="heading">{tr}Language{/tr}</td>
	<td class="form"><select name="lang" size="1">
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

{if count($trads) > 1}
	<h2>{tr}Set of Translations{/tr}</h2>
	<table class="normal">
	<tr><td class="heading">{tr}Language{/tr}</td><td class="heading">{tr}Pages{/tr}</td><td class="heading">Actions</td></tr>
	{cycle values="odd,even" print=false}
	{section name=i loop=$trads}
	<tr class="{cycle}"><td>{$trads[i].langName}</td><td><a href="tiki-index.php?page={$trads[i].page|escape:url}">{$trads[i].page}</a></td>
	<td><input type="submit" name="detach" value="{tr}detach{/tr}" /><br /></td></tr>
	{/section}
	</table>
	<input name="srcName" size="80" type="text" value="{$srcName}" /><input type="submit" class="wikiaction"  value="{tr}add a page to the set{/tr}"/> 
{else}
	{tr}Translation of the page:{/tr} <input name="srcName" size="80" type="text" value="{$srcName}" />
	<br />
	<input type="submit" class="wikiaction"  value="{tr}go{/tr}"/> 
{/if}
</form>
