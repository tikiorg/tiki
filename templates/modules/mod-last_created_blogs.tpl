{* $Header: /cvsroot/tikiwiki/tiki/templates/modules/mod-last_created_blogs.tpl,v 1.6 2003-10-20 01:13:16 zaufi Exp $ *}

{if $feature_blogs eq 'y'}
<div class="box">
<div class="box-title">
{include file="modules/module-title.tpl" module_title="{tr}Last Created blogs{/tr}" module_name="last_created_blogs"}
</div>
<div class="box-data">
<table  border="0" cellpadding="0" cellspacing="0">
{section name=ix loop=$modLastCreatedBlogs}
<tr>{if $nonums != 'y'}<td class="module" valign="top">{$smarty.section.ix.index_next})</td>{/if}
<td class="module">&nbsp;<a class="linkmodule" href="tiki-view_blog.php?blogId={$modLastCreatedBlogs[ix].blogId}">{$modLastCreatedBlogs[ix].title}</a></td></tr>
{/section}
</table>
</div>
</div>
{/if}