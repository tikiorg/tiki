{popup_init src="lib/overlib.js"}
{*Smarty template*}
<h1><a class="pagetitle" href="tiki-g-admin_instance.php?iid={$iid}">{tr}Admin instance{/tr}</a>
</h1>
{include file=tiki-g-monitor_bar.tpl}
<h3>{tr}Process:{/tr} {$proc_info.name} {$proc_info.version}<br />
{tr}Instance{/tr}: {$ins_info.name} <br />
{tr}Activity{/tr}: {$acts.name} {if $acts.actstatus eq 'running'}<a href="tiki-g-run_activity.php?iid={$iid}&amp;activityId={$aid}"><img border='0' title='{tr}run instance{/tr}' alt='{tr}run instance{/tr}' src='lib/Galaxia/img/icons/next.gif' /></a>{/if}</h3>
<form method="POST" action="tiki-g-admin_instance_activity.php?iid={$iid}&amp;aid={$aid}">
<input type="hidden" name="iid" value="{$iid|escape}" />
<table class="normal">
<tr>
	<td class="formcolor">{tr}Created{/tr}</td>
	<td class="formcolor">{$acts.iaStarted|date_format:"%b %e, %Y - %H:%M:%S"|capitalize}</td>
</tr>
<tr>
	<td class="formcolor">{tr}Expiration Date{/tr}</td>
	<td class="formcolor">{if $acts.exptime eq 0 && $acts.type eq 'activity' && $acts.isInteractive eq 'y'}{tr}Not Defined{/tr}{elseif $acts.type != 'activity'}&lt;{$acts.type}&gt;{elseif $acts.isInteractive eq 'n'}{tr}Not Interactive{/tr}{else}{$acts.exptime|date_format:"%b %e, %Y - %H:%M"|capitalize}{/if}</td>
</tr>
<tr>
	<td class="formcolor">{tr}Executed{/tr}</td>
	<td class="formcolor">{$acts.ended|date_format:"%b %e, %Y - %H:%M:%S"|capitalize}</td>
<tr>
	<td class="formcolor">{tr}Status{/tr}</td>
	<td class="formcolor">{tr}{$acts.actstatus}{/tr}</td>
</tr>
<tr>
	<td class="formcolor">{tr}Owner{/tr}</td>
	<td class="formcolor">
	{if $acts.actstatus eq 'running'}
		<select name="owner">
			{section name=ix loop=$users}
				<option value="{$users[ix].user|escape}" {if $users[ix].user eq $acts.user}selected="selected"{/if}>{$users[ix].user}</option>
			{/section}
		</select>
	{else}
		{$acts.user}
	{/if}
	</td>
</tr>
<tr>
	<td class="formcolor">&nbsp;</td>
	<td class="formcolor">	{if $acts.actstatus eq 'running'}<input type="submit" name="save" value="{tr}Update{/tr}" />{/if}</td>
</tr>
</table>
</form>

<h3>{tr}Comments{/tr}</h3>
{section name=ix loop=$comments}
<table class="email">
        <tr>
	    	<td class="heading">{tr}From{/tr}:</td>
		<td class="formcolor">{$comments[ix].user|capitalize:true}</td>
      	        <td class="closeButton">
		    <form method="POST" target="email" action="tiki-g-view_comment.php">
		    	  <input type="hidden" name="__user" value="{$comments[ix].user}" />
			  <input type="hidden" name="__title" value="{$comments[ix].title}" />
			  <input type="hidden" name="__comment" value="{$comments[ix].comment}" />
			  <input type="hidden" name="__timestamp" value="{$comments[ix].timestamp}" />
	      	    	  <input type="submit" name="view" title="{tr}Pop-up{/tr}" tiki-g-view_comment.php','email','HEIGHT=400,width=400,resizable=0,menubar=no,location=no,scrollbars=1')" value="&#164">
		    </form>
		</td>
		<td class="closeButton">    
	      	    <form method="POST" action="tiki-g-admin_instance_activity.php?iid={$iid}&aid={$aid}">
		    	  <input type="hidden" name="__removecomment" value="{$comments[ix].cId}" />
	      	    	  <input type="submit" name="eraser" value="X" title="{tr}erase{/tr}">
	      	    </form>
		</td>
	</tr>
	<tr>
	    	<td class="heading">{tr}Date{/tr}:</td>
		<td class="formcolor" colspan="3">{$comments[ix].timestamp|date_format:"%A %e de %B, %Y %H:%M:%S"|capitalize:true}</td>
	</tr>
	<tr>
		<td class="heading">{tr}Subject{/tr}:</td>
		<td class="formcolor" colspan="3">{$comments[ix].title}</td>
	</tr>
	<tr>
		{*<td class="body">{tr}Body{/tr}:</td>*}
		<td class="body" colspan="4">{$comments[ix].comment}</td>
	</tr>
</table>
{/section}
<form method="POST" action="tiki-g-admin_instance_activity.php?iid={$iid}&aid={$aid}">

<h3>{tr}Answer{/tr}:</h3>
<table class="normal">

	<tr>
	    	<td class="heading">{tr}Subject{/tr}:</td>
	    	<td class="formcolor"><input type="text" name="__title" /></td>
	</tr>
	<tr>
		<td class="heading">{tr}Body{/tr}:</td>
		<td class="formcolor"><textarea rows="5" cols="60" name="__comment"></textarea></td>
	</tr>
	<tr>
		<td class="formcolor"></td>
		<td class="formcolor"><input type="submit" name="answer" value="{tr}Save{/tr}"/></td>
	</tr>
</table>
<input type="hidden" name="__post" value="y" />
</form>
