{* $Header: /cvsroot/tikiwiki/tiki/templates/styles/musus/modules/mod-top_images_th.tpl,v 1.1 2004-01-07 04:31:24 musus Exp $ *}

{if $feature_galleries eq 'y'}
  {tikimodule title="{tr}Top Images{/tr}" name="top_images_th"}
  {section name=ix loop=$modTopImages}
    {if $smarty.section.ix.index < 5}
       <table  cellpadding="0" cellspacing="0">
       <tr>
       <td align="center">
       <a class="linkmodule" href="tiki-browse_image.php?imageId={$modTopImages[ix].imageId}">
       <img alt="image" src="show_image.php?id={$modTopImages[ix].imageId}" height="50" width="90" />
       </a>
       </td></tr>
       </table>
    {/if}
  {/section}
  {/tikimodule}
{/if}
