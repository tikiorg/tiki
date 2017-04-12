{if !empty($field.options_array[2]) && ($field.options_array[2] eq '1' or $field.options_array[2] eq 'y')}
	{select_all checkbox_names=$field.ins_id|cat:"[]" label="{tr}Select All{/tr}"}
{/if}
{if $field.options_array[1] eq 'd' || $field.options_array[1] eq 'm'}
	{if $field.options_array[1] eq 'm' and $prefs.jquery_ui_chosen neq 'y'}<small>{tr}Hold "Ctrl" in order to select multiple values{/tr}</small><br>{/if}
	<select name="{$field.ins_id}[]"{if $field.options_array[1] eq 'm'} multiple="multiple"{/if} class="form-control">
		{if $field.options_array[1] eq 'd' and (empty($field.value[0]) or $field.isMandatory ne 'y')}
			<option value=""></option>
		{/if}
		{foreach key=ku item=cat from=$field.list}
			<option value="{$cat.categId|escape}" {if in_array($cat.categId, $field.selected_categories)}selected="selected"{/if}>{$cat.relativePathString|escape}</option>
		{/foreach}
	</select>
	{foreach key=ku item=cat from=$field.list}
		<input id="cat{$cat.categId|escape}_hidden" type="hidden" name="cat_managed_{$field.ins_id}[]" value="{$cat.categId|escape}">
	{/foreach}
{elseif !empty($cat_tree)}
	{$cat_tree}{* checkboxes with descendents *}
{else}
	<div class="input-group col-md-12">
		{foreach key=ku item=iu from=$field.list name=eforeach}
			{assign var=fcat value=$iu.categId}
			<div class="col-md-4">
				<label for="cat{$iu.categId}" class="{if $field.options_array[1] eq "radio"}radio{else}checkbox{/if}">
					<input id="cat{$iu.categId|escape}_hidden" type="hidden" name="cat_managed_{$field.ins_id}[]" value="{$iu.categId|escape}">
					<input type={if $field.options_array[1] eq "radio"}"radio"{else}"checkbox"{/if} name="{$field.ins_id}[]" value="{$iu.categId}" id="cat{$iu.categId}" {if in_array($fcat, $field.selected_categories)} checked="checked"{/if}>
					{if $field.options_array[4] eq 1 && !empty($iu.description)}<a href="{$iu.description|escape}" target="tikihelp" class="tikihelp" title="{$iu.name|escape}:{$iu.description|escape}">{icon name='help'}</a>{/if}
					{$iu.name|escape}
				</label>
			</div>
		{/foreach}
	</div>
{/if}
