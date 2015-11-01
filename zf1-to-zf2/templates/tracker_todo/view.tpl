{extends 'layout_view.tpl'}

{block name="title"}
	{title}{$title|escape}{/title}
{/block}

{block name="content"}
<div class="table-responsive">
<table class="table">
	<tr>
		<th>{tr}From{/tr}</th>
		<th>{tr}To{/tr}</th>
		<th>{tr}Delay{/tr}</th>
		<th>{tr}After{/tr}</th>
		<th>{tr}Notification{/tr}</th>
		<th>{tr}Action{/tr}</th>
	</tr>
	{foreach from=$todos item=todo}
		<tr>
			<td>{$todo.from.status|escape}</td>
			<td>{$todo.to.status|escape}</td>
			<td>{$todo.after|duration|escape}</td>
			<td>{$todo.event|escape}</td>
			<td>
				{foreach from=$todo.notifs item=notif name=notif}
					{foreach from=$notif.to key=i item=j name=notif2}
						<div>
							{$i|escape}:
							{if $i eq 'before'}
								{$j|duration|escape}
							{else}
								{$j|escape}
							{/if}
						</div>
					{/foreach}
				{/foreach}
			</td>
			<td><a class="confirm-prompt tips" data-confirm="{tr}Do you really want to remove the scheduled event?{/tr}" href="{service controller=tracker_todo action=delete todoId=$todo.todoId trackerId=$trackerId}" title=":{tr}Remove event{/tr}">{icon name='delete'}</a></td>
		</tr>
	{foreachelse}
		<tr>
			<td colspan="6">{tr}No events registered{/tr}</td>
		</tr>
	{/foreach}
</table>
</div>
<form class="add-event no-ajax" method="post" action="{service controller=tracker_todo action=add trackerId=$trackerId}">
	<h4>{tr}New event{/tr}</h4>
	<div class="form-group">
		<label for="from">{tr}From{/tr}</label>
		<select name="from" class="form-control">
			{foreach key=st item=stdata from=$statusTypes}
				<option value="{$st|escape}">{$stdata.label|escape}</option>
			{/foreach}
		</select>
	</div>
	<div class="form-group">
		<label for="to">{tr}To{/tr}</label>
		<select name="to" class="form-control">
			{foreach key=st item=stdata from=$statusTypes}
				<option value="{$st|escape}">{$stdata.label|escape}</option>
			{/foreach}
		</select>
	</div>
	<div class="form-group">
		<label for="event">{tr}Reference date{/tr}</label>
		<select name="event" class="form-control">
			<option value="creation">{tr}After creation{/tr}</option>
			<option value="modification">{tr}After last modification{/tr}</option>
		</select>
	</div>
	<div class="form-group">
		<label>{tr}Delay{/tr}</label>
		<div class="form-control">
			{html_select_duration prefix='after'}
		</div>
	</div>
	<fieldset>
		<legend>{tr}Notification{/tr}</legend>

		<div class="form-group">
			<label>{tr}Delay prior to status change{/tr}</label>
			<div class="form-control">
				{html_select_duration prefix='notif'}
			</div>
		</div>
		<div class="form-group">
			<label for="subject">{tr}Mail subject text{/tr}</label>
			<input type="text" name="subject" class="form-control">
		</div>
		<div class="form-group">
			<label for="body">{tr}Mail body ressource{/tr}</label>
			<input type="text" name="body" class="form-control">
			<div class="help-block">
				{tr}wiki:pageName for a wiki page or tplName.tpl for a template{/tr}
			</div>
		</div>
	</fieldset>
	<div class="submit">
		<input type="submit" class="btn btn-primary" value="{tr}Create{/tr}">
	</div>
</form>
{jq}
$('.add-event').removeClass('add-event').submit(function () {
	var form = this;
	$.ajax({
		type: 'post',
		url: $(form).attr('action'),
		dataType: 'json',
		data: $(form).serialize(),
		success: function () {
			$(form).parent().loadService({
				controller: 'tracker_todo',
				action: 'view',
				trackerId: {{$trackerId}}
			}, {});
		}
	});
	return false;
});
{/jq}
{/block}
