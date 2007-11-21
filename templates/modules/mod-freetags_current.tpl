{* based on $Header: /cvsroot/tikiwiki/tiki/templates/modules/mod-freetags_current.tpl,v 1.3.2.1 2007-11-21 01:52:02 nkoth Exp $ *}

{if $prefs.feature_freetags eq 'y' && count($modFreetagsCurrent) gt 0}
  {if !isset($tpl_module_title)}{eval var="{tr}Tags This Page Has{/tr}" assign="tpl_module_title"}{/if}

  {tikimodule title=$tpl_module_title name="freetags_current" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox}
  {section name=ix loop=$modFreetagsCurrent.data}
     <div class="module">
     <a class="linkmodule" href="tiki-browse_freetags.php?tag={$modFreetagsCurrent.data[ix].tag|escape:'url'}">{$modFreetagsCurrent.data[ix].tag}</a>
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
