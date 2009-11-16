{title}{tr}Object Permissions List{/tr}{/title}

<div class="navbar">
{button href="tiki-objectpermissions.php" _text="{tr}Manage Permissions{/tr}"}
</div>

{foreach from=$res key=type item=content}
	<h2>{tr}{$type}{/tr}</h2>

	<h3>{tr}{$type}{/tr}:{tr}Default{/tr}</h3>
{remarksbox}{tr}If an object is not in the special section, then only the default perms are on{/tr}{/remarksbox}
	<table class="normal">
	<tr><th>{tr}Group{/tr}</th><th>{tr}Permission{/tr}</th></tr>
	{cycle values="even,odd" print=false}
	{foreach from=$content.default item=default}
		<tr class="{cycle}"><td>{$default.group|escape}</td><td>{$default.perm|escape}</td></tr>
	{/foreach}
	</table>

	<h3>{tr}{$type}{/tr}: {tr}Special{/tr}</h3>
{remarksbox}{tr}If an object is not in the special section, then only the default perms are on{/tr}{/remarksbox}
	<table class="normal">
	<tr><th>{tr}Object{/tr}</th><th>{tr}Group{/tr}</th><th>{tr}Permission{/tr}</th><th>{tr}Reason{/tr}</th></tr>
	{foreach from=$content.objects item=object}
		{if !empty($object.special)}
			{foreach from=$object.special item=special}
				<tr class="{cycle}"><td>{$object.objectId}</td><td>{$special.group|escape}</td><td>{$special.perm|escape}</td><td>{$special.reason|escape} {if !empty($special.detail)}({$special.detail|escape}){/if}</td></tr>
			{/foreach}
		{/if}
	{/foreach}
	</table>
{/foreach}