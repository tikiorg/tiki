{* $Header: /cvsroot/tikiwiki/tiki/templates/styles/musus/modules/mod-last_file_galleries.tpl,v 1.2 2004-01-16 18:37:49 musus Exp $ *}

{if $feature_file_galleries eq 'y'}
{if $nonums eq 'y'}
{eval var="{tr}Last `$module_rows` modified file galleries{/tr}" assign="tpl_module_title"}
{else}
{eval var="{tr}Last modified file galleries{/tr}" assign="tpl_module_title"}
{/if}
{tikimodule title=$tpl_module_title name="last_file_galleries"}
  <table border="0" cellpadding="0" cellspacing="0">
    {section name=ix loop=$modLastFileGalleries}
      <tr class="module">
        {if $nonums != 'y'}<td valign="top">{$smarty.section.ix.index_next})</td>{/if}
        <td>
          <a class="linkmodule" href="tiki-list_file_gallery.php?galleryId={$modLastFileGalleries[ix].galleryId}">
            {$modLastFileGalleries[ix].name}
          </a>
        </td>
      </tr>
    {/section}
  </table>
{/tikimodule}
{/if}
