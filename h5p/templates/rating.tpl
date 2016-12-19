<div class="rating">
	{if $prefs.rating_smileys eq 'y'}
		{foreach from=$rating_options item=v key=i}
			<input type="radio" name="rating_value[{$rating_type|escape}][{$rating_id|escape}]" value="{$v|escape}" id="{$rating_type|escape}{$rating_id|escape}_{$v|escape}"{if $current_rating eq $v} checked="checked"{/if}>
			<label for="{$rating_type|escape}{$rating_id|escape}_{$v|escape}">
					<img src="{$rating_smiles[$i].img|escape}" title="{tr}User rating:{/tr} {$v|escape}">
			</label>
		{/foreach}
	{elseif $rating_has_labels}
		{if $prefs.rating_options_reversed eq 'y'}
			{foreach from=$rating_options|array_reverse item=v key=i}
				<input type="radio" name="rating_value[{$rating_type|escape}][{$rating_id|escape}]" value="{$i|escape}" id="{$rating_type|escape}{$rating_id|escape}_{$v|escape}" {if $current_rating == $v}checked="checked"{/if}>
				<label for="{$rating_type|escape}{$rating_id|escape}_{$v|escape}">{$v|escape}</label>
			{/foreach}
		{else}
			{foreach from=$rating_options item=v key=i}
				<input type="radio" name="rating_value[{$rating_type|escape}][{$rating_id|escape}]" value="{$i|escape}" id="{$rating_type|escape}{$rating_id|escape}_{$v|escape}" {if $current_rating == $v}checked="checked"{/if}>
				<label for="{$rating_type|escape}{$rating_id|escape}_{$v|escape}">{$v|escape}</label>
			{/foreach}
		{/if}
	{else}
		{foreach from=$rating_options item=v key=i}
			<input type="radio" name="rating_value[{$rating_type|escape}][{$rating_id|escape}]" value="{$v|escape}" id="{$rating_type|escape}{$rating_id|escape}_{$v|escape}"{if $current_rating eq $v} checked="checked"{/if}>
			<label for="{$rating_type|escape}{$rating_id|escape}_{$v|escape}">{$v|escape}</label>
		{/foreach}
	{/if}
	<input type="hidden" name="rating_prev[{$rating_type|escape}][{$rating_id|escape}]" value="{$current_rating|escape}">
	<input type="submit" class="btn btn-default btn-sm" value="{tr}Rate{/tr}">
</div>
