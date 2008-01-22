<h1><a href="tiki-edit_translation.php?type={$type}&amp;{if $type eq 'wiki page'}page={$name|escape}{else}id={$id}{/if}">{tr}Translate:{/tr}&nbsp;{$name} ({$languageName}, {$langpage})</a>
{if $prefs.feature_help eq 'y'}
<a href="http://tikiwiki.org/tiki-index.php?page=Internationalization" target="tikihelp" class="tikihelp" title="{tr}Tikiwiki.org help{/tr}: {tr}Edit Translations{/tr}"><img src="img/icons/help.gif" border="0" height="16" width="16" alt='{tr}Help{/tr}' /></a>
{/if}
{if $prefs.feature_view_tpl eq 'y'}
<a href="tiki-edit_templates.php?template=tiki-edit_translation.tpl" target="tikihelp" class="tikihelp" title="{tr}View template{/tr}: {tr}Edit Translations Template{/tr}"><img src="img/icons/info.gif" border="0" width="16" height="16" alt='{tr}Edit template{/tr}' /></a>
{/if}
</h1>

{if $type == "wiki page"}
	<a href="tiki-index.php?page={$name|escape:url}" class="linkbut" title="{tr}View{/tr}">{tr}View page{/tr}</a>
{else}
	<a href="tiki-read_article.php?articleId={$id}" class="linkbut" title="{tr}View{/tr}">{tr}View{/tr}</a>
{/if}

{if $error}
	<div class="simplebox hoghlight">
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

<form method="post" action="tiki-editpage.php">
	{tr}Translate to language: {/tr}
		<select name="lang" size="1">
			{section name=ix loop=$languages}
			{if in_array($languages[ix].value, $prefs.available_languages) or $prefs.available_languages|@count eq 0}
			<option value="{$languages[ix].value|escape}">{$languages[ix].name}</option>
			{/if}
			{/section}
		</select>
	<br/>{tr}Translation name: {/tr}<input type="text" name="page"/><input type="hidden" name="translationOf" value="{$name|escape}"/>
	<br/><input type="submit" value="{tr}Create translation{/tr}"/></p>
	<textarea name="edit" style="display:none">^{$translate_message}^

{$pagedata|escape:'htmlall':'UTF-8'}</textarea>
</form>

<hr/>

{if !empty($langpage)}
<h3>{tr}All translations for this page{/tr}</h3>

{if $trads|@count > 1}
	<table class="normal">
	<tr><td class="heading">{tr}Language{/tr}</td><td class="heading">{tr}Page{/tr}</td><td class="heading">{tr}Actions{/tr}</td></tr>
	{cycle values="odd,even" print=false}
	{section name=i loop=$trads}
	<tr class="{cycle}">
		<td>{$trads[i].langName}</td>
		<td>{if $type == 'wiki page'}<a href="tiki-index.php?page={$trads[i].objName|escape:url}">{else}<a href="tiki-read_article.php?articleId={$trads[i].objId|escape:url}">{/if}{$trads[i].objName}</a></td>
		<td><a class="link" href="tiki-edit_translation.php?detach&amp;id={$id|escape:url}&amp;srcId={$trads[i].objId|escape:url}&amp;type={$type|escape:url}">{html_image file='pics/icons/cross.png' border='0' alt='{tr}detach{/tr}' title='{tr}detach{/tr}'}</a>
	</td></tr>
	{/section}
	</table>
{/if}

<form action="tiki-edit_translation.php" method="post">
<input type="hidden" name="id" value="{$id}" />
<input type="hidden" name="type" value="{$type|escape}" />

{if $trads|@count <= 1}
	{if $articles}
		<p>{tr}Select the article for which the current article is the translation.{/tr}</p>
		{tr}Translation of:{/tr}&nbsp;
	{else}
		<p>{tr}Enter the name of the page for which the current page is the translation.{/tr}</p>
		{tr}Translation of:{/tr}&nbsp;
	{/if}
{/if}
{if $trads|@count > 0}
	<p>{tr}Add existing page to the list above.{/tr}</p>
{/if}
{if $articles}
	<select name="srcId">{section name=ix loop=$articles}{if !empty($articles[ix].lang) and $langpage ne $articles[ix].lang}<option value="{$articles[ix].articleId|escape}" {if $articles[ix].articleId == $srcId}checked="checked"{/if}>{$articles[ix].title|truncate:80:"(...)":true}</option>{/if}{/section}</select>
{else}
	<select name="srcName">{section name=ix loop=$pages}{if !empty($pages[ix].lang) and $pages[ix].lang ne $langpage}<option value="{$pages[ix].pageName|escape}" {if $pages[ix].pageName == $srcId}checked="checked"{/if}>{$pages[ix].pageName|truncate:80:"(...)":true}</option>{/if}{/section}</select>
{/if}
&nbsp;
<input type="submit" class="wikiaction" name="set" value="{tr}Go{/tr}"/>
{/if}

</form>
