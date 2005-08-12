{if $feature_stats eq 'y'}
   {if $nonums eq 'y'}
   {eval var="{tr}Top `$module_rows` Objects{/tr}" assign="tpl_module_title"}
   {else}
   {eval var="{tr}Top Objects{/tr}" assign="tpl_module_title"}
   {/if}

  {tikimodule title=$tpl_module_title name="top_objects" flip=$module_params.flip decorations=$module_params.decorations}
  <table  border="0" cellpadding="0" cellspacing="0">
  {section name=ix loop=$modTopObjects}
     <tr>{if $nonums != 'y'}<td class="module" valign='top'>{$smarty.section.ix.index_next})</td>{/if}
     <td class="module">{$modTopObjects[ix]->object} ({$modTopObjects[ix]->type})</td></tr>
  {/section}
  </table>
  {/tikimodule}
{/if}