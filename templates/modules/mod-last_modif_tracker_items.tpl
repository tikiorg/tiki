{* $Header: /cvsroot/tikiwiki/tiki/templates/modules/mod-last_modif_tracker_items.tpl,v 1.6 2003-11-23 03:15:07 zaufi Exp $ *}

{if $feature_trackers eq 'y'}
{tikimodule title="{tr}Last Modified Items{/tr}" name="last_modif_tracker_items"}
  <table  border="0" cellpadding="0" cellspacing="0">
    {section name=ix loop=$modLastModifItems}
      <tr>
        {if $nonums != 'y'}<td class="module" >{$smarty.section.ix.index_next})</td>{/if}
        <td class="module">
          <a class="linkmodule" href="tiki-view_tracker_item.php?itemId={$modLastModifItems[ix].itemId}&amp;trackerId={$modLastModifItems[ix].trackerId}">
          {section name=jjj loop=$modLastModifItems[ix].field_values}
            {if $modlmifn eq $modLastModifItems[ix].field_values[jjj].name}
              {$modLastModifItems[ix].field_values[jjj].value}
            {/if}
          {/section}
          </a>
        </td>
      </tr>
    {/section}
  </table>
{/tikimodule}
{/if}
