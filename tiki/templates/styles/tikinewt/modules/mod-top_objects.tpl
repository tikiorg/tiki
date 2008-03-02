{if $prefs.feature_stats eq 'y'}
{if !isset($tpl_module_title)}
   {if $nonums eq 'y'}
   {eval var="{tr}Top `$module_rows` Objects{/tr}" assign="tpl_module_title"}
   {else}
   {eval var="{tr}Top Objects{/tr}" assign="tpl_module_title"}
   {/if}
{/if}

  {tikimodule title=$tpl_module_title name="top_objects" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox}
  {if $nonums != 'y'}<ol>{else}<ul>{/if}
  {section name=ix loop=$modTopObjects}
     <li>
	 
     {$modTopObjects[ix]->object} ({$modTopObjects[ix]->type})
	 </li>
  {/section}
  {if $nonums != 'y'}</ol>{else}</ul>{/if}
  {/tikimodule}
{/if}