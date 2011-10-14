<table id="timeSheetUnsaved" style="width: inherit; width: 100%;">
	<tr>
		<td>
			<div id="jtrack-holder" style="margin: 0px;">
				<div id="jtrack-bar">
					<a href="#" class="jtrack-create">New task</a> |
					<a href="#" class="jtrack-remove-all">Delete all</a> |
					<input type="checkbox" name="jtrack-show-completed" id="jtrack-show-completed" value="1" title="Toggle completed" /> C
					<input type="checkbox" name="jtrack-show-archived" id="jtrack-show-archived" value="1" title="Toggle archived" /> A
				</div>
				<div id="jtrack-content">
					<div id="jtrack-form-list" class="jtrack-form"></div>
					<div id="jtrack-form-create" class="jtrack-form" style="display: none">
						<p>
							<label for="jtrack-task-name">Task name</label>
							<input type="text" name="jtrack-task-name" id="jtrack-task-name" class="jtrack-text" />
						</p>
						<p>
							<label for="jtrack-task-estimate">Estimate</label>
							<input type="text" name="jtrack-task-estimate" id="jtrack-task-estimate" class="jtrack-text" />
						</p>
						<p>
							<input type="button" id="jtrack-button-create" value="Save" />
							<a href="#" class="jtrack-cancel" rel="jtrack-form-create">Cancel</a>
						</p>
						<p style="display: none" id="jtrack-create-status"></p>
					</div>
					<div id="jtrack-form-update" class="jtrack-form" style="display: none">
					<p>
							<label for="jtrack-task-name">Task name</label>
							<input type="text" name="jtrack-task-name" id="jtrack-task-name" class="jtrack-text" />
						</p>
						<p>
							<label for="jtrack-task-estimate">Estimate</label>
							<input type="text" name="jtrack-task-estimate" id="jtrack-task-estimate" class="jtrack-text" />
						</p>
						<p>
							<input type="checkbox" name="jtrack-task-completed" id="jtrack-task-completed" /> Task is completed<br />
							<input type="checkbox" name="jtrack-task-archived" id="jtrack-task-archived" /> Task is archived
						</p>
						<p>
							<label>Created at</label>
							<span id="created"></span>
						</p>
						<p>
							<input type="button" id="jtrack-button-update" value="Update" />
							<a href="#" class="jtrack-cancel" rel="jtrack-form-update">Cancel</a>
						</p>
						<p style="display: none" id="jtrack-update-status"></p>
					</div>
					<div id="jtrack-form-remove" class="jtrack-form" style="display: none">
						<p id="jtrack-remove-confirm"></p>
						<p>
							<input type="button" id="jtrack-button-remove" value="Delete" />
							<a href="#" class="jtrack-cancel" rel="jtrack-form-remove">Cancel</a>
						</p>
					</div>
					<div id="jtrack-form-remove-all" class="jtrack-form" style="display: none">
						<p>Are you sure you want to delete all tasks?</p>
						<p>
							<input type="button" id="jtrack-button-remove-all" value="Delete all" />
							<a href="#" class="jtrack-cancel" rel="jtrack-form-remove-all">Cancel</a>
						</p>
					</div>
				</div>
			</div>
		</td>
		<td>
			<div id="timesheetSpreadsheet"></div>
		</td>
	</tr>
</table>

{if $timeSheetProfileLoaded eq true}
	{if $all eq false}
		{button _text="{tr}View All{/tr}" href="tiki-timesheet.php" all="true"}
	{else}
		{button _text="{tr}View My Items{/tr}" href="tiki-timesheet.php"}
	{/if}
	
	{button _text="{tr}Commit Time Sheet Items{/tr}" _id="timeSheetCommit"}
	
	{assign var=timeSheetI value=1}
	{assign var=amountOfTimeSpent value="Amount of time spent"}
	
	<div id="timeSheetSaved">
		<table title="{tr}Saved (Committed){/tr}">
			<tr>
				<td>{tr}Summary{/tr}</td>
				<td>{tr}Time Spent{/tr}</td>
				<td>{tr}Description{/tr}</td>
				<td></td>
				<td></td>
			</tr>
			{foreach from=$timeSheet item=item}
				{$timeSheetI++}
				<tr>
					<td>{$item.Summary}</td>
					<td>{$item.$amountOfTimeSpent}</td>
					<td>{$item.Description}</td>
					<td></td>
					<td></td>
				</tr>
			{/foreach}
			<tr>
				<td></td>
				<td formula="=SUM(B2:B{$timeSheetI})"></td>
				<td></td>
				<td></td>
				<td></td>
			</tr>
		</table>
	</div>
{/if}