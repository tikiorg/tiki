<html>
	<head>
		<base href="{$base_url}">
	</head>
	<body>
	<div class="container">
		{activity info=$monitor format=extended}

		<div class="well">
			<p>
				{tr}You receive this notification because you requested it.{/tr}
				<a href="{service controller=monitor action=object type=$monitor.type object=$monitor.object}">{tr}Manage your notifications{/tr}</a>
			</p>
		</div>
	</div>
	</body>
</html>
