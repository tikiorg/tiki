{title admpage="timesheets" help="Timesheets"}{tr}Timesheets{/tr}{/title}
{if $timeSheetProfileLoaded neq true AND $tiki_p_admin eq 'y'}
{tr}Apply the following profile for enhancements: {/tr}
{button href="tiki-admin.php" profile="time_sheet" page="profiles" list="List" _text="Time_Sheet"}
{/if}

<div id="timeSheetTabs">
	<ul>
		<li><a href="#tab1">{tr}Time Tracker{/tr}</a></li>
		<li><a href="#tab2">{tr}Local Cache (Not Committed){/tr}</a></li>
		{if $timeSheetProfileLoaded eq true}
			<li><a href="#tab3">{tr}Saved (Committed){/tr}</a></li>
		{/if}
	</ul>
	<div id="tab1">
		<div id="jtrack-holder" style="margin: 0px;">
			<div id="jtrack-bar">
				<a href="#" class="jtrack-create">{tr}New task{/tr}</a> |
				<a href="#" class="jtrack-remove-all">{tr}Delete all{/tr}</a> |
				<input type="checkbox" name="jtrack-show-completed" id="jtrack-show-completed" value="1" title="{tr}Toggle completed{/tr}"> C
				<input type="checkbox" name="jtrack-show-archived" id="jtrack-show-archived" value="1" title="{tr}Toggle archived{/tr}"> A
			</div>
			<div id="jtrack-content">
				<div id="jtrack-form-list" class="jtrack-form"></div>
				<div id="jtrack-form-create" class="jtrack-form" style="display: none">
					<p>
						<label for="jtrack-task-name">{tr}Task name{/tr}</label>
						<input type="text" name="jtrack-task-name" id="jtrack-task-name" class="jtrack-text">
					</p>
					<p>
						<label for="jtrack-task-estimate">{tr}Estimate (in min.){/tr}</label>
						<input type="text" name="jtrack-task-estimate" id="jtrack-task-estimate" class="jtrack-text">
					</p>
					<p>
						<input type="button" id="jtrack-button-create" value="{tr}Save{/tr}">
						<a href="#" class="jtrack-cancel" rel="jtrack-form-create">Cancel</a>
					</p>
					<p style="display: none" id="jtrack-create-status"></p>
				</div>
				<div id="jtrack-form-update" class="jtrack-form" style="display: none">
				<p>
						<label for="jtrack-task-name">{tr}Task name{/tr}</label>
						<input type="text" name="jtrack-task-name" id="jtrack-task-name" class="jtrack-text">
					</p>
					<p>
						<label for="jtrack-task-estimate">{tr}Estimate{/tr}</label>
						<input type="text" name="jtrack-task-estimate" id="jtrack-task-estimate" class="jtrack-text">
					</p>
					<p>
						<input type="checkbox" name="jtrack-task-completed" id="jtrack-task-completed"> {tr}Task is completed{/tr}<br>
						<input type="checkbox" name="jtrack-task-archived" id="jtrack-task-archived"> {tr}Task is archived{/tr}
					</p>
					<p>
						<label>{tr}Created at{/tr}</label>
						<span id="created"></span>
					</p>
					<p>
						<input type="button" id="jtrack-button-update" value="{tr}Update{/tr}">
						<a href="#" class="jtrack-cancel" rel="jtrack-form-update">{tr}Cancel{/tr}</a>
					</p>
					<p style="display: none" id="jtrack-update-status"></p>
				</div>
				<div id="jtrack-form-remove" class="jtrack-form" style="display: none">
					<p id="jtrack-remove-confirm"></p>
					<p>
						<input type="button" id="jtrack-button-remove" value="{tr}Delete{/tr}">
						<a href="#" class="jtrack-cancel" rel="jtrack-form-remove">{tr}Cancel{/tr}</a>
					</p>
				</div>
				<div id="jtrack-form-remove-all" class="jtrack-form" style="display: none">
					<p>{tr}Are you sure you want to delete all tasks?{/tr}</p>
					<p>
						<input type="button" id="jtrack-button-remove-all" value="{tr}Delete all{/tr}">
						<a href="#" class="jtrack-cancel" rel="jtrack-form-remove-all">{tr}Cancel{/tr}</a>
					</p>
				</div>
			</div>
		</div>
	</div>
	<div id="tab2" style="padding: 0px;">
		{if $timeSheetProfileLoaded eq true}
			{button _text="{tr}Commit Time Sheet Items{/tr}" _id="timeSheetCommit"}
		{/if}
		<div>
			<div id="timesheetSpreadsheet"></div>
		</div>
	</div>
	{if $timeSheetProfileLoaded eq true}
		<div id="tab3" style="padding: 0px;">
			{if $all eq false}
				{button _text="{tr}View All{/tr}" href="tiki-timesheet.php" all="true"}
			{else}
				{button _text="{tr}View My Items{/tr}" href="tiki-timesheet.php"}
			{/if}

			{assign var=timeSheetI value=1}
			{assign var=amountOfTimeSpent value="Amount of time spent"}
			{assign var=doneBy value="Done by"}

			<div id="timeSheetSaved">
				<table title="{tr}Saved (Committed){/tr}">
					<tr>
						<td>{tr}Summary{/tr}</td>
						<td>{tr}Time Spent{/tr}</td>
						<td>{tr}Description{/tr}</td>
						<td>{tr}Done By{/tr}</td>
						<td></td>
					</tr>
					{foreach from=$timeSheet item=item}
						<tr i="{$timeSheetI++}">
							<td>{$item.Summary}</td>
							<td>{$item.$amountOfTimeSpent}</td>
							<td>{$item.Description}</td>
							<td>{$item.$doneBy}</td>
							<td></td>
						</tr>
					{/foreach}
					<tr>
						<td></td>
						<td formula="SUM(B2:B{$timeSheetI})"></td>
						<td></td>
						<td></td>
						<td></td>
					</tr>
					<tr>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
					</tr>
					<tr>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
					</tr>
					<tr>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
					</tr>
				</table>
			</div>
		</div>
	{/if}
</div>
