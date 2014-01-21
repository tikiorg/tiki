{* based on $Id$ *}

{if isset($modMoreLikeThis) && count($modMoreLikeThis) gt 0}
  {tikimodule error=$module_params.error title=$tpl_module_title name="search_morelikethis" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox notitle=$module_params.notitle}
  {if ($nonums eq 'y')}
  	<ul>
      {foreach item=row from=$modMoreLikeThis}
        {if $row.object_id eq $simobject.object and $row.object_type eq $row.object_type}{else}
          <li>{object_link type=$row.object_type id=$row.object_id}</li>
        {/if}
      {/foreach}
	</ul>
  {else}
  	<ol>
      {foreach item=row from=$modMoreLikeThis}
        {if $row.object_id eq $simobject.object and $row.object_type eq $row.object_type}{else}
          <li>{object_link type=$row.object_type id=$row.object_id}</li>
        {/if}
      {/foreach}
	</ol>
  {/if}
  {/tikimodule}
{/if}
