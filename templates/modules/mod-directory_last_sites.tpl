{* $Header: /cvsroot/tikiwiki/tiki/templates/modules/mod-directory_last_sites.tpl,v 1.6 2003-11-23 03:15:06 zaufi Exp $ *}

{if $feature_directory eq 'y'}
{tikimodule title="{tr}Last Sites{/tr}" name="directory_last_sites"}
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
