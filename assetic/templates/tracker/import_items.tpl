{if $return}
	<div class="navbar">
		{include file="tracker_actions.tpl"}
	</div>
	{remarksbox type="note" title="{tr}Confirmation{/tr}"}
		<p>{tr _0=$importfile}Imported file '%0'{/tr}</p>
		<p>{tr _0=$return}Import completed with '%0'{/tr}</p>
	{/remarksbox}
{/if}
<form class="simple no-ajax" action="{service controller=tracker action=import_items trackerId=$trackerId}" method="post" enctype="multipart/form-data">
	{remarksbox type="note" title="{tr}Note{/tr}"}
		<ul>
			<li>{tr}The order of the fields does not matter, but you need to add a header with the field names{/tr}</li>
			<li>{tr}Add " -- " to the end of the fields in the header that you would like to import!{/tr}</li>
			<li>{tr}Auto-incremented itemid fields shall be included with no matter what values{/tr}</li>
		</ul>
	{/remarksbox}
	<label style="display: block">
		{tr}File{/tr}
		<input type="file" name="importfile">
	</label>
	<label style="display: block">
		{tr}Date format{/tr}
		<select name="dateFormat">
			<option value="yyyy-mm-dd">{tr}year{/tr}-{tr}month{/tr}-{tr}day{/tr}(2008-01-31)</option>
			<option value="mm/dd/yyyy">{tr}month{/tr}/{tr}day{/tr}/{tr}year{/tr}(01/31/2008)</option>
			<option value="dd/mm/yyyy">{tr}day{/tr}/{tr}month{/tr}/{tr}year{/tr}(31/01/2008)</option>
			<option value="">{tr}UNIX Timestamp{/tr}</option>
		</select>
	</label>
	<label style="display: block">
		{tr}Character encoding{/tr}
		<select name="encoding">
			<option value="UTF-8" selected="selected">{tr}UTF-8{/tr}</option>
			<option value="ISO-8859-1">{tr}ISO-8859-1{/tr}</option>
		</select>
	</label>
	<label style="display: block">
		{tr}Separator{/tr}
		<input type="text" name="separator" value="," size="2">
	</label>
	<label style="display: block">
		<input type="checkbox" name="add_items" value="1">
		{tr}Create as new items{/tr}
	</label>
	<label style="display: block">
		<input type="checkbox" name="updateLastModif" checked="checked" value="1">
		{tr}Update lastModif date if updating items (status and created are updated only if the fields are specified in the csv){/tr}
	</label>
	<label style="display: block">
		<input type="checkbox" name="convertItemLinkValues" value="1">
		{tr}Convert values of ItemLink type fields from the value in the CSV file to the itemId of the linked item. Requires the linked item to be correctly set up in advance.{/tr}
	</label>
	<div class="submit">
		<input type="hidden" name="trackerId" value="{$trackerId|escape}">
		<input type="submit" value="{tr}Import{/tr}">
	</div>
</form>
