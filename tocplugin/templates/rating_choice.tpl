{* $Id$ *}
<span class="ratingChoice">
	{if $prefs.rating_smileys eq 'y'}
		{foreach from=$rating_options item=v key=i}
			{if $current_rating == $v}
					<label for="{$rating_type|escape}{$rating_id|escape}_{$v|escape}">
							<img src="{$rating_smiles[$i].img|escape}" title="{tr}User rating on thread topic:{/tr} {$v|escape}">
					</label>
			{/if}
		{/foreach}
	{else}
		{foreach from=$rating_options item=v key=i}
			{if $current_rating == $v}
					<label for="{$rating_type|escape}{$rating_id|escape}_{$v|escape}"><a title="{tr}User rating on thread topic{/tr}">({$v|escape})</a></label>
			{/if}
		{/foreach}
	{/if}
	<input type="hidden" name="rating_prev[{$rating_type|escape}][{$rating_id|escape}]" value="{$current_rating|escape}">
</span>
