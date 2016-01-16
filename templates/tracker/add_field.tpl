{extends 'layout_view.tpl'}

{block name="title"}
	{title}{$title|escape}{/title}
{/block}

{block name="content"}
<form method="post" action="{service controller=tracker action=add_field}">
	<div class="form-group">
		<label for="name" class="control-label">{tr}Name{/tr}</label>
		<input type="text" name="name" value="{$name|escape}" required="required" class="form-control">
	</div>
	<div class="form-group" style="display: none;">
		<label for="permName" class="control-label">{tr}Permanent name{/tr}</label>
		<input type="text" name="permName" value="{$permName|escape}" pattern="[a-zA-Z0-9_]+" class="form-control">
	</div>
	<div class="form-group">
		<label for="type" class="control-label">{tr}Type{/tr}</label>
		<select name="type" class="form-control">
			{foreach from=$types key=k item=info}
				<option value="{$k|escape}"
					{if $type eq $k}selected="selected"{/if}>
					{$info.name|escape}
					{if $info.deprecated}- Deprecated{/if}
				</option>
			{/foreach}
		</select>
		{foreach from=$types item=info key=k}
			<div class="help-block {$k|escape}" style="display: none;">
				{$info.description|escape}
				{if $info.help}
					<a href="{$prefs.helpurl|escape}{$info.help|escape:'url'}" target="tikihelp" class="tikihelp" title="{$info.name|escape}">
						{icon name='help'}
					</a>
				{/if}
			</div>
		{/foreach}
	</div>
	{remarksbox type=info title="{tr}More types available{/tr}"}
		{if $tiki_p_admin eq 'y'}
			<p>{tr _0="tiki-admin.php?page=trackers&cookietab=3"}More field types may be enabled from the <a href="%0">administration panel</a>.{/tr}</p>
		{else}
			<p>{tr _0="https://doc.tiki.org/Tracker+Field+Type"}Contact your administrator to see if they can be enabled. The complete field type list is available in the <a rel="external" class="external" href="%0">documentation</a>.{/tr}</p>
		{/if}
	{/remarksbox}
	<div class="form-group">
		<label for="description" class="control-label">{tr}Description{/tr}</label>
		<textarea name="description" class="form-control">{$description|escape}</textarea>
	</div>
	<div class="checkbox">
		<label>
			<input type="checkbox" name="description_parse" value="1"
				{if $descriptionIsParsed}checked="checked"{/if}
				>
			{tr}Description contains wiki syntax{/tr}
		</label>
	</div>
	<div class="checkbox">
		<label>
			<input type="checkbox" name="adminOnly" value="1">
			{tr}Restrict visibility to administrators{/tr}
			<div class="help-block">
				{tr}Useful if you are working on a live tracker.{/tr}
			</div>
		</label>
	</div>
	<div class="submit">
		<input type="submit" class="btn btn-default" name="submit_and_edit" value="{tr}Add Field &amp; Edit Advanced Options{/tr}">
		<input type="submit" class="btn btn-primary" name="submit" value="{tr}Add Field{/tr}">
		<input type="hidden" name="trackerId" value="{$trackerId|escape}">
		<input type="hidden" name="next" value="close">
		<input type="hidden" name="modal" value="{$modal|escape}">
	</div>
</form>
{/block}
