{* $Header: /cvsroot/tikiwiki/tiki/templates/modules/mod-last_images_th.tpl,v 1.2 2004-09-08 19:53:07 mose Exp $ *}

{if $feature_galleries eq 'y'}
  {tikimodule title="{tr}Last Images{/tr}" name="last_images_th"}
  {section name=ix loop=$modLastImages}
    {if $smarty.section.ix.index < 5}
       <table  cellpadding="0" cellspacing="0">
       <tr>
       <td align="center">
       <a class="linkmodule" href="tiki-browse_image.php?imageId={$modLastImages[ix].imageId}">
       <img alt="image" src="show_image.php?id={$modLastImages[ix].imageId}&amp;thumb=1" />
       </a>
       </td></tr>
       </table>
    {/if}
  {/section}
  {/tikimodule}
{/if}