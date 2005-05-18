{* $Header: /cvsroot/tikiwiki/tiki/templates/modules/mod-user_image_galleries.tpl,v 1.9 2005-05-18 11:03:32 mose Exp $ *}

{if $user}
    {if $feature_galleries eq 'y'}
	{tikimodule title="{tr}My galleries{/tr}" name="user_image_galleries" flip=$module_params.flip decorations=$module_params.decorations}
	<table  border="0" cellpadding="0" cellspacing="0">
	{section name=ix loop=$modUserG}
	    <tr>{if $nonums != 'y'}<td class="module" valign="top">{$smarty.section.ix.index_next})</td>{/if}
	    <td class="module"><a class="linkmodule" href="tiki-browse_gallery.php?galleryId={$modUserG[ix].galleryId}">{$modUserG[ix].name}</a></td></tr>
	{/section}
	</table>
	{/tikimodule}
    {/if}
{/if}
