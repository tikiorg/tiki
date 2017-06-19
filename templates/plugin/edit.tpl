{extends 'layout_view.tpl'}

{block name="subtitle"}
	{help url=$info.documentation}
{/block}

{block name="title"}
	<h3>{$title} {block name=subtitle}{/block}</h3>
{/block}

{block name="content"}
	{function plugin_edit_row}{* needs to be in the same block it seems? *}
		{if $param.area}{$inputId=$param.area|escape}{else}{$inputId="param_{$name|escape}_input"}{/if}
		<div class="col-sm-3">
			<label for="{$inputId}">{$param.name|escape}</label>
			{if not empty($param.type)}
				{$onclick = "openFgalsWindow('{$prefs.home_file_gallery|sefurl:'file gallery':true}filegals_manager={$param.area|escape}&id=1', true);return false;"}
				{if $param.type eq 'image'}
					<br>{icon name='image' title='{tr}Select image{/tr}' onclick=$onclick class='btn btn-xs btn-default'}
				{elseif $param.type eq 'fileId'}
					<br>{icon name='file' title='{tr}Pick a file{/tr}' onclick=$onclick class='btn btn-xs btn-default'}
				{elseif $param.type eq 'kaltura'}
					{jq}
$("#picker_{{$name|escape}}").parent().click(function () {
	$(this).serviceDialog({
		title: tr("Upload or record media"),
		width: 710,
		height: 450,
		hideButtons: true,
		success: function (data) {
			if (data.entries) {
				input.value = data.entries[0];
			}
		}
	});
	return false;
});
					{/jq}
					<br>{icon name='video' title='{tr}Upload or record media{/tr}' href={service controller='kaltura' action='upload'} id='picker_'|cat:$name|escape class='btn btn-xs btn-default'}
				{/if}
			{/if}
		</div>
		<div class="col-sm-9">
			{if not empty($param.parentparam.name)}
				{$groupClass = " group-`$param.parentparam.name`"}
				{$dataAttribute = " data-parent_name='`$param.parentparam.name`' data-parent_value='`$param.parentparam.value`'"}
			{else}
				{$groupClass = ''}
				{$dataAttribute = ''}
			{/if}
			{if empty($param.options)}
				{if isset($pluginArgs[$name])}{$val = $pluginArgs[$name]}{else}{$val=''}{/if}
				{if not empty($param.selector_type)}
					{if empty($param.separator)}
						{object_selector type=$param.selector_type _simplevalue=$val _simplename='params['|cat:$name|escape|cat:']' _simpleid=$inputId _parent=$param.parent _parentkey=$param.parentkey _class=$groupClass}
					{else}
						{object_selector_multi type=$param.selector_type _simplevalue=$val _simplename='params['|cat:$name|escape|cat:']' _simpleid=$inputId _separator=$param.separator _parent=$param.parent _parentkey=$param.parentkey _sort=$param.sort_order _class=$groupClass}
					{/if}
					{if not empty($param.parentparam.name)}
						{jq notonready=true}$("#{{$inputId}}").attr("data-parent_name", "{{$param.parentparam.name}}").attr("data-parent_value", "{{$param.parentparam.value}}");{/jq}
					{/if}
				{else}
					<input value="{$val|escape}" class="form-control{$groupClass}" id="{$inputId}" type="text" name="params[{$name|escape}]"{$dataAttribute}>
					{if not empty($param.filter)}
						{if $param.filter eq "pagename"}
							{jq}$({{$inputId}}).tiki("autocomplete", "pagename");{/jq}
						{elseif $param.filter eq "groupname"}
							{jq}$({{$inputId}}).tiki("autocomplete", "groupname", {multiple: true, multipleSeparator: "|"});{/jq}
						{elseif $param.filter eq "username"}
							{jq}$({{$inputId}}).tiki("autocomplete", "username", {multiple: true, multipleSeparator: "|"});{/jq}
						{elseif $param.filter eq "date"}
							{jq}$({{$inputId}}).tiki("datepicker");{/jq}
						{/if}
					{/if}
				{/if}
			{else}
				<select class="form-control{$groupClass}" type="text" name="params[{$name|escape}]" id="{$inputId}"{$dataAttribute}>
					{foreach $param.options as $option}
						<option value="{$option.value|escape}" {if isset($pluginArgs[$name]) and $pluginArgs[$name] eq $option.value} selected="selected"{/if}>
							{$option.text|escape}
						</option>
					{/foreach}
				</select>
			{/if}
			<div class="description">{$param.description}</div>
		</div>
	{/function}
	<div class="form-horizontal" id="plugin_params">
		<form action="{service controller='plugin' action='edit'}" method="post">
			{include file='access/include_ticket.tpl'}
			{foreach $info.params as $name => $param}
				<div class="form-group{if $param.advanced} advanced{/if}" id="param_{$name|escape}">
					{plugin_edit_row param=$param name=$name info=$info pluginArgs=$pluginArgs}
				</div>
			{/foreach}
			{if not empty($info.advancedParams)}
				{button _text='Advanced' _onclick="$('.form-group.advanced.default').toggle('fast'); return false;" _class='btn btn-xs margin-bottom-md'}
				{foreach $info.advancedParams as $name => $param}
					<div class="form-group advanced default" style="display: none;">
						{plugin_edit_row param=$param name=$name info=$info pluginArgs=$pluginArgs}
					</div>
				{/foreach}
			{/if}

			<div class="form-group"{if empty($info.body)} style="display:none"{/if}>
				<label for="content" class="col-sm-3">{tr}Body{/tr}</label>
				<div class="col-sm-9">
					<textarea name="content" id="content" class="form-control" rows="12">{$bodyContent|escape}</textarea>
					<div class="description">{$info.body}</div>
				</div>
			</div>

			<div class="submit">
				<input type="hidden" name="page" value="{$pageName|escape}">
				<input type="hidden" name="type" value="{$type}">
				<input type="hidden" name="index" value="{$index}">
				<input type="hidden" name="args" value="{$pluginArgsJSON|escape}">{* It does not look like this is still in use after r61664. Chealer *}
				<input type="submit" class="btn btn-primary" value="{tr}Save{/tr}">
			</div>

			{if $type eq 'module'}
				{jq}
					$("#param_module_input").change(function () {
						$(this).parents(".modal-content").load(
							$.service("plugin", "edit", {
								area_id: "{{$area_id}}",
								type: "{{$type}}",
								index: {{$index}},
								page: "{{$pageName|escape:javascript}}",
								pluginArgs: {{$pluginArgsJSON}},
								bodyContent: "{{$bodyContent|escape:javascript}}",
								edit_icon: "{{$edit_icon}}",
								selectedMod: $(this).val(),
								modal: 1
							}),
							function () {
								$(this).tikiModal();
								if (jqueryTiki.chosen) {
									$(this).applyChosen();
								}
							}
						).tikiModal(tr("Loading..."));
					});
				{/jq}
			{/if}

		</form>
	</div>
{/block}

