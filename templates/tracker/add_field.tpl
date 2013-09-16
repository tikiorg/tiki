<form class="simple" method="post" action="{service controller=tracker action=add_field}">
	<label>
		{tr}Name:{/tr}
		<input type="text" name="name" value="{$name|escape}" required="required">
	</label>
	<label style="display:none;">
		{tr}Permanent Name:{/tr}
		<input type="text" name="permName" value="{$permName|escape}" pattern="[a-zA-Z0-9_]+">
	</label>
	<label>
		{tr}Type:{/tr}
		<select name="type">
			{foreach from=$types key=k item=info}
				<option value="{$k|escape}"
					{if $type eq $k}selected="selected"{/if}>
					{$info.name|escape}
					{if $info.deprecated}- Deprecated{/if}
				</option>
			{/foreach}
		</select>
		{foreach from=$types item=info key=k}
			<div class="description {$k|escape}" style="display: none;">
				{$info.description|escape}
				{if $info.help}
					<a href="{$prefs.helpurl|escape}{$info.help|escape:'url'}" target="tikihelp" class="tikihelp" title="{$info.name|escape}">
						{icon _id=help alt=''}
					</a>
				{/if}
			</div>
		{/foreach}
	</label>
	{if $tiki_p_admin eq 'y'}
		{remarksbox type=info title="{tr}More types available{/tr}"}
			<p>{tr _0="tiki-admin.php?page=trackers"}More field types may be enabled from the <a href="%0">administration panel</a>.{/tr}</p>
		{/remarksbox}
	{else}
		{remarksbox type=info title="{tr}More types available{/tr}"}
			<p>{tr _0="https://doc.tiki.org/Tracker+Field+Type"}Contact your administrator to see if they can be enabled. The complete field type list is available in the <a rel="external" class="external" href="%0">documentation</a>.{/tr}</p>
		{/remarksbox}
	{/if}
	<label>
		{tr}Description:{/tr}
		<textarea name="description">{$description|escape}</textarea>
	</label>
	<label>
		<input type="checkbox" name="description_parse" value="1"
			{if $descriptionIsParsed}checked="checked"{/if}
			>
		{tr}Description contains wiki syntax{/tr}
	</label>
	<label>
		<input type="checkbox" name="adminOnly" value="1">
		{tr}Restrict visibility to administrators{/tr}
		<div class="description">
			{tr}Useful if you are working on a live tracker.{/tr}
		</div>
	</label>
	<div>
		<input type="submit" class="btn btn-default" name="submit" value="{tr}Add Field{/tr}">
		<input type="submit" class="btn btn-default" name="submit_and_edit" value="{tr}Add Field &amp; Edit Advanced Options{/tr}">
		<input type="hidden" name="trackerId" value="{$trackerId|escape}">
		<input type="hidden" name="next" value="close">
	</div>
</form>
