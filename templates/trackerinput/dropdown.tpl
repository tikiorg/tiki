{strip}
{if $field.type eq 'R'}
	{foreach from=$field.possibilities key=value item=label}
		<label>
			<input type="radio" name="{$field.ins_id|escape}" value="{$value|escape}" {if $field.value eq $value}checked="checked"{/if} />
			{$label|tr_if|escape}
		</label>
	{/foreach}
{elseif $field.type eq 'M'}
	{foreach from=$field.possibilities key=value item=label}
		<label>
			<input type="checkbox" name="{$field.ins_id|escape}[]" value="{$value|escape}" {if in_array($value, $field.selected)}checked="checked"{/if}/>
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
			{if $field.value eq $value}selected="selected"{/if}>
				{$label|tr_if|escape}
			</option>
		{/foreach}
	</select>

	{if $field.type eq 'D'}
		<br />
		<label>
			{tr}Other:{/tr}
			<input type="text" name="other_{$field.ins_id}" value="{if !isset($field.possibilities[$value])}{$value|escape}{/if}" />
		</label>
	{/if}

{/if}
{/strip}
