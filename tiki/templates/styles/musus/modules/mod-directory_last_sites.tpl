{* $Header: /cvsroot/tikiwiki/tiki/templates/styles/musus/modules/mod-directory_last_sites.tpl,v 1.1 2004-01-07 04:31:24 musus Exp $ *}

{if $feature_directory eq 'y'}
{if $nonums eq 'y'}
{eval var="{tr}Last `$module_rows` Sites{/tr}" assign="tpl_module_title"}
{else}
{eval var="{tr}Last Sites{/tr}" assign="tpl_module_title"}
{/if}
{tikimodule title=$tpl_module_title name="directory_last_sites"}
  <table  border="0" cellpadding="0" cellspacing="0">
  {section name=ix loop=$modLastdirSites}
    <tr>
      {if $nonums != 'y'}<td valign="top" class="module">{$smarty.section.ix.index_next})</td>{/if}
      <td class="module">
        <a class="linkmodule" href="tiki-directory_redirect.php?siteId={$modLastdirSites[ix].siteId}" {if $directory_open_links eq 'n'}target="_new"{/if}>
          {$modLastdirSites[ix].name}
        </a>
      </td>
    </tr>
  {/section}
  </table>
{/tikimodule}
{/if}
