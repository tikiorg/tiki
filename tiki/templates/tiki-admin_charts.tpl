{popup_init src="lib/overlib.js"}
{*Smarty template*}
<a class="pagetitle" href="tiki-admin_charts.php">{tr}Admin charts{/tr}</a>
<br/><br/>
<h3>{tr}Add or edit a chart{/tr} <a class="link" href="tiki-admin_charts.php?where={$where}&amp;offset={$offset}&amp;sort_mode={$sort_mode}&amp;chartId=0">new</a>
</h3>
<form action="tiki-admin_charts.php" method="post">
<input type="hidden" name="chartId" value="{$info.chartId}" />
<input type="hidden" name="offset" value="{$offset}" />
<input type="hidden" name="where" value="{$where}" />
<input type="hidden" name="find" value="{$find}" />
<input type="hidden" name="sort_mode" value="{$sort_mode}" />
<table class="normal">
	<tr>
		<td class="formcolor">{tr}Title{/tr}</td>
		<td class="formcolor"><input type="text" maxlength="250" name="title" value="{$info.title}" /></td>
	</tr>
	
	<tr>
		<td class="formcolor">{tr}Description{/tr}</td>
		<td class="formcolor"><textarea rows="4" cols="40" name="description">{$info.description}</textarea></td>
	</tr>
	<tr>
		<td class="formcolor">{tr}Active{/tr}</td>
		<td class="formcolor"><input type="checkbox" name="isActive" {if $info.isActive eq 'y'}checked="checked"{/if} /></td>
	</tr>
	<tr>
		<td class="formcolor">{tr}Users can vote only one item from this chart per period{/tr}</td>
		<td class="formcolor"><input type="checkbox" name="singleChartVotes" {if $info.singleChartVotes eq 'y'}checked="checked"{/if} /></td>
	</tr>
	<tr>
		<td class="formcolor">{tr}Prevent users from voting same item more than one time{/tr}</td>
		<td class="formcolor"><input type="checkbox" name="singleItemVotes" {if $info.singleItemVotes eq 'y'}checked="checked"{/if} /></td>
	</tr>	

	<tr>
		<td class="formcolor">{tr}Users can suggest new items{/tr}</td>
		<td class="formcolor"><input type="checkbox" name="suggestions" {if $info.suggestions eq 'y'}checked="checked"{/if} /></td>
	</tr>
	<tr>
		<td class="formcolor">{tr}Auto validate user suggestions{/tr}</td>
		<td class="formcolor"><input type="checkbox" name="autoValidate" {if $info.autoValidate eq 'y'}checked="checked"{/if}</td>
	</tr>
	
	<tr>
		<td class="formcolor">{tr}Ranking shows{/tr}</td>
		<td class="formcolor">
			<select name="topN">
			    <option value='0' {if $info.topN eq 0}selected="selected"{/if}>{tr}All items{/tr}</option>
				<option value='10' {if $info.topN eq 10}selected="selected"{/if}>{tr}Top 10 items{/tr}</option>
				<option value='20' {if $info.topN eq 20}selected="selected"{/if}>{tr}Top 20 items{/tr}</option>
				<option value='40' {if $info.topN eq 40}selected="selected"{/if}>{tr}Top 40 items{/tr}</option>
				<option value='50' {if $info.topN eq 50}selected="selected"{/if}>{tr}Top 50 items{/tr}</option>
				<option value='100' {if $info.topN eq 100}selected="selected"{/if}>{tr}Top 100 items{/tr}</option>
				<option value='250' {if $info.topN eq 250}selected="selected"{/if}>{tr}Top 250 items{/tr}</option>
			</select>
		</td>
	</tr>
	
	<tr>
		<td class="formcolor">{tr}Voting system{/tr}</td>
		<td class="formcolor">
			<select name="maxVoteValue">
				<option value="1" {if $info.maxVoteValue eq 1}selected="selected"{/if}>{tr}Vote items{/tr}</option>
				<option value="5" {if $info.maxVoteValue eq 5}selected="selected"{/if}>{tr}Rank 1..5{/tr}</option>
				<option value="10" {if $info.maxVoteValue eq 10}selected="selected"{/if}>{tr}Rank 1..10{/tr}</option>
			</select>
		</td>
	</tr>
	<tr>
		<td class="formcolor">{tr}Ranking frequency{/tr}</td>
		<td class="formcolor">
			<select name="frequency">
				<option value="0" {if $info.frequency eq "0"}selected="selected"{/if}>{tr}Realtime{/tr}</option>
				<option value="1" {if $info.frequency eq "1"}selected="selected"{/if}>{tr}Daily{/tr}</option>
				<option value="7" {if $info.frequency eq "7"}selected="selected"{/if}>{tr}Weekly{/tr}</option>
				<option value="30" {if $info.frequency eq "30"}selected="selected"{/if}>{tr}Monthly{/tr}</option>
			</select>
		</td>
	</tr>	
	<tr>
		<td class="formcolor">{tr}Show Average{/tr}</td>
		<td class="formcolor"><input type="checkbox" name="showAverage" {if $info.showAverage eq 'y'}checked="checked"{/if} /></td>
	</tr>
	<tr>
		<td class="formcolor">{tr}Show Votes{/tr}</td>
		<td class="formcolor"><input type="checkbox" name="showVotes" {if $info.showVotes eq 'y'}checked="checked"{/if} /></td>
	</tr>
	<tr>
		<td class="formcolor">{tr}Use Cookies for unregistered users{/tr}</td>
		<td class="formcolor"><input type="checkbox" name="useCookies" {if $info.useCookies eq 'y'}checked="checked"{/if} /></td>
	</tr>
	<tr>
		<td class="formcolor">{tr}Users can vote again after{/tr}</td>
		<td class="formcolor">
			<select name="voteAgainAfter">
				<option value="0" {if $info.frequency eq "0"}selected="selected"{/if}>{tr}Anytime{/tr}</option>
				<option value="1" {if $info.frequency eq "1"}selected="selected"{/if}>{tr}1 day{/tr}</option>
				<option value="7" {if $info.frequency eq "7"}selected="selected"{/if}>{tr}1 week{/tr}</option>
				<option value="30" {if $info.frequency eq "30"}selected="selected"{/if}>{tr}1 month{/tr}</option>			
			</select>
		</td>
	</tr>	

	<tr>
		<td class="formcolor">&nbsp;</td>
		<td class="formcolor"><input type="submit" name="save" value="{if $pid > 0}{tr}update{/tr}{else}{tr}create{/tr}{/if}" /></td>
	</tr>
