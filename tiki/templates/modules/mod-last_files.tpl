{* $Header: /cvsroot/tikiwiki/tiki/templates/modules/mod-last_files.tpl,v 1.8 2003-11-24 00:20:16 zaufi Exp $ *}

{if $feature_file_galleries eq 'y'}
{tikimodule title="{tr}Last Files{/tr}" name="last_files"}
  <table  border="0" cellpadding="0" cellspacing="0">
    {section name=ix loop=$modLastFiles}
      <tr>
        {if $nonums != 'y'}<td class="module">{$smarty.section.ix.index_next})</td>{/if}
        <td class="module">
          <a class="linkmodule" href="tiki-download_file.php?fileId={$modLastFiles[ix].fileId}">
            {$modLastFiles[ix].filename}
          </a>
        </td>
      </tr>
    {/section}
  </table>
{/tikimodule}
{/if}
