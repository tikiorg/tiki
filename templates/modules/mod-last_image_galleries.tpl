{* $Header: /cvsroot/tikiwiki/tiki/templates/modules/mod-last_image_galleries.tpl,v 1.7 2003-11-23 03:15:07 zaufi Exp $ *}

{if $feature_galleries eq 'y'}
{tikimodule title="{tr}Last galleries{/tr}" name="last_image_galleries"}
  <table  border="0" cellpadding="0" cellspacing="0">
    {section name=ix loop=$modLastGalleries}
      <tr>
        {if $nonums != 'y'}<td class="module" valign="top">{$smarty.section.ix.index_next})</td>{/if}
        <td class="module">
          <a class="linkmodule" href="tiki-browse_gallery.php?galleryId={$modLastGalleries[ix].galleryId}">
            {$modLastGalleries[ix].name}
          </a>
        </td>
      </tr>
    {/section}
  </table>
{/tikimodule}
{/if}
