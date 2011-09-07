{* $Id$ *}
{if $tiki_p_tracker_dump eq "y" or $tiki_p_admin eq "y"}
	{tab name="{tr}Dump All Tracker Items{/tr}"}
	<h3>{tr}Tracker Items Dump{/tr}</h3>
	<div>
		<form action="{$smarty.server.PHP_SELF}" method="post">
			<table class="formcolor">
				<tr>
					<td width="20%"><label for="tracker">{tr}Tracker{/tr}</label></td>
					<td>
					<select name="trackerId" onchange="this.form.submit();" id="dumpTrackerId">
				      {foreach from=$trackers item=tracker}
				       <option value="{$tracker.trackerId}" title="{$tracker.description|escape}"{if $tracker.trackerId eq $trackerId} selected="selected"{/if}>
				           {$tracker.name|escape}
				       </option>
				      {/foreach}
				    </select>
				    {$recordsMax} 
					{if $recordsMax eq 1}
						{tr}item{/tr}
					{else}
						{tr}items{/tr}
					{/if}
					</td>
				</tr>
			</table>
		</form>
		<form action="tiki-export_tracker.php?trackerId={$trackerId}" method="post" id="dump_form">
			<table class="formcolor">
				<tr>
					<td width="20%">&nbsp;</td>
					<td>
						<input type="submit" name="dump_tracker" id="dump_tracker" value="{tr}Dump{/tr}" />
					</td>
				</tr>
			</table>
		</form>
	</div>
	{/tab}
{/if}
