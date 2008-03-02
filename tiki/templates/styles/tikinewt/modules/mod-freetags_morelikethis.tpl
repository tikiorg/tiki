{* based on /cvsroot/tikiwiki/tiki/templates/modules/mod-freetags_morelikethis.tpl,v 1.2.2.1 2007/11/21 01:52:02 nkoth *}

{if $prefs.feature_wiki eq 'y' && count($modMoreLikeThis) gt 0}

  {if !isset($tpl_module_title)}{eval var="{tr}More Like This{/tr}" assign="tpl_module_title"}{/if}

  {tikimodule title=$tpl_module_title name="freetags_morelikethis" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox}
  {section name=ix loop=$modMoreLikeThis}
     <div class="module">
	 {if $nonums != 'y'}<span class="module">{$smarty.section.ix.index_next}&nbsp;</span>{/if}
     <a class="linkmodule" href="tiki-index.php?page={$modMoreLikeThis[ix].pageName|escape:'url'}">{$modMoreLikeThis[ix].pageName}</a>
	 </div>
  {/section}
  {/tikimodule}
{/if}
