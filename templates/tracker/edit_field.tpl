<form class="simple" method="post" action="tiki-ajax_services.php">
	<h4>{tr}General{/tr}</h4>
	<label>
		{tr}Name:{/tr}
		<input type="text" name="name" value="{$field.name|escape}"/>
	</label>
	<label>
		{tr}Description:{/tr}
		<textarea name="description">{$field.description|escape}</textarea>
	</label>
	<label>
		<input type="checkbox" name="description_parse" value="1"
			{if $field.descriptionIsParsed eq 'y'}checked="checked"{/if}
			/>
		{tr}Description contains wiki syntax{/tr}
	</label>

	<h4>{tr 0=$info.name}Options for %0{/tr}</h4>
	
	<p>{$info.description|escape}</p>

	{foreach from=$info.params key=param item=def}
		<label>
			{$def.name|escape}
			{if $def.options}
				<select name="option~{$param|escape}">
					{foreach from=$def.options key=val item=label}
						<option value="{$val|escape}"
							{if $options[$param] eq $val} selected="selected"{/if}>
							{$label|escape}
						</option>
					{/foreach}
				</select>
			{else}
				<input type="text" name="option~{$param|escape}" value="{$options[$param]|escape}"/>
			{/if}
			<div class="description">{$def.description|escape}</div>
			{if $def.count eq '*'}
				<div class="description">{tr}Separate multiple with commas.{/tr}</div>
			{/if}
		</label>
	{/foreach}

	<h4>{tr}Validation{/tr}</h4>

	<label>
		{tr}Type{/tr}
		<select name="validation_type">
			{foreach from=$validation_types key=type item=label}
				<option value="{$type|escape}"
					{if $type eq $field.validation} selected="selected"{/if}>
					{$label|escape}
				</option>
			{/foreach}
		</select>
	</label>

	<label>
		{tr}Parameters{/tr}
		<input type="text" name="validation_parameter" value="{$field.validationParam|escape}"/>
	</label>

	<label>
		{tr}Error Message{/tr}
		<input type="text" name="validation_message" value="{$field.validationMessage|escape}"/>
	</label>

	<div>
		<input type="submit" name="submit" value="{tr}Save{/tr}"/>
		<input type="hidden" name="controller" value="tracker"/>
		<input type="hidden" name="action" value="edit_field"/>
		<input type="hidden" name="trackerId" value="{$field.trackerId|escape}"/>
		<input type="hidden" name="fieldId" value="{$field.fieldId|escape}"/>
	</div>
</form>
