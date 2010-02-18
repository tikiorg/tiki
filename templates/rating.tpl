<p>
	{foreach from=$rating_options item=v}
		<input type="radio" name="rating_value[{$rating_type|escape}][{$rating_id|escape}]" value="{$v|escape}" id="{$rating_type|escape}{$rating_id|escape}_{$v|escape}" {if $current_rating == $v}checked="checked"{/if} />
		<label for="{$rating_type|escape}{$rating_id|escape}_{$v|escape}">{$v|escape}</label>
	{/foreach}
	<input type="submit" value="{tr}Rate{/tr}"/>
</p>
