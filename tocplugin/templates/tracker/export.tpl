{extends 'layout_view.tpl'}

{block name="title"}
	{title}{$title|escape}{/title}
{/block}

{block name="content"}
{accordion}
	{accordion_group title="{tr}Export Tracker Items{/tr}"}
	<form class="simple no-ajax" action="{service controller=tracker action=export_items trackerId=$trackerId filterfield=$filterfield filtervalue=$filtervalue}" method="post">
		<div class="form-group">
			<label>{tr}Filename{/tr}</label>
			<input type="text" value="Tracker_{$trackerId|escape}.csv" disabled="disabled" class="form-control">
		</div>
		<div class="form-group">
			<label for="encoding">{tr}Charset encoding{/tr}</label>
			<select name="encoding" class="form-control">
				<option value="UTF-8" selected="selected">{tr}UTF-8{/tr}</option>
				<option value="ISO-8859-1">{tr}ISO-8859-1 Latin{/tr}</option>
			</select>
		</div>
		<div class="form-group">
			<label for="separator">{tr}Separator{/tr}</label>
			<input type="text" name="separator" value="," size="2" class="form-control">
		</div>
		<div class="form-group">
			<label for="delimitorL">{tr}Delimitor (left){/tr}</label>
			<input type="text" name="delimitorL" value="&quot;" size="2" class="form-control">
		</div>
		<div class="form-group">
			<label for="delimitorR">{tr}Delimitor (right){/tr}</label>
			<input type="text" name="delimitorR" value="&quot;" size="2" class="form-control">
		</div>
		<div class="form-group">
			<label for="CR">{tr}Carriage return inside field value{/tr}</label>
			<input type="text" name="CR" value="%%%" size="4" class="form-control">
		</div>
		<div class="checkbox">
			<label>
				<input type="checkbox" name="dateFormatUnixTimestamp" value="1">
				{tr}Export dates as UNIX Timestamps to facilitate importing{/tr}
			</label>
		</div>
		<div class="checkbox">
			<label>
				<input type="checkbox" name="keepItemlinkId" value="1">
				{tr}Export ItemLink type fields as the itemId of the linked item (to facilitate importing){/tr}
			</label>
		</div>
		<div class="checkbox">
			<label>
				<input type="checkbox" name="keepCountryId" value="1" >
				{tr}Export country type fields as the system name of the country (to facilitate importing){/tr}
			</label>
		</div>
		<div class="checkbox">
			<label>
				<input type="checkbox" name="parse" value="1">
				{tr}Parse as wiki text{/tr}
			</label>
		</div>
		<fieldset>
			<legend>{tr}Generic information{/tr}</legend>
			<div class="checkbox">
				<label>
					<input type="checkbox" name="showItemId" value="1" checked="checked">
					{tr}Item ID{/tr}
				</label>
			</div>
			<div class="checkbox">
				<label>
					<input type="checkbox" name="showStatus" value="1" checked="checked">
					{tr}Status{/tr}
				</label>
			</div>
			<div class="checkbox">
				<label>
					<input type="checkbox" name="showCreated" value="1" checked="checked">
					{tr}Creation date{/tr}
				</label>
			</div>
			<div class="checkbox">
				<label>
					<input type="checkbox" name="showLastModif" value="1" checked="checked">
					{tr}Last modification date{/tr}
				</label>
			</div>
		</fieldset>
		<fieldset>
			<legend>{tr}Fields{/tr}</legend>
			{foreach from=$fields item=field}
				<div class="checkbox">
					<label>
						<input type="checkbox" name="listfields[]" value="{$field.fieldId|escape}" checked="checked">
						{$field.name|escape}
					</label>
				</div>
			{/foreach}
		</fieldset>
		<div class="form-group">
			<label for="recordsMax">{tr}Number of records{/tr}</label>
			<input type="number" name="recordsMax" value="{$recordsMax|escape}" class="form-control">
		</div>
		<div class="form-group">
			<label for="recordsOffset">{tr}First record{/tr}</label>
			<input type="number" name="recordsOffset" value="1" class="form-control">
		</div>
		<div>
			<input type="submit" class="btn btn-default" value="{tr}Export{/tr}">
		</div>
	</form>
	{/accordion_group}
{accordion_group title="{tr}Quick Export{/tr}"}
	<form method="post" class="simple no-ajax form-horizontal" action="{service controller=tracker action=dump_items trackerId=$trackerId}">
		<p>{tr}Produce a CSV with basic formatting.{/tr}</p>
		{remarksbox type="info" title="{tr}Note{/tr}" icon="bricks"}
			<p>{tr}If you use field types such as 'User Preference', 'Relations' or 'Items list/Item link', please export your items through the next section below 'Export Tracker Items'{/tr}</p>
		{/remarksbox}
		<div>
			<input type="submit" class="btn btn-default" value="{tr}Export{/tr}">
		</div>
	</form>
{/accordion_group}
	{if isset($export)}
	{accordion_group title="{tr}Structure{/tr}"}
	<form class="simple" action="" method="post">
		<div class="form-group">
			<label for="export">{tr}Tracker Export{/tr}</label>
			<textarea name="export" class="form-control" rows="20">{$export|escape}</textarea>
		</div>
		<div class="description">
			{tr}Copy the definition text above and paste into the Import Structure box for a new tracker.{/tr}
		</div>
	</form>
	{/accordion_group}
	{accordion_group title="{tr}Profile Export{/tr}"}
	<form method="post" class="simple no-ajax" action="{service controller=tracker action=export_profile trackerId=$trackerId}">
		<p>{tr}Produce YAML for a profile.{/tr}</p>
		{remarksbox type="info" title="{tr}New Feature{/tr}" icon="bricks"}
			<p><em>{tr}Please note: Experimental - work in progress{/tr}</em></p>
			<p>{tr}Linked tracker and field IDs (such as those referenced in ItemLink, ItemsList field options, for instance) are not currently converted to profile object references, so will need manual replacement.{/tr}</p>
			<p>{tr}For example: $profileobject:field_ref${/tr}</p>
		{/remarksbox}
		<div>
			<input type="submit" class="btn btn-default" value="{tr}Export Profile{/tr}">
		</div>
	</form>
	{/accordion_group}
	{/if}
{/accordion}
{/block}
