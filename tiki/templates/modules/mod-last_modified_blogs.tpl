{* $Header: /cvsroot/tikiwiki/tiki/templates/modules/mod-last_modified_blogs.tpl,v 1.6 2003-10-20 01:13:16 zaufi Exp $ *}

{if $feature_blogs eq 'y'}
<div class="box">
<div class="box-title">
{include file="modules/module-title.tpl" module_title="{tr}Last Modified blogs{/tr}" module_name="last_modified_blogs"}
</div>
<div class="box-data">
<table  border="0" cellpadding="0" cellspacing="0">
{section name=ix loop=$modLastModifiedBlogs}
<tr>
{if $nonums != 'y'}<td class="module">{$smarty.section.ix.index_next})</td>{/if}
<td class="module"><a class="linkmodule" href="tiki-view_blog.php?blogId={$modLastModifiedBlogs[ix].blogId}">{$modLastModifiedBlogs[ix].title}</a></td></tr>
{/section}
</table>
</div>
</div>
{/if}