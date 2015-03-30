{extends 'layout_view.tpl'}

{block name="title"}
	{title}{$title|escape}{/title}
{/block}

{block name="content"}
<form class="simple" method="post" action="{service controller=managestream action=record}">
	<label>
		{tr}Event{/tr}
		<select name="event">
			{foreach from=$eventTypes item=eventName}
				<option value="{$eventName|escape}"{if $rule.eventType eq $eventName} selected{/if}>{$eventName|escape}</option>
			{/foreach}
		</select>
	</label>
    {if $prefs.activity_notifications eq 'y'}
        <label>
            <input id="notification_checkbox" name="is_notification" type="checkbox"> Is an Activity Notification
        </label>
        <div class="panel panel-default priority-div hidden">
            <div class="panel-heading">
                Activity Notification
            </div>
            <div class="panel-body">
                <div class="form-group">
                    <label for="priorityinput" class="control-label">{tr}Priority{/tr}</label>
                    <select id="priorityinput" name="priority" class="form-control">
                        <option value="low">Low</option>
                        <option value="high">High</option>
                        <option value="critical">Critical</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="priorityinput" class="control-label">{tr}Notification Recipient{/tr}</label>
                    <input id="userInput" name="user" class="form-control" value="user">
                </div>
            </div>
        </div>
    {/if}
	<label>
		{tr}Notes{/tr}
		<textarea name="notes">{$rule.notes|escape}</textarea>
	</label>
	<div class="submit">
		<input type="hidden" name="ruleId" value="{$rule.ruleId|escape}"/>
		<input type="submit" class="btn btn-default btn-sm" value="{tr}Record Event{/tr}"/>
	</div>
</form>

    {jq}
        $("#notification_checkbox").change(function(){
        if (this.checked){
        $(".priority-div").removeClass("hidden");
        }else{
        $(".priority-div").addClass("hidden");
        }
        });
    {/jq}
{/block}
