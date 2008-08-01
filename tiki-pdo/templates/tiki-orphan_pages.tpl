{* $Id$ *}

<h1><a href="tiki-orphan_pages.php" class="pagetitle">{tr}Orphan Pages{/tr}</a>
{if $tiki_p_admin eq 'y'}
<a href="tiki-admin.php?page=wiki">{icon _id='wrench' alt="{tr}Admin Feature{/tr}"}</a>
{/if}
</h1>
{if $listpages or ($find ne '')}
{include file="find.tpl" find_show_languages='y' find_show_categories='y' find_show_num_rows='y'}
{/if}
<div id="tiki-listpages-content">
{include file="tiki-listpages_content.tpl"}
</div>

