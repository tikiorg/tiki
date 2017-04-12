{extends 'layout_view.tpl'}

{block name="title"}
	{title}{$title|escape}{/title}
{/block}

{block name="content"}
<form method="post" action="{service controller=tracker action=edit_field}">
	{accordion}
		{accordion_group title="{tr}General{/tr}"}
		<div class="form-group">
			<label for="name" class="control-label">{tr}Name{/tr}</label>
			<input type="text" name="name" value="{$field.name|escape}" required="required" class="form-control">
		</div>
		<div class="form-group">
			<label name="description" class="control-label">{tr}Description{/tr}</label>
			<textarea name="description" class="form-control">{$field.description|escape}</textarea>
		</div>
		<div class="checkbox">
			<label>
				<input type="checkbox" name="description_parse" value="1"
					{if $field.descriptionIsParsed eq 'y'}checked="checked"{/if}
					>
				{tr}Description contains wiki syntax{/tr}
			</label>
		</div>
		{/accordion_group}
		{accordion_group title="{tr _0=$info.name}Options for %0{/tr}"}
			<p>{$info.description|escape}</p>

			{if $field.type eq 't' or $field.type eq 'a'}
				{* Pretend the field attribute is just an option as it only exists for two field types *}
				<div class="checkbox">
					<label>
						<input type="checkbox" name="multilingual" value="1"
							{if $field.isMultilingual eq 'y'}checked="checked"{/if}>
						{tr}Multilingual{/tr}
					</label>
				</div>
			{/if}

			{foreach from=$info.params key=param item=def}
				<div class="form-group">
					<label for="option~{$param|escape}" class="control-label">{$def.name|escape}</label>
					{if $def.options}
						<select name="option~{$param|escape}" class="form-control">
							{foreach from=$def.options key=val item=label}
								<option value="{$val|escape}"
									{if $options[$param] eq $val} selected="selected"{/if}>
									{$label|escape}
								</option>
							{/foreach}
						</select>
					{elseif $def.selector_type}
						{if $def.separator}
							{object_selector_multi type=$def.selector_type _separator=$def.separator _simplename="option~`$param`" _simplevalue=$options[$param] _simpleid="option-`$param`" _parent=$def.parent _parentkey=$def.parentkey _sort=$def.sort_order}
						{else}
							{object_selector type=$def.selector_type _simplename="option~`$param`" _simplevalue=$options[$param] _simpleid="option-`$param`" _parent=$def.parent _parentkey=$def.parentkey}
						{/if}
					{elseif $def.separator}
						<input type="text" name="option~{$param|escape}" value="{$options[$param]|implode:$def.separator|escape}" class="form-control">
					{elseif $def.count eq '*'}
						<input type="text" name="option~{$param|escape}" value="{$options[$param]|implode:','|escape}" class="form-control">
					{elseif $def.type eq 'textarea'}
						<textarea name="option~{$param|escape}" class="form-control">{$options[$param]|escape}</textarea>
					{else}
						<input type="text" name="option~{$param|escape}" value="{$options[$param]|escape}" class="form-control">
					{/if}
					<div class="help-block">{$def.description|escape}</div>
					{if ! $def.selector_type}
						{if $def.count eq '*'}
							<div class="help-block">{tr}Separate multiple with commas.{/tr}</div>
						{elseif $def.separator}
							<div class="help-block">{tr}Separate multiple with &quot;{$def.separator}&quot;{/tr}</div>
						{/if}
					{/if}
				</div>
			{/foreach}

		{/accordion_group}

		{accordion_group title="{tr}Validation{/tr}"}
			<div class="form-group">
				<label for="validation_type" class="control-label">{tr}Type{/tr}</label>
				<select name="validation_type" class="form-control">
					{foreach from=$validation_types key=type item=label}
						<option value="{$type|escape}"
							{if $type eq $field.validation} selected="selected"{/if}>
							{$label|escape}
						</option>
					{/foreach}
				</select>
			</div>

			<div class="form-group">
				<label for="validation_parameter" class="control-label">{tr}Parameters{/tr}</label>
				<input type="text" name="validation_parameter" value="{$field.validationParam|escape}" class="form-control">
			</div>

			<div class="form-group">
				<label for="validation_message" class="control-label">{tr}Error Message{/tr}</label>
				<input type="text" name="validation_message" value="{$field.validationMessage|escape}" class="form-control">
			</div>
		{/accordion_group}

		{accordion_group title="{tr}Permissions{/tr}"}
			<div class="form-group">
				<label for="visibility" class="control-label">{tr}Visibility{/tr}</label>
				<select name="visibility" class="form-control">
					<option value="n"{if $field.isHidden eq 'n'} selected="selected"{/if}>{tr}Visible by all{/tr}</option>
					<option value="r"{if $field.isHidden eq 'r'} selected="selected"{/if}>{tr}Visible by all but not in RSS feeds{/tr}</option>
					<option value="y"{if $field.isHidden eq 'y'} selected="selected"{/if}>{tr}Visible after creation by administrators only{/tr}</option>
					<option value="p"{if $field.isHidden eq 'p'} selected="selected"{/if}>{tr}Editable by administrators only{/tr}</option>
					<option value="c"{if $field.isHidden eq 'c'} selected="selected"{/if}>{tr}Editable by administrators and creator only{/tr}</option>
					<option value="i"{if $field.isHidden eq 'i'} selected="selected"{/if}>{tr}Immutable after creation{/tr}</option>
				</select>
				<div class="help-block">
					{tr}Creator requires a user field with auto-assign to creator (1){/tr}
				</div>
			</div>

			<div class="form-group">
				<label for="visible_by" class="groupselector control-label">{tr}Visible by{/tr}</label>
				<input type="text" name="visible_by" value="{foreach from=$field.visibleBy item=group}{$group|escape}, {/foreach}" class="form-control">
				<div class="help-block">
					{tr}List of Group names with permission to see this field{/tr}. {tr}Separated by comma (,){/tr}
				</div>
			</div>

			<div class="form-group">
				<label for="editable_by" class="groupselector control-label">{tr}Editable by{/tr}</label>
				<input type="text" name="editable_by" value="{foreach from=$field.editableBy item=group}{$group|escape}, {/foreach}" class="form-control">
				<div class="help-block">
					{tr}List of Group names with permission to edit this field{/tr}. {tr}Separated by comma (,){/tr}
				</div>
			</div>

			<div class="form-group">
				<label for="error_message" class="control-label">{tr}Error Message{/tr}</label>
				<input type="text" name="error_message" value="{$field.errorMsg|escape}" class="form-control">
			</div>
		{/accordion_group}

		{accordion_group title="{tr}Advanced{/tr}"}
			<div class="form-group">
				<label for="permName" class="control-label">{tr}Permanent name{/tr}</label>
				<input type="text" name="permName" value="{$field.permName|escape}" pattern="[a-zA-Z0-9_]+" class="form-control">
				<div class="help-block">
					{tr}Changing the permanent name may have consequences in integrated systems.{/tr}
				</div>
			</div>
			{if $prefs.tracker_change_field_type eq 'y'}
				<div class="form-group">
					<label for="type" class="control-label">{tr}Field Type{/tr}</label>
					<select name="type" data-original="{$field.type}" class="confirm-prompt form-control">
						{foreach from=$types key=k item=info}
							<option value="{$k|escape}"
								{if $field.type eq $k}selected="selected"{/if}>
								{$info.name|escape}
								{if $info.deprecated}- Deprecated{/if}
							</option>
						{/foreach}
					</select>
					{foreach from=$types item=info key=k}
						<div class="help-block field {$k|escape}">
							{$info.description|escape}
							{if $info.help}
								<a href="{$prefs.helpurl|escape}{$info.help|escape:'url'}" target="tikihelp" class="tikihelp" title="{$info.name|escape}">
									{icon name='help'}
								</a>
							{/if}
						</div>
					{/foreach}
{jq}
$('select[name=type]').change(function () {
	var descriptions = $(this).closest('.form-group').
			find('.help-block.field').
			hide();

	if ($(this).val()) {
		descriptions
			.filter('.' + $(this).val())
			.show();
	}
}).change();
{/jq}
					<div class="alert alert-danger">
						{icon name="warning"} {tr}Changing the field type may cause irretrievable data loss - use with caution!{/tr}
					</div>
				</div>
			{/if}
		{/accordion_group}
	{/accordion}

	<div class="submit">
		<input type="submit" class="btn btn-primary" name="submit" value="{tr}Save{/tr}">
		<input type="hidden" name="trackerId" value="{$field.trackerId|escape}">
		<input type="hidden" name="fieldId" value="{$field.fieldId|escape}">
	</div>
</form>
{/block}
