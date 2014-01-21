{* $Id: wikiplugin_sheet.tpl 35178 2011-07-02 11:44:28Z gta74 $ *}
<div id="timesheet" class="ui-widget-content ui-corner-all" style="display: table; text-align: center; padding-bottom: 15px;">
	<div class="ui-widget-header ui-corner-top">{tr}Mini TimeSheet{/tr} {button href="tiki-timesheet.php" _text="{tr}Full View{/tr}"}</div>
	<div id="jtrack-holder" style="margin: 5px;text-align: left;">
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
					<a href="#" class="jtrack-cancel" rel="jtrack-form-create">{tr}Cancel{/tr}</a>
				</p>
				<p style="display: none" id="jtrack-create-status"></p>
			</div>
			<div id="jtrack-form-update" class="jtrack-form" style="display: none">
			<p>
					<label for="jtrack-task-name">{tr}Task name{/tr}</label>
					<input type="text" name="jtrack-task-name" id="jtrack-task-name" class="jtrack-text">
				</p>
				<p>
					<label for="jtrack-task-estimate">{tr}Estimate (in min.){/tr}</label>
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