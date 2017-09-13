<html class="mail">
	<head>
		<base href="{$base_url}">
	</head>
	<body class="notification" style="padding: 1.5rem">
		<div class="container">
			<div class="row">
				<div class="col-md-12">

					{activity info=$monitor}

					<div class="well well-sm">
						<p>
						<small>{tr}You receive this notification because you requested it.{/tr} {tr}If you did not request these emails or did so in an error, please use the following link:{/tr}</small>
						</p>
						<a class="btn btn-default btn-sm" href="{service controller=monitor action=object type=$monitor.type object=$monitor.object}">{tr}Manage your notifications{/tr}</a>
					</div>
				</div>
			</div>
		</div>
	</body>
</html>
