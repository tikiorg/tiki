{popup_init src="lib/overlib.js"}
{*Smarty template*}
<a class="pagetitle" href="tiki-g-admin_instance.php?iid={$iid}">{tr}Admin instance{/tr}</a>
<br /><br />
{include file=tiki-g-monitor_bar.tpl}
<h3>{tr}Instance{/tr}: {$ins_info.instanceId} (Process: {$proc_info.name} {$proc_info.version})</h3>
<form action="tiki-g-admin_instance.php" method="post">
<input type="hidden" name="iid" value="{$iid|escape}" />
<table class="normal">
<tr>
	<td class="formcolor">{tr}Created{/tr}</td>
	<td class="formcolor">{$ins_info.started|tiki_long_date}</td>
</tr>
<tr>
	<td class="formcolor">{tr}Workitems{/tr}</td>
	<td class="formcolor"><a class="link" href="tiki-g-monitor_workitems.php?filter_instance={$ins_info.instanceId}">{$ins_info.workitems}</a></td>
</tr>
<tr>
	<td class="formcolor">{tr}Status{/tr}</td>
	<td class="formcolor">
	<select name="status">
		<option value="active" {if $ins_info.status eq 'active'}selected="selected"{/if}>{tr}active{/tr}</option>
		<option value="exception" {if $ins_info.status eq 'exception'}selected="selected"{/if}>{tr}exception{/tr}</option>
		<option value="completed" {if $ins_info.status eq 'completed'}selected="selected"{/if}>{tr}completed{/tr}</option>
    	<option value="aborted" {if $ins_info.status eq 'aborted'}selected="selected"{/if}>{tr}aborted{/tr}</option>
	</select>
	</td>
</tr>
<tr>
	<td class="formcolor">{tr}Owner{/tr}</td>
	<td class="formcolor">
	<select name="owner">
	{section name=ix loop=$users}
	<option value="{$users[ix].user|escape}" {if $users[ix].user eq $ins_info.owner}selected="selected"{/if}>{$users[ix].user}</option>
	{/section}
	</select>
	</td>
</tr>
<tr>
	<td class="formcolor">{tr}Send all to{/tr}</td>
	<td class="formcolor">
	<select name="sendto">
	  <option value="">{tr}Don't move{/tr}</option>
	  {section loop=$activities name=ix}
	  <option value="{$activities[ix].activityId|escape}">{$activities[ix].name}</option>
	  {/section}
	</select>
	</td>
</tr>

<tr>
	<td class="formcolor">{tr}Activities{/tr}</td>
	<td class="formcolor">
		{if count($acts)}
		<table class="normal">
		<tr>
			<td class="heading">{tr}Activity{/tr}</td>
			<td class="heading">{tr}Act status{/tr}</td>
			<td class="heading">{tr}User{/tr}</td>
		</tr>
		
		{section name=ix loop=$acts}
		<tr>
			<td class="odd">{$acts[ix].name}
			{if $acts[ix].isInteractive eq 'y'}
			<a href="tiki-g-run_activity.php?activityId={$acts[ix].activityId}&amp;iid={$iid}"><img src='lib/Galaxia/img/icons/next.gif' border='0' alt='{tr}run{/tr}' title='{tr}run{/tr}' /></a>
			
			
			{/if}

			</td>
			<td class="odd">{$acts[ix].actstatus}</td>
			<td class="odd">
			<select name="acts[{$acts[ix].activityId}]">
			<option value="*" value="*" {if $acts[ix].user eq '*'}selected='selected'{/if}>*</option>
			{section name=ix loop=$users}
			<option value="{$users[ix].user|escape}" {if $users[ix].user eq $acts[ix].user}selected="selected"{/if}>{$users[ix].user}</option>
			{/section}
			</select>
			</td>
		</tr>
		{/section}
		</table>
		{else}
		&nbsp;
		{/if}
	</td>
</tr>	
<tr>
	<td class="formcolor">&nbsp;</td>
	<td class="formcolor"><input type="submit" name="save" value="{tr}update{/tr}" /></td>
</tr>
</table>
</form>
<h3>{tr}Properties{/tr}</h3>
<form action="tiki-g-admin_instance.php" method="post">
<input type="hidden" name="iid" value="{$iid|escape}" />
<table class="normal">
<tr>
	<td class="heading">{tr}Property{/tr}</td>
	<td class="heading">{tr}Value{/tr}</td>
</tr>
{foreach from=$props item=item key=key}
<tr>
	<td class="odd">

	 <a href="tiki-g-admin_instance.php?iid={$iid}&amp;unsetprop={$key}"><img border="0" src='lib/Galaxia/img/icons/trash.gif' alt='{tr}del{/tr}' title='{tr}del{/tr}' /></a>
	 <b>{$key}</b>
	 </td>
	<td class="odd">
	{if strlen($item)>80}
	<textarea name="props[$key]" cols="80" rows="{$item|div:80:20}">{$item|escape}</textarea>
	{else}
	<input type="text" name="props[{$key}]" value="{$item|escape}" />
	{/if}
	</td>
</tr>
{/foreach}
<tr>
	<td class="odd">&nbsp;</td>
	<td class="odd"><input type="submit" name="saveprops" value="{tr}update{/tr}" /></td>
</tr>

</table>
</form>
<h3>{tr}Add property{/tr}</h3>
<form action="tiki-g-admin_instance.php" method="post">
<input type="hidden" name="iid" value="{$iid|escape}" />
<table class="normal">
<tr>
	<td class="formcolor">{tr}name{/tr}</td>
	<td class="formcolor"><input type="text" name="name" /></td>
</tr>
<tr>
	<td class="formcolor">{tr}value{/tr}</td>
	<td class="formcolor"><textarea name="value" rows="4" cols="80"></textarea></td>
</tr>
<tr>
	<td class="formcolor">&nbsp;</td>
	<td class="formcolor"><input type="submit" name="addprop" value="{tr}add{/tr}" /></td>
</tr>

</table>
</form>

{include file=tiki-g-instance_comments.tpl}
