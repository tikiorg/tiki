{* $Id$ *}

<h1><a href="tiki-orphan_pages.php" class="pagetitle">{tr}Orphan Pages{/tr}</a></h1>
{if $tiki_p_admin eq 'y'}
<a href="tiki-admin.php?page=wiki">{icon _id='wrench' alt="{tr}Admin Feature{/tr}"}</a>
{/if}

{include file="find.tpl"}

<div id="tiki-listpages-content">
{include file="tiki-listpages_content.tpl"}
</div>

