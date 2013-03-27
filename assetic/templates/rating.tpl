<div class="rating">
	{if $prefs.rating_smileys eq 'y'}
		{foreach from=$rating_options item=v key=i}
			<input type="radio" name="rating_value[{$rating_type|escape}][{$rating_id|escape}]" value="{$v|escape}" id="{$rating_type|escape}{$rating_id|escape}_{$v|escape}" {if $current_rating == $v}checked="checked"{/if}>
			<label for="{$rating_type|escape}{$rating_id|escape}_{$v|escape}">
					<img src="{$rating_smiles[$i].img|escape}">
			</label>
		{/foreach}
	{else}
		{foreach from=$rating_options item=v key=i}
			<input type="radio" name="rating_value[{$rating_type|escape}][{$rating_id|escape}]" value="{$v|escape}" id="{$rating_type|escape}{$rating_id|escape}_{$v|escape}" {if $current_rating == $v}checked="checked"{/if}>
			<label for="{$rating_type|escape}{$rating_id|escape}_{$v|escape}">{$v|escape}</label>
		{/foreach}
	{/if}
	<input type="hidden" name="rating_prev[{$rating_type|escape}][{$rating_id|escape}]" value="{$current_rating|escape}">
	<input type="submit" value="{tr}Rate{/tr}">

	{if $tiki_p_wiki_admin_ratings eq 'y' or $tiki_p_admin eq 'y'}
		{rating_result id=$rating_id type=$rating_type}
	{/if}
</div>
