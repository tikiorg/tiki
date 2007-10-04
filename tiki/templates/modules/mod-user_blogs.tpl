{* $Header: /cvsroot/tikiwiki/tiki/templates/modules/mod-user_blogs.tpl,v 1.12 2007-10-04 22:17:47 nyloth Exp $ *}

{if $user}
    {if $prefs.feature_blogs eq 'y'}
{if !isset($tpl_module_title)}{assign var=tpl_module_title value="{tr}My blogs{/tr}"}{/if}
{tikimodule title=$tpl_module_title name="user_blogs" flip=$module_params.flip decorations=$module_params.decorations}
	<table  border="0" cellpadding="0" cellspacing="0">
	{section name=ix loop=$modUserBlogs}
	    <tr>{if $nonums != 'y'}<td class="module" valign="top">{$smarty.section.ix.index_next})</td>{/if}
	    <td class="module"><a class="linkmodule" href="tiki-view_blog.php?blogId={$modUserBlogs[ix].blogId}">{$modUserBlogs[ix].title}</a></td></tr>
	{/section}
	</table>
	{/tikimodule}
    {/if}
{/if}
