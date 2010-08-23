{title}{tr}Object Permissions List{/tr}{/title}

<div class="navbar">
{button href="tiki-objectpermissions.php" _text="{tr}Manage Permissions{/tr}"}
{foreach from=$res key=type item=content}
	{button href="#$type" _text="{tr}$type{/tr}"}	 
{/foreach}
</div>

{tabset name='tabs_list_object_permissions'}
{foreach from=$res key=type item=content}
	{tab name="{tr}$type{/tr}"}		
	<h2 id="{$type}">{tr}{$type|ucfirst}{/tr}</h2>

	<h3>{tr}{$type|ucfirst}{/tr}: {tr}global permissions{/tr}</h3>
{remarksbox}{tr}If an object is not listed in the section below, then only the global perms are on{/tr}{/remarksbox}
	<table class="normal">
	<tr><th>{tr}Group{/tr}</th><th>{tr}Permission{/tr}</th></tr>
	{cycle values="even,odd" print=false}
	{foreach from=$content.default item=default}
		<tr class="{cycle}"><td>{$default.group|escape}</td><td>{$default.perm|escape}</td></tr>
	{/foreach}
	</table>

	<h3>{tr}{$type|ucfirst}{/tr}: {tr}category or object permissions{/tr}</h3>
{remarksbox}{tr}If an object is not listed in this section, then only the global perms are on{/tr}{/remarksbox}
	<table class="normal">
	<tr><th>{tr}Object{/tr}</th><th>{tr}Group{/tr}</th><th>{tr}Permission{/tr}</th><th>{tr}Reason{/tr}</th></tr>
	{foreach from=$content.objects item=object}
		{if !empty($object.special)}
			{foreach from=$object.special item=special}
				<tr class="{cycle}">
					<td>{$special.objectName}</td>
					<td>{$special.group|escape}</td>
					<td>{$special.perm|escape}</td>
					<td>
						{if !empty($special.objectId)}
							<a href="tiki-objectpermissions.php?objectId={$special.objectId}&amp;objectType={$special.objectType}&amp;objectName={$special.objectName|escape}">{tr}{$special.reason|escape}{/tr}</a>
						{else}
							{tr}{$special.reason|escape}{/tr}
						{/if}
						{if !empty($special.detail)}({$special.detail|escape}){/if}
					</td>
				</tr>
			{/foreach}
		{/if}
	{/foreach}
	</table>
	{/tab}
{/foreach}
{/tabset}