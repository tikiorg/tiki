{* $Header: /cvsroot/tikiwiki/tiki/templates/tiki-listpages.tpl,v 1.46 2007-03-21 20:19:48 sylvieg Exp $ *}

<h1><a href="tiki-listpages.php" class="pagetitle">{tr}Pages{/tr}</a></h1>
{if $tiki_p_admin eq 'y'}
<a href="tiki-admin.php?page=wiki"><img src='pics/icons/wrench.png' border='0' alt="{tr}configure listing{/tr}" title="{tr}configure listing{/tr}" width='16' height='16' /></a>
{/if}
{include file="find.tpl"}

<div align="center">
<form name="checkform" method="post" action="{$smarty.server.PHP_SELF}">
<input type="hidden" name="offset" value="{$offset|escape}" />
<input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
<input type="hidden" name="find" value="{$find|escape}" />
{if !empty($maxRecords)}<input type="hidden" name="maxRecords" value="{$maxRecords}" />{/if}
</form>

<div id="tiki-listpages-content">
{include file="tiki-listpages_content.tpl"}
</div>
</div>

