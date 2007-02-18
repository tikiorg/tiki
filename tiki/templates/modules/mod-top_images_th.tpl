{* $Header: /cvsroot/tikiwiki/tiki/templates/modules/mod-top_images_th.tpl,v 1.14 2007-02-18 11:21:19 mose Exp $ *}

{if $feature_galleries eq 'y'}
{if !isset($tpl_module_title)}{assign var=tpl_module_title value="{tr}Top Images{/tr}"}{/if}
{tikimodule title=$tpl_module_title name="top_images_th" flip=$module_params.flip decorations=$module_params.decorations}
  {section name=ix loop=$modTopImages}
    {if $smarty.section.ix.index < 5}
       <table  cellpadding="0" cellspacing="0">
       <tr>
       <td align="center">
       <a class="linkmodule" href="tiki-browse_image.php?imageId={$modTopImages[ix].imageId}">
       <img alt="image" src="show_image.php?id={$modTopImages[ix].imageId}&amp;thumb=1" />
       </a>
       </td></tr>
       </table>
    {/if}
  {/section}
  {/tikimodule}
{/if}
