{* based on $Id$ *}

{if isset($modMoreLikeThis) && count($modMoreLikeThis) gt 0}
  {tikimodule error=$module_params.error title=$tpl_module_title name="freetags_morelikethis" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox notitle=$module_params.notitle}
  {section name=ix loop=$modMoreLikeThis}
     <div class="module">
	 {if $nonums != 'y'}<span class="module">{$smarty.section.ix.index_next}&nbsp;</span>{/if}
     <a class="linkmodule" href="tiki-index.php?page={$modMoreLikeThis[ix].pageName|escape:'url'}">{$modMoreLikeThis[ix].pageName|escape}</a>
	 </div>
  {/section}
  {/tikimodule}
{/if}
