{* $Header: /cvsroot/tikiwiki/tiki/templates/modules/mod-top_active_blogs.tpl,v 1.8 2003-11-23 04:01:52 gmuslera Exp $ *}

{if $feature_blogs eq 'y'}
{tikimodule title="{tr}Most Active blogs{/tr}" name="top_active_blogs"}
<table  border="0" cellpadding="0" cellspacing="0">
{section name=ix loop=$modTopActiveBlogs}
<tr>{if $nonums != 'y'}<td class="module" valign="top">{$smarty.section.ix.index_next})</td>{/if}
<td class="module"><a class="linkmodule" href="tiki-view_blog.php?blogId={$modTopActiveBlogs[ix].blogId}">{$modTopActiveBlogs[ix].title}</a></td></tr>
{/section}
</table>
{/tikimodule}
{/if}