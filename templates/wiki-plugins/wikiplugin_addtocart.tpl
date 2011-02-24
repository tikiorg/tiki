<form method="post" action="{query _type=relative _keepall=y}" style="display: inline;">
	<input type="hidden" name="code" value="{$code|escape}"/>
	{if $onbehalf == 'y'}
		{tr}Buy on behalf of{/tr}:
		<select name="buyonbehalf">
			<option value="">{tr}None{/tr}</option>
			{foreach key=id item=one from=$cartuserlist}
				<option value="{$one|escape}">{$one|escape}</option>
			{/foreach}
		</select>
		<br />
	{/if}
	<input type="text" name="quantity" value="1" size="2"/>
	<input type="submit" value="{$add_label|escape}"/>
</form>

