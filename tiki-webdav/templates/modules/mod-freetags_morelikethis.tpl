{* based on $Id$ *}

{if isset($modMoreLikeThis) && count($modMoreLikeThis) gt 0}
  {tikimodule error=$module_params.error title=$tpl_module_title name="freetags_morelikethis" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox notitle=$module_params.notitle}
  {foreach item=row from=$modMoreLikeThis}
     <div class="module">
	 {if $nonums != 'y'}<span class="module">{$smarty.section.ix.index_next}&nbsp;</span>{/if}
     <a class="linkmodule" href="{$row.href|escape}">{$row.name|escape}</a>
	 </div>
  {/foreach}
  {/tikimodule}
{/if}
