{title}{tr}Object Permissions List{/tr}{/title}

<div class="navbar">
{button href="tiki-objectpermissions.php" _text="{tr}Manage Permissions{/tr}"}
</div>

{tabset name='tabs_list_object_permissions'}
{foreach from=$res key=type item=content}
	{tab name="{tr}$type{/tr}"}
	
	<!-- ul>
		<li><a href="#tabs-1">{tr}global permissions{/tr}</a></li>
		<li><a href="#tabs-2">{tr}object permissions{/tr} ({$content.objects|@count})</a></li>
		<li><a href="#tabs-3">{tr}category permissions{/tr} ({$content.category|@count})</a></li>
	</ul-->

	<h3>{tr}{$type|ucfirst}:{/tr} {tr}global permissions{/tr}</h3>
	<div id="tabs-1">
	{remarksbox}{tr}If an object is not listed in the section below, then only the global perms are on{/tr}{/remarksbox}
	<table class="normal">
	<tr><th>{tr}Group{/tr}</th><th>{tr}Permission{/tr}</th></tr>
	{cycle values="even,odd" print=false}
	{foreach from=$content.default item=default}
		<tr class="{cycle}"><td>{$default.group|escape}</td><td>{$default.perm|escape}</td></tr>
	{/foreach}
	</table>
	</div>
	<h3>{tr}{$type|ucfirst}:{/tr} {tr}object permissions{/tr} ({$content.objects|@count})</h3>
	<div id="tabs-2">
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
	</div>
	<h3>{tr}{$type|ucfirst}:{/tr} {tr}category permissions{/tr} ({$content.category|@count})</h3>
	<div id="tabs-3">
	{remarksbox}{tr}If an object is not listed in this section, then only the global perms are on{/tr}{/remarksbox}
	<table class="normal">
	<tr><th>{tr}Object{/tr}</th><th>{tr}Group{/tr}</th><th>{tr}Permission{/tr}</th><th>{tr}Reason{/tr}</th></tr>
	{foreach from=$content.category item=object}
		{if !empty($object.category)}
			{foreach from=$object.category item=special}
				<tr class="{cycle}">
					<td>{if isset($object.objectName)}{$object.objectName}{else}{$object.objectId}{/if}</td>
					<td>{$special.group|escape}</td>
					<td>{$special.perm|escape}</td>
					<td>
						{if !empty($special.objectId)}
							<a href="tiki-objectpermissions.php?objectId={$special.objectId}&amp;objectType={$special.objectType}&amp;objectName={$special.objectName|escape}">{tr}{$special.reason|escape}:{/tr} {$special.objectName}</a>
						{else}
							{tr}{$special.reason|escape}: {$special.objectName}{/tr}
						{/if}
						{if !empty($special.detail)}({$special.detail|escape}){/if}
					</td>
				</tr>
			{/foreach}
		{/if}
	{/foreach}
	</table>
	</div>
	{/tab}
{/foreach}
{/tabset}
{*jq notonready=true}if ($.ui) { $("#role_main fieldset").tabs();}{/jq*}
{* ui tabs inside tikitabs don't get on :( *}
{jq}if ($.ui) { $("#role_main fieldset").tiki("accordion", {heading: "h3"});}{/jq}
