{* $Header: /cvsroot/tikiwiki/tiki/templates/tiki-orphan_pages.tpl,v 1.27 2007-10-12 22:23:05 mose Exp $ *}

<h1><a href="tiki-orphan_pages.php" class="pagetitle">{tr}Orphan Pages{/tr}</a></h1>
{if $tiki_p_admin eq 'y'}
<a href="tiki-admin.php?page=wiki"><img src='pics/icons/wrench.png' border='0' alt="{tr}Admin Feature{/tr}" title="{tr}Admin Feature{/tr}" width='16' height='16' /></a>
{/if}

<table class="findtable">
<tr><td class="findtitle">{tr}Find{/tr}</td>
   <td class="findtitle">
   <form method="get" action="tiki-orphan_pages.php">
     <input type="text" name="find" value="{$find|escape}" />
     {tr}Exact&nbsp;match{/tr} <input type="checkbox" name="exact_match" {if $exact_match ne 'n'}checked="checked"{/if}/>
     {tr}Number of lines{/tr} <input type="text" name="numrows" value="{$maxRecords|escape}" size="3" />
     <input type="submit" name="search" value="{tr}Find{/tr}" />
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