</table>
</form>


<h3>{tr}Charts{/tr}</h3>
<form action="tiki-admin_charts.php" method="post">
<input type="hidden" name="offset" value="{$offset}" />
<input type="hidden" name="sort_mode" value="{$sort_mode}" />
{tr}Find{/tr}:<input size="8" type="text" name="find" value="{$find}" />
</form>
<form action="tiki-admin_charts.php" method="post">
<input type="hidden" name="offset" value="{$offset}" />
<input type="hidden" name="find" value="{$find}" />
<input type="hidden" name="where" value="{$where}" />
<input type="hidden" name="sort_mode" value="{$sort_mode}" />
<table class="normal">
<tr>
<td width="7%" class="heading"><input type="submit" name="delete" value="{tr}x{/tr} " /></td>
<td class="heading" ><a class="tableheading" href="{if $sort_mode eq 'title_desc'}{sameurl sort_mode="title_asc"}{else}{sameurl sort_mode="title_desc"}{/if}">{tr}Title{/tr}</a></td>
</tr>
{cycle values="odd,even" print=false}
{section name=ix loop=$items}
<tr>
	<td class="{cycle advance=false}">
		<input type="checkbox" name="chart[{$items[ix].chartId}]" />
	</td>
	<td class="{cycle advance=false}">
		<a class="link" href="{sameurl chartId=$items[ix].chartId}">{$items[ix].title}</a>
	</td>
</tr>
{sectionelse}
<tr>
	<td class="{cycle advance=false}" colspan="5">
	{tr}No charts defined yet{/tr}
	</td>
</tr>	
{/section}
</table>
</form>

<div class="mini">
<div align="center">
{if $prev_offset >= 0}
[<a class="prevnext" href="{sameurl offset=$prev_offset}">{tr}prev{/tr}</a>]&nbsp;
{/if}
{tr}Page{/tr}: {$actual_page}/{$cant_pages}
{if $next_offset >= 0}
&nbsp;[<a class="prevnext" href="{sameurl offset=$next_offset}">{tr}next{/tr}</a>]
{/if}
{if $direct_pagination eq 'y'}
<br/>
{section loop=$cant_pages name=foo}
{assign var=selector_offset value=$smarty.section.foo.index|times:$maxRecords}
<a class="prevnext" href="{sameurl offset=$selector_offset}">
{$smarty.section.foo.index_next}</a>&nbsp;
{/section}
{/if}
</div>
</div> 
 

