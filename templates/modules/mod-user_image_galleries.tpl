{* $Header: /cvsroot/tikiwiki/tiki/templates/modules/mod-user_image_galleries.tpl,v 1.7 2003-11-23 04:01:52 gmuslera Exp $ *}

{if $user}
    {if $feature_galleries eq 'y'}
	{tikimodule title="{tr}My galleries{/tr}" name="user_image_galleries"}
	<table  border="0" cellpadding="0" cellspacing="0">
	{section name=ix loop=$modUserG}
	    <tr>{if $nonums != 'y'}<td class="module" valign="top">{$smarty.section.ix.index_next})</td>{/if}
	    <td class="module"><a class="linkmodule" href="tiki-browse_gallery.php?galleryId={$modUserG[ix].galleryId}">{$modUserG[ix].name}</a></td></tr>
	{/section}
	</table>
	{/tikimodule}
    {/if}
{/if}