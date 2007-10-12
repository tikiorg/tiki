{* based on $Header: /cvsroot/tikiwiki/tiki/templates/modules/mod-freetags_morelikethis.tpl,v 1.1 2007-10-12 20:02:19 nkoth Exp $ *}

{if $prefs.feature_wiki eq 'y' && count($modMoreLikeThis) gt 0}

  {eval var="{tr}More Like This{/tr}" assign="tpl_module_title"}

  {tikimodule title=$tpl_module_title name="freetags_morelikethis" flip=$module_params.flip decorations=$module_params.decorations}
  {section name=ix loop=$modMoreLikeThis}
     <div class="module">
	 {if $nonums != 'y'}<span class="module">{$smarty.section.ix.index_next}&nbsp;</span>{/if}
     <a class="linkmodule" href="tiki-index.php?page={$modMoreLikeThis[ix].pageName|escape:'url'}">{$modMoreLikeThis[ix].pageName}</a>
	 </div>
  {/section}
  {/tikimodule}
{/if}
