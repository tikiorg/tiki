{popup_init src="lib/overlib.js"}
{*Smarty template*}
<h1><a class="pagetitle" href="tiki-g-admin_instance.php?iid={$iid}">{tr}Admin instance{/tr}</a>
</h1>
{include file=tiki-g-monitor_bar.tpl}
<h2>{tr}Process:{/tr} {$proc_info.name} {$proc_info.version}<br />
<form method="POST" action="tiki-g-admin_instance.php?aid={$aid}">
{tr}Instance{/tr}: <input type="text" name="name" value="{$ins_info.name}" /></h2>
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
    	<option value="aborted" {if $ins_info.status eq 'aborted'}selected="selected"{/if}>{tr}Aborted{/tr}</option>
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
	  {if $activities[ix].type neq 'standalone' and $activities[ix].isInteractive eq 'y'}
	  <option value="{$activities[ix].activityId|escape}">{$activities[ix].name}</option>
	  {/if}
	  {/section}
	</select>
	</td>
</tr>

<tr>
	<td class="formcolor"><h2>{tr}Activities{/tr}</h2></td>
</tr>
<tr>
	<td class="formcolor" colspan="2">
		{if count($acts)}
		<table class="normal">
			<tr>
				<td class="formcolor">{tr}Name{/tr}</td>
				<td class="formcolor">{tr}Started{/tr}</td>
				<td class="formcolor">{tr}Act status{/tr}</td>
				<td class="formcolor">{tr}Expiration Date{/tr}</td>
				<td class="formcolor">{tr}Ended{/tr}</td>
				<td class="formcolor">{tr}User{/tr}</td>
			</tr>
		{section name=ix loop=$acts}
			<tr>
				<td class="{cycle values='odd,even' advance=false}"><a href="tiki-g-admin_instance_activity.php?iid={$iid}&aid={$acts[ix].activityId}">{$acts[ix].name}</a></td>
				<td class="{cycle advance=false}">{$acts[ix].iaStarted|date_format:"%b %e, %Y - %H:%M"|capitalize}</td>
				<td class="{cycle advance=false}">{$acts[ix].actstatus}</td>
				<td class="{cycle advance=false}">{if $acts[ix].exptime eq 0 && $acts[ix].type eq 'activity' && $acts[ix].isInteractive eq 'y'}{tr}Not Defined{/tr}{elseif $acts[ix].type != 'activity'}&lt;{$acts[ix].type}&gt;{elseif $acts[ix].isInteractive eq 'n'}{tr}Not Interactive{/tr}{else}{$acts[ix].exptime|date_format:"%b %e, %Y - %H:%M"|capitalize}{/if}</td>
				<td class="{cycle advance=false}">{if $acts[ix].ended eq 0}{tr}Not Ended{/tr}{else}{$acts[ix].ended|date_format:"%b %e, %Y - %H:%M"|capitalize}{/if}</td>
				<td class="{cycle values='odd,even'}">{$acts[ix].user}</td>
		{/section}
		{/if}
		</table>
	</td>
</tr>	
<tr>
	<td class="formcolor">&nbsp;</td>
	<td class="formcolor"><input type="submit" name="save" value="{tr}Update{/tr}" /></td>
</tr>
</table>
</form>

<h2>{tr}Properties{/tr}</h2>
<form method="POST" action="tiki-g-admin_instance.php?iid={$iid}&aid={$aid}">
<input type="hidden" name="iid" value="{$iid|escape}" />
<table class="normal">
<tr>
	<td class="heading">{tr}Property{/tr}</td>
	<td class="heading">{tr}Value{/tr}</td>
</tr>
{foreach from=$props item=item key=key}
<tr>
	<td class="odd">

	 <a href="tiki-g-admin_instance.php?iid={$iid}&amp;unsetprop={$key}"><img border="0" src='lib/Galaxia/img/icons/trash.gif' alt='{tr}Delete{/tr}' title='{tr}Delete{/tr}' /></a>
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
	<td class="odd"><input type="submit" name="saveprops" value="{tr}Update{/tr}" /></td>
</tr>

</table>
</form>
<h2>{tr}Add property{/tr}</h2>
<form method="POST" action="tiki-g-admin_instance.php?iid={$iid}&aid={$aid}">
<input type="hidden" name="iid" value="{$iid|escape}" />
<table class="normal">
<tr>
	<td class="formcolor">{tr}Name{/tr}</td>
	<td class="formcolor"><input type="text" name="name" /></td>
</tr>
<tr>
	<td class="formcolor">{tr}Value{/tr}</td>
	<td class="formcolor"><textarea name="value" rows="4" cols="80"></textarea></td>
</tr>
<tr>
	<td class="formcolor">&nbsp;</td>
	<td class="formcolor"><input type="submit" name="addprop" value="{tr}Add{/tr}" /></td>
</tr>

</table>
</form>

