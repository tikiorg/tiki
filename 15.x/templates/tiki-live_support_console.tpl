<!DOCTYPE html>

<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<link rel="StyleSheet" href="styles/{$prefs.style}" type="text/css">
		<title>{tr}Live support:Console{/tr}</title>
		{literal}
			<script type="text/javascript" src="lib/live_support/live-support.js">

			</script>
		{/literal}
		{$trl}
	</head>
	{literal}
		<body style="background-color: white">
	{/literal}
	{if $isOperator}
		<div class="table-responsive">
			<table class="table" >
				<tr>
					<th>{tr}Operator:{/tr} {$user}</th>
					<th>{tr}Status:{/tr} <b>{tr}{$status}{/tr}</b></th>
					<th style="text-align:right;">
						{if $status eq 'offline'}
							<a href="tiki-live_support_console.php?status=online">{tr}be online{/tr}</a>
						{else}
							<a href="tiki-live_support_console.php?status=offline">{tr}be offline{/tr}</a>
					{/if}
					</th>
				</tr>
			</table>
		</div>
	{else}
		{tr}You are not an operator.{/tr} <a href="tiki-live_support_admin.php">{tr}Live support system{/tr}</a>
	{/if}

	{if count($requests) > 0}
		<h3>{tr}Support requests{/tr}</h3>
		{if $new_requests eq 'y'}
			<script type='text/javascript'>
				sound();
			</script>
		{/if}
		<table id='reqs' class="table normal">
			<tr>
				<th>{tr}User{/tr}</th>
				<th>{tr}Reason{/tr}</th>
				<th>{tr}Requested{/tr}</th>
				<th>&nbsp;</th>
			</tr>

			{section loop=$requests name=ix}
				<tr>
					<td>{$requests[ix].user}</td>
					<td>{$requests[ix].reason}</td>
					<td>{$requests[ix].timestamp|tiki_short_time}</td>
					<td>
						{if $status eq 'online'}
							{assign var=thereqId value=$requests[ix].reqId}
							<a class="link" {jspopup href="tiki-live_support_chat_window.php?reqId=$thereqId&amp;role=operator" width="300" height="450"}>{tr}Accept{/tr}</a>
							<a class="link" {jspopup href="tiki-live_support_chat_window.php?reqId=$thereqId&amp;role=observer" width="300" height="450"}>{tr}Join{/tr}</a>
						{else}
							&nbsp;
						{/if}
					</td>
				</tr>
			{/section}
		</table>
	{else}
		<h3>{tr}No support requests{/tr}</h3>
	{/if}
	<script type='text/javascript'>
		var last_support_req={$last};
		console_poll();
	</script>

	</body>
</html>
