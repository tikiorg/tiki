{* $Header: /cvsroot/tikiwiki/tiki/templates/modules/mod-user_blogs.tpl,v 1.10 2005-05-18 11:03:32 mose Exp $ *}

{if $user}
    {if $feature_blogs eq 'y'}
	{tikimodule title="{tr}My blogs{/tr}" name="user_blogs" flip=$module_params.flip decorations=$module_params.decorations}
	<table  border="0" cellpadding="0" cellspacing="0">
	{section name=ix loop=$modUserBlogs}
	    <tr>{if $nonums != 'y'}<td class="module" valign="top">{$smarty.section.ix.index_next})</td>{/if}
	    <td class="module"><a class="linkmodule" href="tiki-view_blog.php?blogId={$modUserBlogs[ix].blogId}">{$modUserBlogs[ix].title}</a></td></tr>
	{/section}
	</table>
	{/tikimodule}
    {/if}
{/if}
