{* $Header: /cvsroot/tikiwiki/tiki/templates/modules/mod-last_file_galleries.tpl,v 1.7 2003-11-23 03:15:07 zaufi Exp $ *}

{if $feature_file_galleries eq 'y'}
{tikimodule title="{tr}Last modified file galleries{/tr}" name="last_file_galleries"}
  <table  border="0" cellpadding="0" cellspacing="0">
    {section name=ix loop=$modLastFileGalleries}
      <tr>
        {if $nonums != 'y'}<td class="module" valign="top">{$smarty.section.ix.index_next})</td>{/if}
        <td class="module">
          <a class="linkmodule" href="tiki-list_file_gallery.php?galleryId={$modLastFileGalleries[ix].galleryId}">
            {$modLastFileGalleries[ix].name}
          </a>
        </td>
      </tr>
    {/section}
  </table>
{/tikimodule}
{/if}
