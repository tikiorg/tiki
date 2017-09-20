{* $Id$ *}<!DOCTYPE html>
<html lang="{if !empty($pageLang)}{$pageLang}{else}{$prefs.language}{/if}">
<head>
	{include file='header.tpl'}
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body{html_body_attributes}>
<div class="container">
	<div class="page-header" id="page-header">
		{modulelist zone=top class='row top_modules'}
		<div class="topbar row" id="topbar">
			{modulelist zone=topbar}
		</div>
	</div>
	<div class="row row-middle" id="row-middle">
		{if zone_is_empty('left') and zone_is_empty('right')}
			<div class="col-md-12 col1" id="col1" style="padding-top: 15px;">
				<div class="panel panel-danger">
					<div class="panel-heading">
						{icon name='error' alt="{tr}Error{/tr}" style="vertical-align:middle"} {$errortitle|default:"{tr}Error{/tr}"}
					</div>
					<div class="panel-body">
						<p>{$msg}</p>
						<form action="{$self}{if $query}?{$query|escape}{/if}" method="post" class="margin-bottom-sm">
							{foreach key=k item=i from=$post}
								<input type="hidden" name="{$k}" value="{$i|escape}">
							{/foreach}
							<input type="submit" class="btn btn-success" name="ticket_action_button"
								value="{tr}Confirm action{/tr}">
						</form>
					</div>
				</div>
			</div>
		{elseif zone_is_empty('left')}
			<div class="col-md-9 col1" id="col1" style="padding-top: 15px;">
				<div class="panel panel-danger">
					<div class="panel-heading">
						{icon name='error' alt="{tr}Error{/tr}" style="vertical-align:middle"} {$errortitle|default:"{tr}Error{/tr}"}
					</div>
					<div class="panel-body">
						<p>{$msg}</p>
						<form action="{$self}{if $query}?{$query|escape}{/if}" method="post" class="margin-bottom-sm">
							{foreach key=k item=i from=$post}
								<input type="hidden" name="{$k}" value="{$i|escape}">
							{/foreach}
							<input type="submit" class="btn btn-success" name="ticket_action_button"
								value="{tr}Confirm action{/tr}">
						</form>
					</div>
				</div>
			</div>
			<div class="col-md-3" id="col3">
				{modulelist zone=right}
			</div>
		{elseif zone_is_empty('right')}
			<div class="col-md-9 col-md-push-3 col1" id="col1" style="padding-top: 15px;">
				<div class="panel panel-danger">
					<div class="panel-heading">
						{icon name='error' alt="{tr}Error{/tr}" style="vertical-align:middle"} {$errortitle|default:"{tr}Error{/tr}"}
					</div>
					<div class="panel-body">
						<p>{$msg}</p>
						<form action="{$self}{if $query}?{$query|escape}{/if}" method="post" class="margin-bottom-sm">
							{foreach key=k item=i from=$post}
								<input type="hidden" name="{$k}" value="{$i|escape}">
							{/foreach}
							<input type="submit" class="btn btn-success" name="ticket_action_button"
								value="{tr}Confirm action{/tr}">
						</form>
					</div>
				</div>
			</div>
			<div class="col-md-3 col-md-pull-9" id="col2">
				{modulelist zone=left}
			</div>
		{else}
			<div class="col-md-8 col-md-push-2 col1" id="col1" style="padding-top: 15px;">
				<div class="panel panel-danger">
					<div class="panel-heading">
						{icon name='error' alt="{tr}Error{/tr}" style="vertical-align:middle"} {$errortitle|default:"{tr}Error{/tr}"}
					</div>
					<div class="panel-body">
						<p>{$msg}</p>
						<form action="{$self}{if $query}?{$query|escape}{/if}" method="post" class="margin-bottom-sm">
							{foreach key=k item=i from=$post}
								<input type="hidden" name="{$k}" value="{$i|escape}">
							{/foreach}
							<input type="submit" class="btn btn-success" name="ticket_action_button"
								value="{tr}Confirm action{/tr}">
						</form>
					</div>
				</div>
			</div>
			<div class="col-md-2 col-md-pull-8" id="col2">
				{modulelist zone=left}
			</div>
			<div class="col-md-2" id="col3">
				{modulelist zone=right}
			</div>
		{/if}
	</div>
	<footer class="footer" id="footer">
		<div class="footer_liner">
			{modulelist zone=bottom class='row row-sidemargins-zero'}
		</div>
	</footer>
</div>
{include file='footer.tpl'}

</body>
</html>
