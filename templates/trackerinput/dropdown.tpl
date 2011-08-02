{strip}
{if $field.type eq 'radio' or $field.type eq 'R'}
	{foreach from=$field.possibilities key=value item=label}
		<label>
			<input type="radio" name="{$field.ins_id|escape}" value="{$value|escape}" {if $field.value eq $value or (empty($item.itemId) and $field.defaultvalue eq $value)}checked="checked"{/if} />
			{$label|tr_if|escape}
		</label>
	{/foreach}
{else}
	<select name="{$field.ins_id|escape}" {if $field.http_request}onchange="selectValues('trackerIdList={$field.http_request[0]}&amp;fieldlist={$field.http_request[3]}&amp;filterfield={$field.http_request[1]}&amp;status={$field.http_request[4]}&amp;mandatory={$field.http_request[6]}&amp;filtervalue='+escape(this.value),'{$field.http_request[5]}')"{/if}>
{assign var=otherValue value=$field.value}
		{if $field.isMandatory ne 'y' || empty($field.value)}
			<option value="">&nbsp;</option>
		{/if}
		{foreach from=$field.possibilities key=value item=label}
			<option value="{$value|escape}" 
			{if !empty($item.itemId) && ($field.value eq $value or (isset($field.isset) && $field.isset == 'n' && $field.defaultvalue eq $value))}{assign var=otherValue value=''}selected="selected"{elseif (empty($item.itemId) || !isset($field.value)) && $field.defaultvalue eq $value}selected="selected"{/if}>
				{$label|tr_if|escape}
			</option>
		{/foreach}
	</select>

	{if $field.type eq 'D'}
		<br />
		<label>
			{tr}Other:{/tr}
			<input type="text" name="other_{$field.ins_id}" value="{$otherValue|escape}" />
		</label>
	{/if}

{/if}
{/strip}
