{* $Header: /cvsroot/tikiwiki/tiki/templates/modules/mod-top_visited_blogs.tpl,v 1.9 2003-11-23 04:01:52 gmuslera Exp $ *}

{if $feature_blogs eq 'y'}
    {tikimodule title="{tr}Most visited blogs{/tr}" name="top_visited_blogs"}
    <table  border="0" cellpadding="0" cellspacing="0">
    {section name=ix loop=$modTopVisitedBlogs}
	<tr>{if $nonums != 'y'}<td class="module" valign="top">{$smarty.section.ix.index_next})</td>{/if}
	<td class="module"><a class="linkmodule" href="tiki-view_blog.php?blogId={$modTopVisitedBlogs[ix].blogId}">{$modTopVisitedBlogs[ix].title}</a></td></tr>
    {/section}
    </table>
    {/tikimodule}
{/if}
