{* $Id$ *}

<h1><a href="tiki-listpages.php" class="pagetitle">{tr}Pages{/tr}</a>
{if $tiki_p_admin eq 'y'}
<a href="tiki-admin.php?page=wiki">{icon _id='wrench' alt="{tr}Admin Feature{/tr}"}</a>
{/if}
</h1>

{include file="find.tpl" find_show_languages='y' find_show_categories='y' find_show_num_rows='y'}

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

