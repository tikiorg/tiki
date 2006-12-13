{* $Header: /cvsroot/tikiwiki/tiki/templates/tiki-listpages.tpl,v 1.42 2006-12-13 01:53:07 mose Exp $ *}

<h1><a href="tiki-listpages.php" class="pagetitle">{tr}Pages{/tr}</a></h1>
{if $tiki_p_admin eq 'y'}
<a href="tiki-admin.php?page=wiki"><img src='pics/icons/wrench.png' border='0' alt="{tr}configure listing{/tr}" title="{tr}configure listing{/tr}" width='16' height='16' /></a>
{/if}
<table class="findtable">
<tr><td class="findtitle">{tr}Find{/tr}</td>
   <td class="findtitle">
   <form method="get" action="tiki-listpages.php">
     <input type="text" name="find" value="{$find|escape}" />
     {tr}Exact&nbsp;match{/tr} <input type="checkbox" name="exact_match" {if $exact_match ne 'n'}checked="checked"{/if}/>
     {tr}Number of lines{/tr} <input type="text" name="numrows" value="{$maxRecords|escape}" size="3" />
     <input type="submit" name="search" value="{tr}find{/tr}" />
     <input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
   </form>
   </td>
</tr>
</table>

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

