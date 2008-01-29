{* $Header: /cvsroot/tikiwiki/tiki/templates/tiki-orphan_pages.tpl,v 1.27.2.3 2008-01-29 18:46:11 luciash Exp $ *}

<h1><a href="tiki-orphan_pages.php" class="pagetitle">{tr}Orphan Pages{/tr}</a></h1>
{if $tiki_p_admin eq 'y'}
<a href="tiki-admin.php?page=wiki"><img src='pics/icons/wrench.png' border='0' alt="{tr}Admin Feature{/tr}" title="{tr}Admin Feature{/tr}" width='16' height='16' /></a>
{/if}

{include file="find.tpl"}

<div id="tiki-listpages-content">
{include file="tiki-listpages_content.tpl"}
</div>

