{title help="Banners"}{tr}Banner stats{/tr}{/title}

<div class="t_navbar btn-group form-group">
	{button href="tiki-list_banners.php" class="btn btn-default" _text="{tr}List banners{/tr}"}
	{if $tiki_p_admin_banners eq 'y'}
		{button href="tiki-edit_banner.php?bannerId=$bannerId" class="btn btn-default" _text="{tr}Edit{/tr}"}
		{button href="tiki-edit_banner.php" class="btn btn-default" _text="{tr}Create new banner{/tr}"}
	{/if}
</div>

<h2>{tr}Banner Information{/tr}</h2>

<div class="panel panel-default">
	<div class="panel-body">
		<table>
			<tr>
				<td>{tr}Client:{/tr}</td>
				<td>{$client}</td>
			</tr>
			<tr>
				<td>{tr}URL:{/tr}</td>
				<td>{$url}</td>
			</tr>
			<tr>
				<td>{tr}Zone:{/tr}</td>
				<td>{$zone}</td>
			</tr>
			<tr>
				<td>{tr}Created:{/tr}</td>
				<td>{$created|tiki_short_date}</td>
			</tr>
			<tr>
				<td>{tr}Max Impressions:{/tr}</td>
				<td>{$maxImpressions}</td>
			</tr>
			<tr>
				<td>{tr}Impressions:{/tr}</td>
				<td>{$impressions}</td>
			</tr>
			<tr>
				<td>{tr}Max Clicks:{/tr}</td>
				<td>{$maxClicks}</td>
			</tr>

			<tr>
				<td>{tr}Clicks:{/tr}</td>
				<td>{$clicks}</td>
			</tr>
			<tr>
				<td>{tr}Click ratio:{/tr}</td>
				<td>{$ctr|@truncate:'7':"%":"true"}</td>
			</tr>
			<tr>
				<td>{tr}Method:{/tr}</td>
				<td>{$use}</td>
			</tr>
			{if $useDates eq 'y'}
			<tr>
				<td>{tr}Use dates:{/tr}</td>
				<td>{tr}From:{/tr} {$fromDate|tiki_short_date} {tr}to:{/tr} {$toDate|tiki_short_date}
				</td>
			</tr>
			{/if}
			<tr>
				<td>{tr}Hours:{/tr}</td>
				<td>{tr}From:{/tr} {$fromTime_h}:{$fromTime_m} {tr}to:{/tr} {$toTime_h}:{$toTime_m}</td>
			</tr>
			<tr>
				<td>{tr}Weekdays:{/tr}</td>
				<td>
					{if $Dmon eq 'y'} {tr}mon{/tr} {/if}
					{if $Dtue eq 'y'} {tr}tue{/tr} {/if}
					{if $Dwed eq 'y'} {tr}wed{/tr} {/if}
					{if $Dthu eq 'y'} {tr}thu{/tr} {/if}
					{if $Dfri eq 'y'} {tr}fri{/tr} {/if}
					{if $Dsat eq 'y'} {tr}sat{/tr} {/if}
					{if $Dsun eq 'y'} {tr}sun{/tr} {/if}
				</td>
			</tr>
		</table>
	</div>
</div>

<h2>{tr}Banner raw data{/tr}</h2>
<div class="panel panel-default">
	<div class="panel-body">
		<div align="center">
			{$raw}
		</div>
	</div>
</div>
