{* $Id$ *}
{* include file for module edit form - to be called by ajax *}

<div class="module_selector form-group">
	<label for="assign_name">{tr}Module Name{/tr}</label>
	<select id="assign_name" name="assign_name" class="form-control">
		<option value=""></option>
		{foreach key=name item=info from=$all_modules_info}
			<option value="{$name|escape}" {if $assign_name eq $name || $assign_selected eq $name}selected="selected"{/if}>{$info.name}</option>
		{/foreach}
	</select>
	{if isset($assign_info)}<div class="description help-block">{$assign_info.description}{if isset($assign_info.documentation)} {help url=$assign_info.documentation}{/if}</div>{/if}
</div>
<div id="module_params">
	{if !empty($assign_name)}
		{if isset($assign_info.type) and $assign_info.type eq 'function'}
			<ul>
				<li><a href="#param_section_basic">{tr}Basic{/tr}</a></li>
				{foreach from=$assign_info.params key=sect item=params}
					<li><a href="#param_section_{$sect}">{tr}{$sect|capitalize}{/tr}</a></li>
				{/foreach}
			</ul>
		{else}
		{/if}
		<fieldset id="param_section_basic">
			<div class="clearfix form-group">
				<label for="assign_position">{tr}Position{/tr}</label>
				<select id="assign_position" name="assign_position" class="form-control">
					{foreach from=$module_zone_list key=code item=zone}
						<option value="{$code|escape}"{if $code eq $assign_position} selected="selected"{/if}>{$zone.name|escape}</option>
					{/foreach}
				</select>
			</div>

			<div class="clearfix form-group">
				<label for="assign_order">{tr}Order{/tr}</label>
				<select id="assign_order" name="assign_order" class="form-control">
					{section name=ix loop=$orders}
						<option value="{$orders[ix]|escape}" {if $assign_order eq $orders[ix]}selected="selected"{/if}>{$orders[ix]}</option>
					{/section}
				</select>
			</div>

			<div class="clearfix form-group">
				<label for="assign_cache">{tr}Cache Time{/tr} ({tr}secs{/tr})</label>
				<input type="text" id="assign_cache" name="assign_cache" class="form-control" value="{$assign_cache|escape}">
			</div>
			{if !isset($assign_info.type) or $assign_info.type neq 'function'}
				<div class="clearfix form-group">
					<label for="assign_rows">{tr}Rows{/tr}</label>
					<input type="text" id="assign_rows" name="assign_rows" value="{$assign_rows|escape}" class="form-control">
				</div>
			{/if}
			<div class="admin2cols adminoptionbox clearfix">
				<label for="groups">{tr}Groups{/tr}</label>
				<select multiple="multiple" id="groups" name="groups[]" class="form-control">
					{section name=ix loop=$groups}
						<option value="{$groups[ix].groupName|escape}" {if $groups[ix].selected eq 'y'}selected="selected"{/if}>{$groups[ix].groupName|escape}</option>
					{/section}
				</select>
				{if $prefs.jquery_ui_chosen ne 'y'}
					{remarksbox type="tip" title="{tr}Tip{/tr}"}{tr}Use Ctrl+Click to select multiple options{/tr}{/remarksbox}
				{/if}
				{if $prefs.modallgroups eq 'y'}
					<div class="panel panel-default"><div class="panel-body">
						{icon name="information" style="vertical-align:middle;float:left"} {tr}The{/tr} <a class="rbox-link" href="tiki-admin.php?page=module">{tr}Display Modules to All Groups{/tr}</a> {tr}setting will override your selection of specific groups.{/tr}
					</div></div>
					<br>
				{/if}
			</div>
			{if $prefs.user_assigned_modules eq 'y'}
				<div class="admin2cols adminoptionbox clearfix">
					{tr}Visibility{/tr}
						<select name="assign_type">
							<option value="D" {if $assign_type eq 'D'}selected="selected"{/if}>
								{tr}Displayed now for all eligible users even with personal assigned modules{/tr}
							</option>
							<option value="d" {if $assign_type eq 'd'}selected="selected"{/if}>
								{tr}Displayed for the eligible users with no personal assigned modules{/tr}
							</option>
							<option value="P" {if $assign_type eq 'P'}selected="selected"{/if}>
								{tr}Displayed now, can't be unassigned{/tr}
							</option>
							<option value="h" {if $assign_type eq 'h'}selected="selected"{/if}>
								{tr}Not displayed until a user chooses it{/tr}
							</option>
						</select>
						<div class="panel panel-default"><div class="panel-body">
							{icon name="information" style="vertical-align:middle;float:left;"}{tr}Because <a class="rbox-link" href="tiki-admin.php?page=module">Users can Configure Modules</a>, select either{/tr} &quot;{tr}Displayed now for all eligible users even with personal assigned modules{/tr}&quot; {tr}or{/tr} &quot;{tr}Displayed now, can't be unassigned{/tr}&quot; {tr}to make sure users will notice any newly assigned modules.{/tr}
						</div></div>
					</div>
				{/if}
			</fieldset>
			{if isset($assign_info.type) and $assign_info.type eq 'function'}
				{foreach from=$assign_info.params key=sect item=params}
					<fieldset id="param_section_{$sect}">
						{foreach from=$params key=name item=param}
							<div class="admin2cols adminoptionbox clearfix">
								<label for="assign_params[{$name|escape}]">{$param.name|escape}{if $param.required} <span class="attention">({tr}required{/tr})</span>{/if}</label>
								<input type="text" id="assign_params[{$name|escape}]" name="assign_params[{$name|escape}]" value="{$param.value|escape}"{if !empty($param.filter)} class="{$param.filter} form-control"{else} class="{$param.filter} form-control"{/if}>
								<div class="description margin-bottom-sm">
									{$param.description|escape}
									{if !empty($param.default)} - {tr}Default:{/tr} {$param.default|escape}{/if}
								</div>
							</div>
						{/foreach}
					</fieldset>
				{/foreach}
				{autocomplete element=".pagename" type="pagename" options="multiple: true, multipleSeparator:';'"}
			{else}
				<div class="admin2cols adminoptionbox clearfix">
					<a title="{tr}Parameters{/tr}" {popup text="{tr}Params: specific params to the module and/or general params ('lang', 'flip', 'title', 'decorations', 'section', 'overflow', 'page', 'nobox', 'bgcolor', 'color', 'theme', 'notitle', 'nopage'). Separator between params:'&amp;'. E.g. maxlen=15&amp;nonums=y.{/tr}" width=200 center=true}>
						<label for="assign_params">{tr}Parameters{/tr}</label>
					</a>
					<textarea id="assign_params" name="assign_params" rows="1" cols="60" class="form-control">{$assign_params|escape}</textarea>
					{help url="Module+Parameters" desc="{tr}Enter the parameters in URL format, e.g. 'nobox=y&class=rbox-data'{/tr}"}
					{self_link um_edit=$assign_name cookietab="2" _anchor="editcreate"}{tr}Edit custom module{/tr} {icon name="next"}{/self_link}
				</div>
			{/if}
		<div class="form-group clearfix">
			<div class="pull-right">
				<input type="submit" class="btn btn-default btn-sm" name="preview" value="{tr}Preview{/tr}" onclick="needToConfirm=false;">
				<input type="submit" class="btn btn-primary btn-sm" name="assign" value="{tr}Assign{/tr}" onclick="needToConfirm=false;">
			</div>
		</div>
	{/if}
</div>
