{* $Header: /cvsroot/tikiwiki/tiki/templates/styles/musus/modules/mod-last_image_galleries.tpl,v 1.2 2004-01-16 18:37:49 musus Exp $ *}

{if $feature_galleries eq 'y'}
{if $nonums eq 'y'}
{eval var="{tr}Last `$module_rows` galleries{/tr}" assign="tpl_module_title"}
{else}
{eval var="{tr}Last galleries{/tr}" assign="tpl_module_title"}
{/if}
{tikimodule title=$tpl_module_title name="last_image_galleries"}
  <table border="0" cellpadding="0" cellspacing="0">
    {section name=ix loop=$modLastGalleries}
      <tr class="module">
        {if $nonums != 'y'}<td valign="top">{$smarty.section.ix.index_next})</td>{/if}
        <td>
          <a class="linkmodule" href="tiki-browse_gallery.php?galleryId={$modLastGalleries[ix].galleryId}">
            {$modLastGalleries[ix].name}
          </a>
        </td>
      </tr>
    {/section}
  </table>
{/tikimodule}
{/if}
