{* $Header: /cvsroot/tikiwiki/tiki/templates/modules/mod-directory_top_sites.tpl,v 1.6 2003-11-23 03:15:07 zaufi Exp $ *}

{if $feature_directory eq 'y'}
{tikimodule title="{tr}Top Sites{/tr}" name="directory_top_sites"}
  <table  border="0" cellpadding="0" cellspacing="0">
    {section name=ix loop=$modTopdirSites}
      <tr>
        {if $nonums != 'y'}<td valign="top" class="module">{$smarty.section.ix.index_next})</td>{/if}
        <td class="module">
          <a class="linkmodule" href="tiki-directory_redirect.php?siteId={$modTopdirSites[ix].siteId}" {if $directory_open_links eq 'n'}target="_new"{/if}>
            {$modTopdirSites[ix].name}
          </a>
        </td>
      </tr>
    {/section}
  </table>
{/tikimodule}
{/if}
