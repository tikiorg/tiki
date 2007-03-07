{* $Header: /cvsroot/tikiwiki/tiki/templates/tiki-listpages.tpl,v 1.44 2007-03-07 15:49:59 sylvieg Exp $ *}

<h1><a href="tiki-listpages.php" class="pagetitle">{tr}Pages{/tr}</a></h1>
{if $tiki_p_admin eq 'y'}
<a href="tiki-admin.php?page=wiki"><img src='pics/icons/wrench.png' border='0' alt="{tr}configure listing{/tr}" title="{tr}configure listing{/tr}" width='16' height='16' /></a>
{/if}
<form method="post" action="tiki-listpages.php">
<table class="findtable">
<tr><td class="findtitle">{tr}Find{/tr}</td>
   <td class="findtitle">
     <input type="text" name="find" value="{$find|escape}" />
     {tr}Exact&nbsp;match{/tr} <input type="checkbox" name="exact_match" {if $exact_match ne 'n'}checked="checked"{/if}/>
     {if $feature_multilingual eq 'y'}
     <select name="find_lang">
	 <option value='' {if $find_lang eq ''}selected="selected"{/if}>{tr}all{/tr}</option>
     {section name=ix loop=$languages}
     {if count($available_languages) == 0 || in_array($languages[ix].value, $available_languages)}
     <option value="{$languages[ix].value|escape}" {if $find_lang eq $languages[ix].value}selected="selected"{/if}>{tr}{$languages[ix].name}{/tr}</option>
	 {/if}
     {/section}
     </select>
     {/if}
     {if $feature_categories eq 'y'}
     <select name="find_categId">
     <option value='' {if $find_categId eq ''}selected="selected"{/if}>{tr}all{/tr}</option>
     {section name=ix loop=$categories}
     <option value="{$categories[ix].categId|escape}" {if $find_categId eq $categories[ix].categId}selected="selected"{/if}>{tr}{$categories[ix].categpath}{/tr}</option>
     {/section}
     {/if}
   </td><td rowspan="2" class="findtitle">
    <input type="submit" name="search" value="{tr}find{/tr}" />
   </td>
</tr>
<tr><td class="findtitle">{tr}Number of lines{/tr}</td><td  class="findtitle"><input type="text" name="numrows" value="{$maxRecords|escape}" size="3" /></td></tr>
</table>
<input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
</form>

<div align="center">
<form name="checkform" method="post" action="{$smarty.server.PHP_SELF}">
<input type="hidden" name="offset" value="{$offset|escape}" />
<input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
<input type="hidden" name="find" value="{$find|escape}" />
</form>

<div id="tiki-listpages-content">
{include file="tiki-listpages_content.tpl"}
</div>
</div>

