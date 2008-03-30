{* $Id$ *}

{if $prefs.feature_trackers eq 'y'}
{if !isset($tpl_module_title)}
{if $nonums eq 'y'}
{eval var="{tr}Last `$module_rows` Modified Items{/tr}" assign="tpl_module_title"}
{else}
{eval var="{tr}Last Modified Items{/tr}" assign="tpl_module_title"}
{/if}
{/if}
{tikimodule title=$tpl_module_title name="last_modif_tracker_items" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox}
  <table  border="0" cellpadding="0" cellspacing="0">
    {section name=ix loop=$modLastModifItems}
      <tr>
        {if $nonums != 'y'}<td class="module" >{$smarty.section.ix.index_next})</td>{/if}
        <td class="module">
          <a class="linkmodule" href="tiki-view_tracker_item.php?itemId={$modLastModifItems[ix].itemId}&amp;trackerId={$modLastModifItems[ix].trackerId}">
              {$modLastModifItems[ix].subject}
          </a>
        </td>
      </tr>
    {/section}
  </table>
{/tikimodule}
{/if}
