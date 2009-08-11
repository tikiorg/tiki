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
	</form>
</div>
<div class="clearfix">
{remarksbox title='Tips'}
{tr}To configure the toolbars on the various text editing areas select the section, and optionally check the comments checkbox, you want to edit and drag the icons from the left hand box to the toolbars on the right.<br />
Drag icons back from the toolbar rows onto the full list to remove them.<br />
Icons with <strong>bold</strong> labels are for wiki text areas, those that are <em>italic</em> are for WYSIWYG mode, and those that are <strong><em>bold and italic</em></strong> are for both.<br />
To save the current set use the dropdown (and optionally check the comments checkbox) at the bottom of the page to set where you want these toolbars to appear, and click Save.{/tr}
{/remarksbox}
</div>
