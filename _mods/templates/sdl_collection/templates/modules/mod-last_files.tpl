{* $Header: /cvsroot/tikiwiki/_mods/templates/sdl_collection/templates/modules/mod-last_files.tpl,v 1.1 2004-05-09 23:09:44 damosoft Exp $ *}

{if $feature_file_galleries eq 'y'}
{if $nonums eq 'y'}
{eval var="{tr}Last `$module_rows` Files{/tr}" assign="tpl_module_title"}
{else}
{eval var="{tr}New Files{/tr}" assign="tpl_module_title"}
{/if}
{tikimodule title=$tpl_module_title name="last_files"}
  <table  border="0" cellpadding="0" cellspacing="0" width="95%">
    {section name=ix loop=$modLastFiles}
      <tr>
        {if $nonums != 'y'}<td class="module">{$smarty.section.ix.index_next})</td>{/if}
        <td class="module">
          <a class="linkmodule" href="tiki-download_file.php?fileId={$modLastFiles[ix].fileId}" title="{$modLastFiles[ix].created|tiki_short_datetime}, by {$modLastFiles[ix].user}">{$modLastFiles[ix].name}</a>
        </td>
      </tr>
    {/section}
  </table>
{/tikimodule}
{/if}
