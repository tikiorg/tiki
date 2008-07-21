{popup_init src="lib/overlib.js"}
{*Smarty template*}
<h1><a class="pagetitle" href="tiki-g-view_workitem.php?itemId={$smarty.request.itemId}">{tr}Browsing Workitem{/tr}</a>
</h1>
{include file=tiki-g-monitor_bar.tpl}
<h3>{tr}Workitem information{/tr}</h3>
<table class="normal">
<tr>
	<td  class="odd"><b>id</b></td>
	<td  class="odd">{$wi.itemId}</td>
</tr>
<tr>
	<td  class="odd"><b>#</b></td>
	<td  class="odd">{$wi.orderId}</td>
</tr>
<tr>
	<td class="odd"><b>Process</b></td>
	<td class="odd">{$wi.procname} {$wi.version}</td>
</tr>
<tr>
	<td class="odd"><b>Activity</b></td>
	<td class="odd">{$wi.type|act_icon:"$wi.isInteractive"} {$wi.name}</td>
</tr>
<tr>
	<td class="odd"><b>User</b></td>
	<td class="odd">{$wi.user}</td>
</tr>
<tr>
	<td class="odd"><b>Started</b></td>
	<td class="odd">{$wi.started|tiki_long_datetime}</td>
</tr>
<tr>
	<td class="odd"><b>Duration</b></td>
	<td class="odd">{$wi.duration|duration}</td>
</tr>


</table>
<h2>{tr}Properties{/tr}</h2>
<table class="normal">
<tr>
	<td  class="heading">{tr}Property{/tr}</td>
	<td  class="heading">{tr}Value{/tr}</td>
</tr>
{foreach from=$wi.properties item=item key=key}
<tr>
	<td class="odd">
	 <b>{$key}</b>
	 </td>
	<td class="odd">
	{$item}
	</td>
</tr>
{/foreach}
</table>
