{* $Header: /cvsroot/tikiwiki/tiki/templates/styles/musus/modules/mod-directory_top_sites.tpl,v 1.3 2004-01-16 18:00:49 musus Exp $ *}

{if $feature_directory eq 'y'}
{if $nonums eq 'y'}
{eval var="{tr}Top $module_rows Sites{/tr}" assign="tpl_module_title"}
{else}
{eval var="{tr}Top Sites{/tr}" assign="tpl_module_title"}
{/if}
{tikimodule title=$tpl_module_title name="directory_top_sites"}
  <table>
    {section name=ix loop=$modTopdirSites}
      <tr class="module">
        {if $nonums != 'y'}<td valign="top">{$smarty.section.ix.index_next})</td>{/if}
        <td>
          <a class="linkmodule" href="tiki-directory_redirect.php?siteId={$modTopdirSites[ix].siteId}" {if $directory_open_links eq 'n'}target="_new"{/if}>
            {$modTopdirSites[ix].name}
          </a>
        </td>
      </tr>
    {/section}
  </table>
{/tikimodule}
{/if}
