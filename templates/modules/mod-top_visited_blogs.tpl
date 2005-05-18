{* $Header: /cvsroot/tikiwiki/tiki/templates/modules/mod-top_visited_blogs.tpl,v 1.12 2005-05-18 11:03:31 mose Exp $ *}

{if $feature_blogs eq 'y'}
    {if $nonums eq 'y'}
    {eval var="{tr}Most `$module_rows` visited blogs{/tr}" assign="tpl_module_title"}
    {else}
    {eval var="{tr}Most visited blogs{/tr}" assign="tpl_module_title"}
    {/if}

    {tikimodule title=$tpl_module_title name="top_visited_blogs" flip=$module_params.flip decorations=$module_params.decorations}
    <table  border="0" cellpadding="0" cellspacing="0">
    {section name=ix loop=$modTopVisitedBlogs}
	<tr>{if $nonums != 'y'}<td class="module" valign="top">{$smarty.section.ix.index_next})</td>{/if}
	<td class="module"><a class="linkmodule" href="tiki-view_blog.php?blogId={$modTopVisitedBlogs[ix].blogId}">{$modTopVisitedBlogs[ix].title}</a></td></tr>
    {/section}
    </table>
    {/tikimodule}
{/if}
