{* $Header: /cvsroot/tikiwiki/tiki/templates/modules/mod-last_files.tpl,v 1.7 2003-11-23 03:15:07 zaufi Exp $ *}

{if $feature_file_galleries eq 'y'}
{tiki module title="{tr}Last Files{/tr}" name="last_files"}
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
