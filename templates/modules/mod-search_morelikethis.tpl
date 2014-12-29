{* based on $Id$ *}

{if isset($modMoreLikeThis) && count($modMoreLikeThis) gt 0}
	{tikimodule error=$module_params.error title=$tpl_module_title name="search_morelikethis" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox notitle=$module_params.notitle}
		{$tag = ($nonums eq 'y') ? 'ul' : 'ol'}
		<{$tag}>
			{foreach item=row from=$modMoreLikeThis}
				{if $row.object_id eq $simobject.object and $row.object_type eq $row.object_type}{else}
					<li>
						{object_link type=$row.object_type id=$row.object_id title=$row.title}
						{if $row._external}
							<span class="label label-info">{tr}External{/tr}</span>
						{/if}
					</li>
				{/if}
			{/foreach}
		</{$tag}>
	{/tikimodule}
{/if}
