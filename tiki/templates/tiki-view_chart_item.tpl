<a class="pagetitle" href="tiki-view_chart_item.php?itemId={$smarty.request.itemId}">Item information</a>
<br/><br/>
<table class="normal">
<tr>
	<td width="30%" class="formcolor">{tr}Chart{/tr}</td>
	<td class="formcolor">{$chart_info.title}</td>
</tr>
<tr>
	<td width="30%" class="formcolor">{tr}Item{/tr}</td>
	<td class="formcolor">{$info.title}</td>
</tr>
<tr>
	<td width="30%" class="formcolor">{tr}Desc{/tr}</td>
	<td class="formcolor">{$info.description}</td>
</tr>
<tr>
	<td width="30%" class="formcolor">{tr}Permanency{/tr}</td>
	<td class="formcolor">{$info.perm}</td>
</tr>
<tr>
	<td width="30%" class="formcolor">{tr}Position{/tr}</td>
	<td class="formcolor">{$info.position}</td>
</tr>
<tr>
	<td width="30%" class="formcolor">{tr}Previous{/tr}</td>
	<td class="formcolor">{$info.lastPosition}</td>
</tr>
<tr>
	<td width="30%" class="formcolor">{tr}Dif{/tr}</td>
	<td class="formcolor">{$info.dif}</td>
</tr>

<tr>
	<td width="30%" class="formcolor">{tr}Best Position{/tr}</td>
	<td class="formcolor">{$info.best}</td>
</tr>
{if ($chart_info.singleChartVotes eq 'n' or $user_voted_chart eq 'n')
	and
	($chart_info.singleItemVotes eq 'n' or $user_voted_item eq 'n')}
<tr>
	<td width="30%" class="formcolor">{tr}Vote tihis item{/tr}</td>
	<td class="formcolor">
	voting...
	</td>
</tr>
{/if}
</table>