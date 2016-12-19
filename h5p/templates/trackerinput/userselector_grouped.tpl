{* $Id: userselector_grouped.tpl 60482 2016-11-30 11:14:10Z kroky6 $ *}
<div class="row">
	<div class="col-xs-6">
		{tr}Filter by group:{/tr}
		<select id="user_group_selector_{$field.fieldId}" multiple="multiple" class="form-control">
			{section name=ix loop=$data.groups}
				<option value="{$data.groups[ix]|escape}" {if (in_array($data.groups[ix], $data.selected_groups))}selected{/if}>{$data.groups[ix]}</option>
			{/section}
		</select>
	</div>
	<div class="col-xs-6">
		{tr}Select user(s):{/tr}
		<select name="{$field.ins_id}[]" id="user_selector_{$field.fieldId}" multiple="multiple" class="form-control col-xs-6">
			{section name=ix loop=$data.selected_users}
				<option value="{$data.selected_users[ix]}" selected>{if ($field.showRealname == 'y')}{$data.selected_users[ix]|username}{else}{$data.selected_users[ix]}{/if}</option>
			{/section}
		</select>
	</div>
</div>
{jq}
	var users{{$field.fieldId}} = {{$data.users|json_encode}};
	$("#user_group_selector_{{$field.fieldId}}").change(function() {
		var $selector = $('#user_selector_{{$field.fieldId}}'),
			selected = $selector.val(),
			group_users = {};
		$selector.empty();
		$.map($(this).val(), function(group) {
			$.extend(group_users, users{{$field.fieldId}}[group] || {});
		});
		$.map(Object.keys(group_users), function(user){
			return {value: user, label: group_users[user]};
		}
		).sort(function(u1, u2) {
			u1 = u1.label.toUpperCase();
			u2 = u2.label.toUpperCase();
			return u1 < u2 ? -1 : ( u1 > u2 ? 1 : 0 );
		}).map(function(opt) {
			$('<option>')
				.attr('value', opt.value)
				.text(opt.label)
				.appendTo($('#user_selector_{{$field.fieldId}}'));
		});
		$selector.val(selected).trigger('chosen:updated');
	}).trigger('change');
{/jq}
