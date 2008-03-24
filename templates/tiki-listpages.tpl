{* $Header: /cvsroot/tikiwiki/tiki/templates/tiki-listpages.tpl,v 1.53.2.3 2008-01-30 15:33:51 nyloth Exp $ *}

<h1><a href="tiki-listpages.php" class="pagetitle">{tr}Pages{/tr}</a></h1>
{if $tiki_p_admin eq 'y'}
<a href="tiki-admin.php?page=wiki">{icon _id='wrench' alt="{tr}Admin Feature{/tr}"}</a>
{/if}
{include file="find.tpl"}

<div align="center">
<form name="checkform" method="post" action="{$smarty.server.PHP_SELF}">
<input type="hidden" name="offset" value="{$offset|escape}" />
<input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
<input type="hidden" name="find" value="{$find|escape}" />
<input type="hidden" name="maxRecords" value="{$maxRecords|escape}" />
</form>

<div id="tiki-listpages-content">
{include file="tiki-listpages_content.tpl"}
</div>
</div>

