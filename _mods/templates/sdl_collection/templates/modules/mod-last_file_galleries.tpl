{* $Header: /cvsroot/tikiwiki/_mods/templates/sdl_collection/templates/modules/mod-last_file_galleries.tpl,v 1.1 2004-05-09 23:09:44 damosoft Exp $ *}

{if $feature_file_galleries eq 'y'}
{if $nonums eq 'y'}
{eval var="{tr}Last `$module_rows` Modified File Galleries{/tr}" assign="tpl_module_title"}
{else}
{eval var="{tr}Last Modified File Galleries{/tr}" assign="tpl_module_title"}
{/if}
{tikimodule title=$tpl_module_title name="last_file_galleries"}
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
