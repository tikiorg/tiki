{* based on $Id$ *}

{if isset($modMoreLikeThis) && count($modMoreLikeThis) gt 0}
  {tikimodule error=$module_params.error title=$tpl_module_title name="search_morelikethis" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox notitle=$module_params.notitle}
  {if ($nonums eq 'y')}
  	<ul>
      {foreach item=row from=$modMoreLikeThis}
        <li>{object_link type=$row.object_type id=$row.object_id}</li>
      {/foreach}
	</ul>
  {else}
  	<ol>
      {foreach item=row from=$modMoreLikeThis}
        <li>{object_link type=$row.object_type id=$row.object_id}</li>
      {/foreach}
	</ol>
  {/if}
  {/tikimodule}
{/if}
