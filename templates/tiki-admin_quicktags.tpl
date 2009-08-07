{title help="Quicktags"}{tr}Admin Quicktags{/tr}{/title}
<div class="quicktags-admin clearfix">
	<form method="get" action="tiki-admin_quicktags.php">
		<div>
			<label>{tr}Section{/tr}:</label>
			<select name="section" onchange="this.form.submit()">
				{foreach from=$sections item=name}
					<option{if $name eq $loaded} selected="selected"{/if}>{$name|escape}</option>
				{/foreach}
			</select>
			<label>{tr}Comments{/tr}:</label>
			<input name="comments" type="checkbox" onchange="this.form.submit()" {if $comments eq 'on'}checked{/if}/>
			{if $prefs.javascript_enabled eq 'n'}<input name="load" type="submit" value="{tr}Load{/tr}"/>{/if}
		</div>
	</form>
	<div class="rows">
		<label for="#full-list">{tr}All Quicktags:{/tr}</label><br/>
		{if $prefs.feature_jquery eq 'y'}<div id="qt_filter_div">
			{tr}Filters{/tr}:
			<input id="qt-wiki-filter" class="qt-filter" type="checkbox" checked /><label>{tr}Wiki{/tr}</label>
			<input id="qt-wys-filter" class="qt-filter" type="checkbox" checked /><label>{tr}WYSIWYG{/tr}</label>
			<input id="qt-plugin-filter" class="qt-filter" type="checkbox" checked /><label>{tr}Plugins{/tr}</label>
		</div>{/if}
		<ul id="full-list" class="full"></ul>
	</div>
	<div class="rows">
		{foreach from=$rows item=i}
			<label for="row-{$i|escape}">{tr}Row{/tr}&nbsp;{$i}:</label>
			<ul id="row-{$i|escape}" class="row"></ul>
		{/foreach}
	</div>
	<form method="post" action="tiki-admin_quicktags.php" onsubmit="return window.quicktags_sortable.saveRows()">
		<div class="selectDiv">
			<input id="qt-form-field" type="hidden" name="pref" value=""/>
			<label>{tr}Section{/tr}:</label>
			<select name="section">
				{foreach from=$sections item=name}
					<option{if $name eq $loaded} selected="selected"{/if}>{$name|escape}</option>
				{/foreach}
			</select>
			<label>{tr}Comments{/tr}:</label>
			<input name="comments" type="checkbox" {if $comments eq 'on'}checked{/if}/>
			<input type="submit" name="save" value="{tr}Save{/tr}"/>
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