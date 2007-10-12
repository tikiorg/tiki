{* based on $Header: /cvsroot/tikiwiki/tiki/templates/modules/mod-freetags_current.tpl,v 1.1 2007-10-12 19:40:53 nkoth Exp $ *}

{if $prefs.feature_freetags eq 'y' && count($modFreetagsCurrent) gt 0}
  {eval var="{tr}Tags This Page Has{/tr}" assign="tpl_module_title"}

  {tikimodule title=$tpl_module_title name="freetags_current" flip=$module_params.flip decorations=$module_params.decorations}
  {section name=ix loop=$modFreetagsCurrent.data}
     <div class="module">
     <a class="linkmodule" href="tiki-browse_freetags.php?tag={$modFreetagsCurrent.data[ix].tag|escape:'url'}">{$modFreetagsCurrent.data[ix].tag}</a>
	 </div>
  {/section}
  {if $tiki_p_freetags_tag eq 'y'}
  <form method="post" action="">
  <div>
  <input type="text" name="tags" value=""/>
  <input type="submit" name="mod_add_tags" value="{tr}Add tags{/tr}"/>
  </div>
  </form>
  {/if}
  {/tikimodule}
{/if}
