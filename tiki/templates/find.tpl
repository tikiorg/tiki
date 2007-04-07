{* $Header: /cvsroot/tikiwiki/tiki/templates/find.tpl,v 1.5 2007-04-07 00:15:08 nyloth Exp $ *}
<div align="center">
<form method="post" action="{$smarty.server.PHP_SELF}">

{if !empty($sort_mode)}<input type="hidden" name="sort_mode" value="{$sort_mode}" />{/if}
{if !empty($offset)}<input type="hidden" name="offset" value="{$offset}" />{/if}

<table class="findtable">
<tr>
<td class="findtitle">{if empty($what)}{tr}Find{/tr}{else}{tr}{$what}{/tr}{/if}</td>
<td class="findtitle">
	<input type="text" name="find" value="{$find|escape}" />
	{if isset($exact_match)}{tr}Exact&nbsp;match{/tr}<input type="checkbox" name="exact_match" {if $exact_match ne 'n'}checked="checked"{/if}/>{/if}
</td>

{if !empty($types)}
	<td class="findtitle">
		<select name="type">
		<option value='' {if $find_type eq ''}selected="selected"{/if}>{tr}any type{/tr}</option>
		{section name=t loop=$types}
			<option value="{$types[t].type|escape}" {if $find_type eq $types[t].type}selected="selected"{/if}>{tr}{$types[t].type}{/tr}</option>
		{/section}
		</select>
	</td>
{/if}

{if !empty($topics)}
	<td class="findtitle">
		<select name="topic">
		<option value='' {if $find_topic eq ''}selected="selected"{/if}>{tr}all topic{/tr}</option>
		{section name=ix loop=$topics}
			<option value="{$topics[ix].topicId|escape}" {if $find_topic eq $topics[ix].topicId}selected="selected"{/if}>{tr}{$topics[ix].name}{/tr}</option>
		{/section}
		</select>
	</td>
{/if}

{if $feature_multilingual eq 'y'}
	<td class="findtitle">
		<select name="lang">
		<option value='' {if $find_lang eq ''}selected="selected"{/if}>{tr}any language{/tr}</option>
		{section name=ix loop=$languages}
			{if count($available_languages) == 0 || in_array($languages[ix].value, $available_languages)}
			<option value="{$languages[ix].value|escape}" {if $find_lang eq $languages[ix].value}selected="selected"{/if}>{tr}{$languages[ix].name}{/tr}</option>
			{/if}
		{/section}
		</select>
	</td>
{/if}

{if $feature_categories eq 'y'}
	<td class="findtitle">
		<select name="categId">
		<option value='' {if $find_categId eq ''}selected="selected"{/if}>{tr}any category{/tr}</option>
		{section name=ix loop=$categories}
			<option value="{$categories[ix].categId|escape}" {if $find_categId eq $categories[ix].categId}selected="selected"{/if}>{tr}{$categories[ix].categpath}{/tr}</option>
		{/section}
		</select>
	</td>
{/if}

<td class="findtitle">{tr}Number of displayed rows{/tr}</td><td  class="findtitle"><input type="text" name="maxRecords" value="{$maxRecords|escape}" size="3" /></td>

<td class="findtitle"><input type="submit" name="search" value="{tr}find{/tr}" /></td>
</tr>
</table>

</form>
</div>
 
