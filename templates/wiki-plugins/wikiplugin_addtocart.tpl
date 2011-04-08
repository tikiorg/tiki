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
	
	<table>
		{if $gift_certificate_error}
			<tr>
				<th style="text-align: right;">{$gift_certificate_error}</th>
				<td>{$gift_certificate}</td>
			</tr>
		{/if}
		
		{if $giftcertificate == 'y'}
			<tr>
				<th style="text-align: right;">{tr}Gift Certificate:{/tr}</th>
				<td><input type="text" name="gift_certificate" size="2"/></td>
			</tr>
		{/if}

		{if $hideamountfield eq 'y'}
		<input type="hidden" name="quantity" value="1" />
		{else}			
		<tr>
			<th style="text-align: right;">{tr}Qty:{/tr}</th>
			<td><input type="text" name="quantity" value="1" size="2"/></td>
		</tr>
		{/if}
	</table>
	<input type="submit" value="{$add_label|escape}" class="addProductToCartSubmit" />
	{if $exchangeorderitemid && $exchangetoproductid}
		<input type="hidden" value="{$exchangeorderitemid|escape}" name="exchangeorderitemid" />
		<input type="hidden" value="{$exchangetoproductid|escape}" name="exchangetoproductid" />
	{/if}
</form>

