{* $Header: /cvsroot/tikiwiki/tiki/templates/styles/musus/modules/mod-directory_last_sites.tpl,v 1.3 2004-01-16 18:00:23 musus Exp $ *}

{if $feature_directory eq 'y'}
{if $nonums eq 'y'}
{eval var="{tr}Last `$module_rows` Sites{/tr}" assign="tpl_module_title"}
{else}
{eval var="{tr}Last Sites{/tr}" assign="tpl_module_title"}
{/if}
{tikimodule title=$tpl_module_title name="directory_last_sites"}
  <table>
  {section name=ix loop=$modLastdirSites}
    <tr class="module">
      {if $nonums != 'y'}<td valign="top">{$smarty.section.ix.index_next})</td>{/if}
      <td>
        <a class="linkmodule" href="tiki-directory_redirect.php?siteId={$modLastdirSites[ix].siteId}" {if $directory_open_links eq 'n'}target="_new"{/if}>
          {$modLastdirSites[ix].name}
        </a>
      </td>
    </tr>
  {/section}
  </table>
{/tikimodule}
{/if}
