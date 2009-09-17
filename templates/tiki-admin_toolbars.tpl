{title help="Toolbars"}{tr}Admin Toolbars{/tr}{/title}
<script type='text/javascript'>
<!--//--><![CDATA[//><!--
{literal}
	function toolbars_autoreload() {
		if (document.forms['toolbars'].elements['autoreload'].checked) {
			document.forms['toolbars'].submit();
		}
	}
{/literal}
//--><!]]>
</script>

<div class="toolbars-admin clearfix">
	<form name="toolbars" method="post" action="tiki-admin_toolbars.php" onsubmit="return saveRows()">
		<div>
			<div class="adminoptionbox">
			</div>
			<div class="adminoptionbox">
				<div class="adminoptionlabel"><label for="section">{tr}Section{/tr}:</label>
				<select id="section" name="section" onchange="javascript:toolbars_autoreload()">
					{foreach from=$sections item=name}
						<option{if $name eq $loaded} selected="selected"{/if}>{$name|escape}</option>
					{/foreach}
				</select>
				</div>
			</div>
			<div class="adminoptionbox">
				<label for="comments">{tr}Comments{/tr}</label>
				<input id="comments" name="comments" type="checkbox" onchange="javascript:toolbars_autoreload()" {if $comments eq 'on'}checked="checked" {/if}/>
			</div>
			<div class="adminoptionbox">
				<input name="load" type="submit" value="{tr}Load{/tr}"/>
				<input type="submit" name="save" value="{tr}Save{/tr}"/>
				{if $loaded neq 'global' }<input type="submit" name="reset" value="{tr}Reset to Global{/tr}"/>{/if}
				{if $loaded eq 'global' }<input type="submit" name="reset_global" value="{tr}Reset to defaults{/tr}"/>{/if}
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
			{foreach from=$line item=tool}
				<li class="{$qtelement[$tool].class}">{$qtelement[$tool].html}</li>
			{/foreach}
			</ul>
			{if $smarty.foreach.line.last and $rowCount gt 1}
				{assign var=total value=`$smarty.foreach.line.total+1`}
			<label for="row-{$total|escape}">{tr}Row{/tr}&nbsp;{$total}:</label>
				<ul id="row-{$total|escape}" class="row">
			{/if}
		{/foreach}
	</div>
	<div class="lists">
		<label for="#full-list-w">{tr}Formatting Toolbars:{/tr}</label>
		<div id="qt_filter_div_w" class="qt_filter_div">
			{tr}Filters{/tr}:<br />
			<label><input class="qt-wiki-filter"  type="checkbox" checked /> {tr}Wiki{/tr}</label>
			<label><input class="qt-wys-filter" type="checkbox" checked /> {tr}WYSIWYG{/tr}</label>
		</div>
		<ul id="full-list-w" class="full">
		{foreach from=$display_w item=tool}
			<li class="{$qtelement[$tool].class}">{$qtelement[$tool].html}</li>
		{/foreach}
		</ul>
	</div>
	<div class="lists">
		<label for="#full-list-p">{tr}Plugin Toolbars:{/tr}</label><br/>
		<div id="qt_filter_div_p" class="qt_filter_div">
			{tr}Filters{/tr}:<br />
			<label><input class="qt-wiki-filter" type="checkbox" checked /> {tr}Wiki{/tr}</label>
			<label><input class="qt-wys-filter" type="checkbox" checked /> {tr}WYSIWYG{/tr}</label>
		</div>
		<ul id="full-list-p" class="full">
		{foreach from=$display_p item=tool}
			<li class="{$qtelement[$tool].class}">{$qtelement[$tool].html}</li>
		{/foreach}
		</ul>
	</div>
	<div class="lists">
		<div id="toolbar_edit_div" style="display:none">
			<form name="toolbar_edit_form" method="post" action="tiki-admin_toolbars.php">
				<h2>{tr}Edit tool{/tr} (work in progress)</h2>
				<fieldset>
					<label for="tool_name">{tr}Name{/tr}:</label>
					<input type="text" name="tool_name" id="tool_name" class="text ui-widget-content ui-corner-all" />
					<label for="tool_label">{tr}Label{/tr}:</label>
					<input type="text" name="tool_label" id="tool_label" class="text ui-widget-content ui-corner-all" />
					<label for="tool_icon">{tr}Icon{/tr}:</label>
					<input type="text" name="tool_icon" id="tool_icon" class="text ui-widget-content ui-corner-all" />
					<label for="tool_token">{tr}Wysiwyg Token{/tr}:</label>
					<input type="text" name="tool_token" id="tool_token" class="text ui-widget-content ui-corner-all" />
					<label for="tool_syntax">{tr}Syntax{/tr}:</label>
					<input type="text" name="tool_syntax" id="tool_syntax" class="text ui-widget-content ui-corner-all" />
					<label for="tool_type">{tr}Type{/tr}:</label>
					<select type="text" name="tool_type" id="tool_type" class="select ui-widget-content ui-corner-all">
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
					<label for="tool_plugin">{tr}Plugin name{/tr}:</label>
					<select name="tool_plugin" id="tool_plugin" class="select ui-widget-content ui-corner-all">
						<option value="">{tr}None{/tr}</option>
						{foreach from=$plugins key=plugin item=info}
							<option value="{$plugin|escape}">{$info.name|escape}</option>
						{/foreach}
					</select>
					<input type="hidden" value="" name="save_tool" id="save_tool">
					<input type="hidden" value="" name="delete_tool" id="delete_tool">
					<input type="hidden" name="section" value="{$loaded}"/>
					<input type="hidden" name="comments" value="{if $comments}on{/if}"/>
					<input type="hidden" name="autoreload" value="{if $autoreload}on{/if}"/>
				</fieldset>
			</form>
		</div>
		<label for="#full-list-c">{tr}Custom Toolbars:{/tr}</label><br/>
		<div id="qt_filter_div_c" class="qt_filter_div">
			{tr}Filters{/tr}:<br />
			<label><input class="qt-wiki-filter" type="checkbox" checked />{tr}Wiki{/tr}</label>
			<label><input class="qt-wys-filter" type="checkbox" checked /> {tr}WYSIWYG{/tr}</label>
		</div>
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
</div>
