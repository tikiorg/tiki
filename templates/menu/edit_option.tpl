{extends 'layout_view.tpl'}

{block name="title"}
	{title}{$title|escape}{/title}
{/block}
{block name="subtitle"}{/block}
{block name="content"}
	{include file='templates/menu/quicklinks.tpl'}
	<form action="{service controller=menu action=edit_option}" method="post" role="form" class="form">
		<div class="form form-horizontal">
			<div class="form-group">
				<label class="control-label col-md-3" for="menu_name">{tr}Name:{/tr}</label>

				<div class="col-md-9">
					<input id="menu_name" class="form-control" type="text" name="name" value="{$info.name|escape}">
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-md-3" for="menu_url">{tr}URL:{/tr}</label>

				<div class="col-md-9">
					{capture name='options'}select:function(event,ui){ldelim}ui.item.value='(('+ui.item.value+'))';{rdelim}{/capture}
					{autocomplete element="#menu_url" type='pagename' options=$smarty.capture.options}
					<input id="menu_url" type="text" name="url" value="{$info.url|escape}" class="form-control">

					<div class="help-block">{tr}For wiki page, use ((PageName)).{/tr}</div>
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-md-3" for="type">{tr}Type{/tr}:</label>

				<div class="col-md-9">
					<select name="type" class="form-control">
						<option value="o" {if $info.type eq 'o'}selected="selected"{/if}>{tr}option{/tr}</option>
						<option value="s" {if $info.type eq 's'}selected="selected"{/if}>{tr}section level 0{/tr}</option>
						<option value='1' {if $info.type eq '1'}selected="selected"{/if}>{tr}section level 1{/tr}</option>
						<option value='2' {if $info.type eq '2'}selected="selected"{/if}>{tr}section level 2{/tr}</option>
						<option value='3' {if $info.type eq '3'}selected="selected"{/if}>{tr}section level 3{/tr}</option>
						<option value="r" {if $info.type eq 'r'}selected="selected"{/if}>{tr}sorted section level 0{/tr}</option>
						<option value="-" {if $info.type eq '-'}selected="selected"{/if}>{tr}separator{/tr}</option>
					</select>
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-md-3" for="position">{tr}Position:{/tr}</label>

				<div class="col-md-9">
					<input type="text" name="position" id="position" value="{$info.position|escape}" class="form-control">
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-md-3" for="menu_groupname">{tr}Group:{/tr}</label>

				<div class="col-md-9">
					<select id="menu_groupname" name="groupname[]" class="form-control margin-bottom-md" multiple="multiple">
						<option value="">&nbsp;</option>
						{foreach $option_groups as $groupname => $selected}
							<option value="{$groupname|escape}" {$selected}>{$groupname|escape}</option>
						{/foreach}
					</select>
					{if $option_groups|@count ge '2'}
						{if $prefs.jquery_ui_chosen neq 'y'}{$ctrlMsg="{tr}Use Ctrl+Click to select multiple options{/tr}<br>"}{/if}
						{remarksbox type="tip" title="{tr}Tip{/tr}"}{$ctrlMsg}{tr}Selecting 2 groups means that the option will be seen if the user belongs to the 2 groups. If you want the 2 groups to see the option, create 2 options with one group each.{/tr}
							<br>
						{tr}If the url is ((PageName)), you do not need to put the groups, the option will be displayed only if the page can be displayed.{/tr}{/remarksbox}
					{/if}
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-md-3" for="menu_section">{tr}Sections:{/tr}</label>

				<div class="col-md-9">
					<input id="menu_section" type="text" name="section" value="{$info.section|escape}" class="form-control"><br>
					{autocomplete element="#menu_section" type="array" options="source:prefNames,multiple:true,multipleSeparator:','"}{* note, multiple doesn't work in jquery-ui 1.8 *}
					<div class="help-block">{tr}Separate multiple feature/preferences with a comma ( , ) for an AND or a vertical bar ( | ) for an OR.{/tr}</div>
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-md-3" for="menu_perm">{tr}Permissions:{/tr}</label>

				<div class="col-md-9">
					<input id="menu_perm" type="text" name="perm" value="{$info.perm|escape}" class="form-control"><br>
					{autocomplete element="#menu_perm" type="array" options="source:permNames,multiple:true,multipleSeparator:','"}{* note, multiple doesn't work in jquery-ui 1.8 *}
					<div class="help-block">{tr}Separate multiple permissions with a comma ( , ) for an AND or a vertical bar ( | ) for an OR.{/tr}</div>
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-md-3" for="menu_class">{tr}Class:{/tr}</label>

				<div class="col-md-9">
					<input id="menu_class" type="text" name="class" value="{$info.class|escape}" class="form-control"><br>

					<div class="help-block">{tr}Input an HTML class value for the menu option. Separate with a space for multiple classes.{/tr}</div>
				</div>
			</div>
			{if $prefs.feature_userlevels eq 'y'}
				<div class="form-group">
					<label class="control-label col-md-3" for="level">{tr}Level:{/tr}</label>

					<div class="col-md-9">
						<select name="level" id="level">
							<option value="0"{if $level eq 0} selected="selected"{/if}>{tr}All{/tr}</option>
							{foreach key=levn item=lev from=$prefs.userlevels}
								<option value="{$levn}"{if $info.userlevel eq $levn} selected="selected"{/if}>{$lev}</option>
							{/foreach}
						</select>
					</div>
				</div>
			{/if}
			{if $prefs.menus_items_icons eq 'y'}
				<div class="form-group">
					<label class="control-label col-md-3" for="icon">{tr}Icon:{/tr}</label>

					<div class="col-md-9">
						<input type="text" name="icon" value="{$info.icon|escape}" class="form-control">
					</div>
				</div>
			{/if}
			<div class="form-group text-center submit">
				{ticket}
				<input type="hidden" name="optionId" value="{$optionId|escape}">
				<input type="hidden" name="menuId" value="{$menuId|escape}">
				<input type="hidden" name="offset" value="{$offset|escape}">
				<input type="hidden" name="confirm" value="1">
				<input type="submit" class="btn btn-primary" name="save" value="{tr}Save{/tr}">
			</div>
		</div>

	</form>
{/block}
