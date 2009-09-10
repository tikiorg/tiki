{* based on $Id$ *}

{if $prefs.feature_freetags eq 'y' && count($modFreetagsCurrent) gt 0}
  {if !isset($tpl_module_title)}{eval var="{tr}Tags This Page Has{/tr}" assign="tpl_module_title"}{/if}

  {tikimodule error=$module_params.error title=$tpl_module_title name="freetags_current" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox notitle=$module_params.notitle}
  {section name=ix loop=$modFreetagsCurrent.data}
     <div class="module">
	 {capture name=tagurl}{if (strstr($modFreetagsCurrent.data[ix].tag, ' '))}"{$modFreetagsCurrent.data[ix].tag}"{else}{$modFreetagsCurrent.data[ix].tag}{/if}{/capture}
     <a class="linkmodule" href="tiki-browse_freetags.php?tag={$smarty.capture.tagurl|escape:'url'}">{$modFreetagsCurrent.data[ix].tag|escape}</a>
	 </div>
  {/section}
  {if $tiki_p_freetags_tag eq 'y' && $tiki_p_edit eq 'y'}
  <form method="post" action="">
  <div>
  <input type="text" name="tags" value=""/>
  <input type="submit" name="mod_add_tags" value="{tr}Add tags{/tr}"/>
  </div>
  </form>
  {/if}
  {/tikimodule}
{/if}
