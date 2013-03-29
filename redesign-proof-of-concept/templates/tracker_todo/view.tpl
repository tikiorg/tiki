<h4>{tr}Events{/tr}</h4>
<table class="normal">
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
			<td><a class="confirm-prompt" data-confirm="{tr}Do you really want to remove the scheduled event?{/tr}" href="{service controller=tracker_todo action=delete todoId=$todo.todoId trackerId=$trackerId}">{icon _id=cross}</a></td>
		</tr>
	{foreachelse}
		<tr>
			<td colspan="6">{tr}No events registered{/tr}</td>
		</tr>
	{/foreach}
</table>
<form class="simple add-event no-ajax" method="post" action="{service controller=tracker_todo action=add trackerId=$trackerId}">
	<h4>{tr}New event{/tr}</h4>
	<label>
		{tr}From{/tr}
		<select name="from">
			{foreach key=st item=stdata from=$statusTypes}
				<option value="{$st|escape}">{$stdata.label|escape}</option>
			{/foreach}
		</select>
	</label>
	<label>
		{tr}To{/tr}
		<select name="to">
			{foreach key=st item=stdata from=$statusTypes}
				<option value="{$st|escape}">{$stdata.label|escape}</option>
			{/foreach}
		</select>
	</label>
	<label>
		{tr}Reference date{/tr}
		<select name="event">
			<option value="creation">{tr}After creation{/tr}</option>
			<option value="modification">{tr}After last modification{/tr}</option>
		</select>
	</label>
	<label>
		{tr}Delay{/tr}
		{html_select_duration prefix='after'}
	</label>
	<fieldset>
		<legend>{tr}Notification{/tr}</legend>

		<label>
			{tr}Delay prior to status change{/tr}
			{html_select_duration prefix='notif'}
		</label>
		<label>
			{tr}Mail subject text{/tr}
			<input type="text" name="subject">
		</label>
		<label>
			{tr}Mail body ressource{/tr}
			<input type="text" name="body">
			<div class="description">
				{tr}wiki:pageName for a wiki page or tplName.tpl for a template{/tr}
			</div>
		</label>
	</fieldset>
	<div class="submit">
		<input type="submit" value="{tr}Create{/tr}">
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
