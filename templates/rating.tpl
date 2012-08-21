<div class="rating">
	{foreach from=$rating_options item=v key=i}
		<input type="radio" name="rating_value[{$rating_type|escape}][{$rating_id|escape}]" value="{$v|escape}" id="{$rating_type|escape}{$rating_id|escape}_{$v|escape}" {if $current_rating == $v}checked="checked"{/if} />
		<label for="{$rating_type|escape}{$rating_id|escape}_{$v|escape}">
			{if $prefs.rating_smileys eq 'y'}
				<img src="{$rating_smiles[$i + 1].img|escape}" />
			{else}
				{$v|escape}
			{/if}
		</label>
	{/foreach}
	<input type="hidden" name="rating_prev[{$rating_type|escape}][{$rating_id|escape}]" value="{$current_rating|escape}" />
	<input type="submit" value="{tr}Rate{/tr}"/>

	{if $tiki_p_wiki_admin_ratings eq 'y'}
		{rating_result id=$rating_id type=$rating_type}
	{/if}
</div>
