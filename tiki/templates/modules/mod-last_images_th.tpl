{* $Header: /cvsroot/tikiwiki/tiki/templates/modules/mod-last_images_th.tpl,v 1.5 2006-06-27 21:46:48 amette Exp $ *}

{if $feature_galleries eq 'y'}
  {tikimodule title="{tr}Last Images{/tr}" name="last_images_th" flip=$module_params.flip decorations=$module_params.decorations}
  {section name=ix loop=$modLastImages}
    {if $smarty.section.ix.index < $module_rows}
       <table  cellpadding="0" cellspacing="0">
       <tr>
       <td align="center">
       <a class="linkmodule" href="tiki-browse_image.php?imageId={$modLastImages[ix].imageId}">
       {* doing regex to prevent xss *}
       <img src="show_image.php?id={$modLastImages[ix].imageId}&amp;thumb=1" title="{$modLastImages[ix].name|regex_replace:"/\"/":"'"}" alt="{$modLastImages[ix].description|regex_replace:"/\"/":"'"}" />
       </a>
       </td></tr>
       </table>
    {/if}
  {/section}
  {/tikimodule}
{/if}
