{title help="Quicktags"}{tr}Admin Quicktags{/tr}{/title}
<div class="quicktags-admin">
	<form method="get" action="tiki-admin_quicktags.php">
		<div>
			<select name="section">
				{foreach from=$sections item=name}
					<option{if $name eq $loaded} selected="selected"{/if}>{$name|escape}</option>
				{/foreach}
			</select>
			<input name="load" type="submit" value="{tr}Load{/tr}"/>
		</div>
	</form>
	<label>{tr}All Quicktags:{/tr}</label><br/>
	<ul id="full-list" class="full"></ul>

	<div class="rows">
		{foreach from=$rows item=i}
			<label for="row-{$i|escape}">{tr}Row{/tr}&nbsp;{$i}:</label>
			<ul id="row-{$i|escape}" class="row"></ul>
		{/foreach}
	</div>
	<form method="post" action="tiki-admin_quicktags.php" onsubmit="return window.quicktags_sortable.saveRows()">
		<div>
			<input id="qt-form-field" type="hidden" name="pref" value=""/>
			<select name="section">
				{foreach from=$sections item=name}
					<option{if $name eq $loaded} selected="selected"{/if}>{$name|escape}</option>
				{/foreach}
			</select>
			<input type="submit" name="save" value="{tr}Save{/tr}"/>
		</div>
	</form>
</div>
