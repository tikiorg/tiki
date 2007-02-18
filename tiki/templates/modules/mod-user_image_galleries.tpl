{* $Header: /cvsroot/tikiwiki/tiki/templates/modules/mod-user_image_galleries.tpl,v 1.10 2007-02-18 11:21:19 mose Exp $ *}

{if $user}
    {if $feature_galleries eq 'y'}
{if !isset($tpl_module_title)}{assign var=tpl_module_title value="{tr}My galleries{/tr}"}{/if}
{tikimodule title=$tpl_module_title name="user_image_galleries" flip=$module_params.flip decorations=$module_params.decorations}
	<table  border="0" cellpadding="0" cellspacing="0">
	{section name=ix loop=$modUserG}
	    <tr>{if $nonums != 'y'}<td class="module" valign="top">{$smarty.section.ix.index_next})</td>{/if}
	    <td class="module"><a class="linkmodule" href="tiki-browse_gallery.php?galleryId={$modUserG[ix].galleryId}">{$modUserG[ix].name}</a></td></tr>
	{/section}
	</table>
	{/tikimodule}
    {/if}
{/if}
