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
			<div class="floatright">
				<em><label for="autoreload">{tr}Auto Reloading{/tr}:</label></em>
				<input id="autoreload" name="autoreload" type="checkbox" {if $autoreload eq 'on'}checked="checked"{/if}/>
			</div>
			<label>{tr}Section{/tr}:</label>
			<select name="section" onchange="javascript:toolbars_autoreload()">
				{foreach from=$sections item=name}
					<option{if $name eq $loaded} selected="selected"{/if}>{$name|escape}</option>
				{/foreach}
			</select>
			<label>{tr}Comments{/tr}:</label>
			<input name="comments" type="checkbox" onchange="javascript:toolbars_autoreload()" {if $comments eq 'on'}checked="checked"{/if}/>
			<input name="load" type="submit" value="{tr}Load{/tr}"/>
			<input type="submit" name="save" value="{tr}Save{/tr}"/>
			{if $loaded neq 'global' }<input type="submit" name="reset" value="{tr}Reset to Global{/tr}"/>{/if}
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
		<label for="#full-list-w">{tr}Formatting Toolbars:{/tr}</label><br/>
		<div id="qt_filter_div_w" class="qt_filter_div">
			{tr}Filters{/tr}:
			<input class="qt-wiki-filter"  type="checkbox" checked /><label>{tr}Wiki{/tr}</label>
			<input class="qt-wys-filter" type="checkbox" checked /><label>{tr}WYSIWYG{/tr}</label>
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
			{tr}Filters{/tr}:
			<input class="qt-wiki-filter" type="checkbox" checked /><label>{tr}Wiki{/tr}</label>
			<input class="qt-wys-filter" type="checkbox" checked /><label>{tr}WYSIWYG{/tr}</label>
		</div>
		<ul id="full-list-p" class="full">
		{foreach from=$display_p item=tool}
			<li class="{$qtelement[$tool].class}">{$qtelement[$tool].html}</li>
		{/foreach}
		</ul>
	</div>
	<div class="lists">
		<div class="cbox" id="toolbar_edit_div" style="display:none">
			<form name="toolbar_edit_form" method="post" action="tiki-admin_toolbars.php">
				<h2>{tr}Edit tool{/tr} (work in progress)</h2>
				<div class="adminoptionbox">
					<label for="">{tr}Name{/tr}:</label>
					<input type="text" name="tool_name" id="tool_name" />
				</div>
				<div class="adminoptionbox">
					<label for="">{tr}Label{/tr}:</label>
					<input type="text" name="tool_label" id="tool_label" />
				</div>
				<div class="adminoptionbox">
					<label for="">{tr}Icon{/tr}:</label>
					<input type="text" name="tool_icon" id="tool_icon" />
				</div>
				<div class="adminoptionbox">
					<label for="">{tr}Wysiwyg Token{/tr}:</label>
					<input type="text" name="tool_token" id="tool_token" />
				</div>
				<div class="adminoptionbox">
					<label for="">{tr}Syntax{/tr}:</label>
					<input type="text" name="tool_syntax" id="tool_syntax" />
				</div>
				<div align="center">
					<input type="submit" value="Save" name="save_tool" id="save_tool">
					<input type="button" value="Cancel" name="cancel_tool" id="cancel_tool">
				</div>
				<input type="hidden" name="section" value="{$loaded}"/>
				<input type="hidden" name="comments" value="{if $comments}on{/if}"/>
				<input type="hidden" name="autoreload" value="{if $autoreload}on{/if}"/>
			</form>
		</div>
		<label for="#full-list-c">{tr}Custom Toolbars:{/tr}</label><br/>
		<div id="qt_filter_div_c" class="qt_filter_div">
			{tr}Filters{/tr}:
			<input class="qt-wiki-filter" type="checkbox" checked /><label>{tr}Wiki{/tr}</label>
			<input class="qt-wys-filter" type="checkbox" checked /><label>{tr}WYSIWYG{/tr}</label>
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
