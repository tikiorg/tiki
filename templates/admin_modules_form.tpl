{* $Id$ *}
{* include file for module edit form - to be called by ajax *}
			<span class="description">{$assign_info.description}</span>
			<fieldset>
				<div class="admin2cols adminoptionbox clearfix">
					<label for="assign_position">{tr}Position{/tr}</label>
					<select id="assign_position" name="assign_position">
						<option value="t" {if $assign_position eq 't'}selected="selected"{/if}>{tr}Top{/tr}</option>
						<option value="o" {if $assign_position eq 'o'}selected="selected"{/if}>{tr}Topbar{/tr}</option>
						<option value="p" {if $assign_position eq 'p'}selected="selected"{/if}>{tr}Page Top{/tr}</option>
						<option value="l" {if $assign_position eq 'l'}selected="selected"{/if}>{tr}Left{/tr}</option>
						<option value="r" {if $assign_position eq 'r'}selected="selected"{/if}>{tr}Right{/tr}</option>
						<option value="q" {if $assign_position eq 'q'}selected="selected"{/if}>{tr}Page Bottom{/tr}</option>
						<option value="b" {if $assign_position eq 'b'}selected="selected"{/if}>{tr}Bottom{/tr}</option>
					</select>
				</div>

				<div class="admin2cols adminoptionbox clearfix">
					<label for="assign_order">{tr}Order{/tr}</label></td>
					<select id="assign_order" name="assign_order">
						{section name=ix loop=$orders}
							<option value="{$orders[ix]|escape}" {if $assign_order eq $orders[ix]}selected="selected"{/if}>{$orders[ix]}</option>
						{/section}
					</select>
				</div>

				<div class="admin2cols adminoptionbox clearfix">
					<label for="assign_cache">{tr}Cache Time{/tr} ({tr}secs{/tr})</label>
					<input type="text" id="assign_cache" name="assign_cache" value="{$assign_cache|escape}" />
				</div>
				{if !isset($assign_info.type) or $assign_info.type neq 'function'}
					<div class="admin2cols adminoptionbox clearfix">
						<label for="assign_rows">{tr}Rows{/tr}</label>
						<input type="text" id="assign_rows" name="assign_rows" value="{$assign_rows|escape}" />
					</div>
				{/if}
			</fieldset>
			<fieldset>
			{if isset($assign_info.type) and $assign_info.type eq 'function'}
				{foreach from=$assign_info.params key=name item=param}
					<div class="admin2cols adminoptionbox clearfix">
						<div class="q1">
							<label for="assign_params[{$name|escape}]">{$param.name|escape}{if $param.required} <span class="attention">({tr}required{/tr})</span>{/if}</label>
							<br />&nbsp;
						</div>
						<div class="description q234">
							<input type="text" id="assign_params[{$name|escape}]" name="assign_params[{$name|escape}]" value="{$param.value|escape}" class="{$param.filter}" />
							<br />
							{$param.description|escape}
							{if !empty($param.default)} - {tr}Default:{/tr} {$param.default|escape}{/if}
						</div>
					</div>
				{/foreach}
			{else}
				<div class="admin2cols adminoptionbox clearfix">
					<div class="q1">
						<a {popup text="{tr}Params: specific params to the module and/or general params ('lang', 'flip', 'title', 'decorations', 'section', 'overflow', 'page', 'nobox', 'bgcolor', 'color', 'theme', 'notitle', 'nopage'). Separator between params:'&amp;'. E.g. maxlen=15&amp;nonums=y.{/tr}" width=200 center=true}>
							<label for="assign_params">{tr}Parameters{/tr}</label>
						</a>
					</div>
					<div class="description q234">
						<textarea id="assign_params" name="assign_params" rows="1" cols="60" >{$assign_params|escape}</textarea>
						{help url="Module+Parameters"}
					</div>
				</div>
			{/if}
			</fieldset>
			<div class="admin2cols adminoptionbox clearfix">
				<div class="q1">
					<label for="groups">{tr}Groups{/tr}</label>
				</div>
				<div class="description q234">
					<select multiple="multiple" id="groups" name="groups[]">
						{section name=ix loop=$groups}
							<option value="{$groups[ix].groupName|escape}" {if $groups[ix].selected eq 'y'}selected="selected"{/if}>{$groups[ix].groupName|escape}</option>
						{/section}
					</select>
					{remarksbox type="tip" title="{tr}Tip{/tr}"}{tr}Use Ctrl+Click to select multiple options{/tr}{/remarksbox}
					{if $prefs.modallgroups eq 'y'}
						<div class="simplebox">
							{icon _id=information style="vertical-align:middle;float:left"} {tr}The{/tr} <a class="rbox-link" href="tiki-admin.php?page=module">{tr}Display Modules to All Groups{/tr}</a> {tr}setting will override your selection of specific groups.{/tr}
						</div>
						<br />
					{/if}
				</div>
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
						<div class="simplebox">
							{icon _id=information style="vertical-align:middle;float:left;"}{tr}Because <a class="rbox-link" href="tiki-admin.php?page=module">Users can Configure Modules</a>, select either{/tr} &quot;{tr}Displayed now for all eligible users even with personal assigned modules{/tr}&quot; {tr}or{/tr} &quot;{tr}Displayed now, can't be unassigned{/tr}&quot; {tr}to make sure users will notice any newly assigned modules.{/tr}
						</div>
				</div>
			{/if}
			<div class="input_submit_container">
				<input type="submit" name="preview" value="{tr}Preview{/tr}" onclick="needToConfirm=false;" />
				<input type="submit" name="assign" value="{tr}Assign{/tr}" onclick="needToConfirm=false;" />
			</div>
