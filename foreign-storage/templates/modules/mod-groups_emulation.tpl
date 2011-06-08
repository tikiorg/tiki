{strip}
{tikimodule error=$module_params.error title=$tpl_module_title name="switch_lang" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox notitle=$module_params.notitle}
	{if isset($allGroups)}
		<fieldset>
			<legend>{tr}All Groups{/tr}</legend>
			<ul >
			{foreach from=$allGroups key=groupname item=inclusion name=ix}
				<li>{$groupname|escape}</li>
			{/foreach}
			</ul >
		</fieldset>
	{/if}

	<fieldset>
		<legend>{tr}Your Groups{/tr}</legend>
		<ul >
		{foreach from=$userGroups key=groupname item=inclusion name=ix}
			{if $inclusion eq 'included'}
				<li><i>{$groupname|escape}</i></li>
			{else}
				<li>{$groupname|escape}</li>
			{/if}
		{/foreach}
		</ul >
	</fieldset>

	{if $groups_are_emulated eq 'y'}
		<fieldset>
			<legend>{tr}Emulated Groups{/tr}</legend>
			<ul>
			{section name=ix loop=$groups_emulated}
				<li>{$groups_emulated[ix]}</li>
			{/section}
			</ul>
			<form method="get" action="tiki-emulate_groups_switch.php" target="_self">
				<div style="text-align: center"><button type="submit" name="emulategroups" value="resetgroups">{tr}Reset{/tr}</button></div>
			</form>
		</fieldset>
	{/if}

	<form method="get" action="tiki-emulate_groups_switch.php" target="_self">
		<fieldset>
			<legend>{tr}Switch to Groups{/tr}</legend>
			<select name="switchgroups[]" size="{$module_rows}" multiple="multiple" >
				{foreach from=$chooseGroups key=groupname item=inclusion name=ix}
					<option value="{$groupname|escape}" >{$groupname|escape}</option>
				{/foreach}
			</select>
			<div style="text-align: center"><button type="submit" name="emulategroups" value="setgroups" >{tr}Simulate{/tr}</button></div>
		</fieldset>
	</form>
<br />
{/tikimodule}
{/strip}
