{title}{tr}Rankings{/tr}{/title}

<form action="{$rpage}" method="post">
	<select name="which">
		{section name=ix loop=$allrankings}
			<option value="{$allrankings[ix].value|escape}" {if $which eq $allrankings[ix].value}selected="selected"{/if}>{$allrankings[ix].name}</option>
		{/section}
	</select>

	<select name="limit">
		<option value="10" {if $limit eq 10}selected="selected"{/if}>{tr}Top 10{/tr}</option>
		<option value="20" {if $limit eq 20}selected="selected"{/if}>{tr}Top 20{/tr}</option>
		<option value="50" {if $limit eq 50}selected="selected"{/if}>{tr}Top 50{/tr}</option>
		<option value="100" {if $limit eq 100}selected="selected"{/if}>{tr}Top 100{/tr}</option>
	</select>

	{if $categIdstr}<input type="hidden" name="categId" value="{$categIdstr}" />{/if}
	<input type="submit" name="selrank" value="{tr}View{/tr}" />
</form>

{cycle values="odd,even" print=false}
{section name=ix loop=$rankings}
	<table class="normal">
		<tr>
			<th>{tr}Rank{/tr}</th>
			<th>{$rankings[ix].title}</th>
			<th>{$rankings[ix].y}</th>
		</tr>
		{section name=xi loop=$rankings[ix].data}
			<tr class="{cycle}">
				<td>{$smarty.section.xi.index_next}</td>
				<td>
					<a class="link" href="{$rankings[ix].data[xi].href}">{if $rankings[ix].data[xi].name eq ""}-{else}{$rankings[ix].data[xi].name|escape}{/if}</a>
				</td>
				<td>
					{if $rankings[ix].type eq 'nb'}{$rankings[ix].data[xi].hits}{else}{$rankings[ix].data[xi].hits|tiki_long_datetime}{/if}
				</td>
			</tr>
		{sectionelse}
			{norecords _colspan=3}
		{/section}
	</table>
{/section}
