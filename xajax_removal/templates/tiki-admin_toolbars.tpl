{title help="Toolbars"}{tr}Admin Toolbars{/tr}{/title}
{jq notonready=true}
	function toolbars_autoreload() {
		if (document.forms['toolbars'].elements['autoreload'].checked) {
			document.forms['toolbars'].submit();
		}
	}
{/jq}

<div class="toolbars-admin clearfix">
	<form name="toolbars" method="post" action="tiki-admin_toolbars.php" onsubmit="return saveRows()">
		<div>
			<div class="adminoptionbox">
				<div class="adminoptionlabel"><label for="section">{tr}Section:{/tr}</label>
				<select id="section" name="section" onchange="javascript:toolbars_autoreload()">
					{foreach from=$sections item=name key=skey}
						<option value="{$skey}"{if $skey eq $loaded} selected="selected"{/if}>{$name|escape}</option>
					{/foreach}
				</select>
				</div>
			</div>
			<div class="adminoptionbox">
				<label for="comments">{tr}Comments{/tr}</label>
				<input id="comments" name="comments" type="checkbox" onchange="javascript:toolbars_autoreload()" {if $comments eq 'on'}checked="checked" {/if}/>
			</div>
			<div class="adminoptionbox" style="float: right;">
				<label for="view_mode">{tr}View mode{/tr}</label>
				<select id="view_mode" name="view_mode">
					{if $prefs.feature_wysiwyg eq 'y'}<option value="both"{if $view_mode eq "both"} selected{/if}>{tr}Wiki and WYSIWYG{/tr}</option>{/if}
					<option value="wiki"{if $view_mode eq "wiki"} selected{/if}>{tr}Wiki only{/tr}</option>
					{if $prefs.feature_wysiwyg eq 'y'}<option value="wysiwyg"{if $view_mode eq "wysiwyg"} selected{/if}>{tr}WYSIWYG only{/tr}</option>{/if}
					{if $prefs.feature_sheet eq 'y'}<option value="sheet"{if $view_mode eq "sheet"} selected{/if}>{tr}Spreadsheet{/tr}</option>{/if}
				</select>
			</div>
			<div class="adminoptionbox">
				<input name="load" type="submit" value="{tr}Load{/tr}"/>
				<input type="submit" name="save" value="{tr}Save{/tr}"/>
				{if $loaded neq 'global' and $not_global }<input type="submit" name="reset" value="{tr}Reset to Global{/tr}"/>{/if}
				{if $loaded eq 'global' and $not_default }<input type="submit" name="reset_global" value="{tr}Reset to defaults{/tr}"/>{/if}
				<label for="autoreload">{tr}Auto Reloading{/tr}</label>
				<input id="autoreload" name="autoreload" type="checkbox" {if $autoreload eq 'on'}checked="checked"{/if}/>
			</div>
			<input id="qt-form-field" type="hidden" name="pref" value=""/>
		</div>
	</form>
	<div class="rows">
		{foreach from=$current item=line name=line}
			<label for="row-{$smarty.foreach.line.iteration|escape}">{tr}Row{/tr}&nbsp;{$smarty.foreach.line.iteration}:</label>
			<ul id="row-{$smarty.foreach.line.iteration|escape}" class="row">
			{foreach from=$line item=bit name=bit}
				{foreach from=$bit item=tool name=tool}
					<li class="{$qtelement[$tool].class}" {if $smarty.foreach.bit.index eq 1}style="float:right;"{/if}>{$qtelement[$tool].html}</li>
				{/foreach}
			{/foreach}
			{if $smarty.foreach.line.last and $rowCount gt 1}
				{assign var=total value=`$smarty.foreach.line.total+1`}
				</ul>
				<label for="row-{$total|escape}">{tr}Row{/tr}&nbsp;{$total}:</label>
					<ul id="row-{$total|escape}" class="row">
			{/if}
			</ul>
		{/foreach}
	</div>
	<div class="lists">
		<label for="full-list-w">{tr}Formatting Tools:{/tr}</label>
		<ul id="full-list-w" class="full">
		{foreach from=$display_w item=tool}
			<li class="{$qtelement[$tool].class}">{$qtelement[$tool].html}</li>
		{/foreach}
		</ul>
	</div>
	<div class="lists">
		<label for="full-list-p">{tr}Plugin Tools:{/tr}</label><br/>
		<ul id="full-list-p" class="full">
		{foreach from=$display_p item=tool}
			<li class="{$qtelement[$tool].class}">{$qtelement[$tool].html}</li>
		{/foreach}
		</ul>
	</div>
	<div class="lists">
		<div id="toolbar_edit_div" style="display:none">
			<form name="toolbar_edit_form" method="post" action="tiki-admin_toolbars.php">
				<h2>{tr}Edit tool{/tr}</h2>
				<fieldset>
					<label for="tool_name">{tr}Name:{/tr}<small class="dialog_tips error">&nbsp;</small></label>
					<input type="text" name="tool_name" id="tool_name" class="text ui-widget-content ui-corner-all" />
					<label for="tool_label">{tr}Label:{/tr}<small class="dialog_tips error">&nbsp;</small></label><small class="dialog_tips error">&nbsp;</small>
					<input type="text" name="tool_label" id="tool_label" class="text ui-widget-content ui-corner-all" />
					<label for="tool_icon">{tr}Icon:{/tr}</label>
					<input type="text" name="tool_icon" id="tool_icon" class="text ui-widget-content ui-corner-all" />
					<label for="tool_token">{tr}Wysiwyg Token:{/tr}</label>
					<input type="text" name="tool_token" id="tool_token" class="text ui-widget-content ui-corner-all" />
					<label for="tool_syntax">{tr}Syntax:{/tr}</label>
					<input type="text" name="tool_syntax" id="tool_syntax" class="text ui-widget-content ui-corner-all" />
					<label for="tool_type">{tr}Type:{/tr}</label>
					<select name="tool_type" id="tool_type" class="select ui-widget-content ui-corner-all">
						<option value="Inline">Inline</option>
						<option value="Block">Block</option>
						<option value="LineBased">LineBased</option>
						<option value="Picker">Picker</option>
						<option value="Separator">Separator</option>
						<option value="FckOnly">FckOnly</option>
						<option value="Fullscreen">Fullscreen</option>
						<option value="TextareaResize">TextareaResize</option>
						<option value="Helptool">Helptool</option>
						<option value="FileGallery">FileGallery</option>
						<option value="Wikiplugin">Wikiplugin</option>
					</select>
					<label for="tool_plugin">{tr}Plugin name:{/tr}</label>
					<select name="tool_plugin" id="tool_plugin" class="select ui-widget-content ui-corner-all" style="margin-bottom:0.5em">
						<option value="">{tr}None{/tr}</option>
						{foreach from=$plugins key=plugin item=info}
							<option value="{$plugin|escape}">{$info.name|escape}</option>
						{/foreach}
					</select>
					<input type="hidden" value="" name="save_tool" id="save_tool" />
					<input type="hidden" value="" name="delete_tool" id="delete_tool" />
					<input type="hidden" name="section" value="{$loaded}"/>
					<input type="hidden" name="comments" value="{if $comments}on{/if}"/>
					<input type="hidden" name="autoreload" value="{if $autoreload}on{/if}"/>
				</fieldset>
			</form>
			{jq}$('#tool_icon').tiki('autocomplete', 'icon');{/jq}
		</div>
		<label for="full-list-c">{tr}Custom Tools:{/tr}</label>{icon _id="add" title="{tr}Add a new custom tool{/tr}" id="toolbar_add_custom"}<br/>
		<ul id="full-list-c" class="full">
		{foreach from=$display_c item=tool}
			<li class="{$qtelement[$tool].class}">{$qtelement[$tool].html}</li>
		{/foreach}
		</ul>
	</div>
</div>
<div class="clearfix">
{remarksbox title='Tips'}
{tr}To configure the toolbars on the various text editing areas select the section, and optionally check the comments checkbox, you want to edit and drag the icons from the left hand box to the toolbars on the right.<br />
Drag icons back from the toolbar rows onto the full list to remove them.<br />
Icons with <strong>bold</strong> labels are for wiki text areas, those that are <em>italic</em> are for WYSIWYG mode, and those that are <strong><em>bold and italic</em></strong> are for both.<br />
To save the current set use the dropdown (and optionally check the comments checkbox) at the bottom of the page to set where you want these toolbars to appear, and click Save.{/tr}
{/remarksbox}
{remarksbox title='Note' type='note'}
	{tr}If you are experiencing problems with this page after upgrading from Tiki 4 please use this link to delete all your customised tools:{/tr}
	<strong>{self_link reset_all_custom_tools=y}{tr}Delete all custom tools{/tr}{/self_link}</strong>
	<em>{tr}Warning: There is no undo!{/tr}</em>
{/remarksbox}
</div>
