<!DOCTYPE html>
<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title>{tr}System error.{/tr}</title>
	<link rel="stylesheet" href="vendor/twitter/bootstrap/dist/css/bootstrap.css" type="text/css" />
	<link rel="stylesheet" href="vendor/fortawesome/font-awesome/css/font-awesome.min.css" type="text/css" />

	<style type="text/css" media="screen">
		html {ldelim}
			background-color: #fff;
		{rdelim}
		#centercolumn {ldelim}
			padding: 4em 10em;
		{rdelim}
	</style>
</head>
<body class="tiki_wiki db_error">
	<div id="fixedwidth"> {* enables fixed-width layouts *}
		<div id="main">
		<div id="siteheader" style="margin: 1em auto; max-width: 800px">
				<div class="sitelogo">
					<img style="border: medium none ;" alt="Site Logo" src="img/tiki/Tiki_WCG.png">
				</div>
		</div>

		<div id="tiki-main">
			<div id="tiki-mid">
				<div style="margin:10px 30px;">
					{if $prefs.error_reporting_level and ( $tiki_p_admin eq 'y' or $prefs.error_reporting_adminonly ne 'y' )}
						<h1>{tr}System error.{/tr}</h1>
						<div class="text-left">
							<p>{tr}The following error message was returned:{/tr}</p>
							<strong>
								<pre>{$msg|escape|nl2br}</pre>
							</strong>

							{if $requires_update}
								<p>
									{tr}Database is not currently up to date! Visit <a href="tiki-install.php">Tiki Installer</a> to resolve this issue.{/tr}
									{tr}If you have shell (SSH) access, you can also use the following, on the command line, from the root of your Tiki installation:{/tr}
									<kbd>php console.php{if not empty($tikidomain)} --site={$tikidomain|replace:'/':''}{/if} database:update</kbd>
								</p>
							{/if}

							{if $base_query}
								<p><strong>{tr}The query was:{/tr}</strong></p>
								<strong>{$base_query|escape}</strong>
							{/if}
							{if $values|@count > 0}
								<p><strong>{tr}Values:{/tr}</strong></p>
								<ol>
									{foreach from=$values key=key item=value}
										<li>{$value|escape}</li>
									{/foreach}
								</ol>
							{/if}
							{if $built_query}
								<p><strong>{tr}The built query was likely:{/tr}</strong></p>
								<strong>{$built_query|escape}</strong>
							{/if}
							{if $stacktrace}
								<p><strong>{tr}Stacktrace:{/tr}</strong></p>
								<div>
									{$stacktrace}
								</div>
							{/if}
						</div>

					</div>
					<div class="wikitext" style="border: solid 1px #ccc; margin: 1em auto; padding: 1em; text-align: left; width: 90%;">
						<p>Things to check:</p>
						<ol class="fancylist">
							<li><p>Is your database up and running?</p></li>
							<li><p>Is your database corrupt? Please see <a target="_blank" href="http://doc.tiki.org/Repair+Database">how to repair your database</a></p></li>
							<li><p>Are your database credentials accurate? (username, database name, etc in db/local.php)</p></li>
							{if $where neq 'connection'}
								<li><p>Did you complete the <a href="tiki-install.php">Tiki Installer?</a></p></li>
							{/if}
						</ol>
					</div>
				{elseif $where eq 'connection'}
					<h1>{tr}Tiki is unable to connect to the database{/tr}</h1>
					<div class="wikitext" style="border: solid 1px #ccc; margin: 1em auto; padding: 1em; text-align: left; width: 90%;">
						<p>Things to check:</p>
						<ol class="fancylist">
							<li><p>Is your database up and running?</p></li>
							<li><p>Are your database credentials accurate? (username, database name, etc in db/local.php)</p></li>
						</ol>
					</div>
				{else}
					<h1>{tr}An error occurred while performing the request.{/tr}</h1>
					<div class="wikitext" style="border: solid 1px #ccc; margin: 1em auto; padding: 1em; text-align: left; width: 90%;">
						<p>Things to check:</p>
						<ol class="fancylist">
							<li><p>Did you complete the <a href="tiki-install.php">Tiki Installer?</a></p></li>
							<li><p>Is your database corrupt? Please see <a target="_blank" href="http://doc.tiki.org/Repair+Database">how to repair your database</a></p></li>
							<li><p>Are your database credentials accurate? (username, database name, etc in db/local.php)</p></li>
						</ol>
					</div>
				{/if}

				<p>Please see <a target="_blank" href="http://doc.tiki.org/">the documentation</a> for more information.</p>

				<hr/>

	{* Can be restored when we'll have a new http://branding.tiki.org/Badge
				<p align="center">
					<a target="_blank" href="http://tiki.org" title="Tiki Wiki CMS Groupware">
					<img src="img/tiki/tikibutton2.png" alt="Tiki Wiki CMS Groupware" height="31" width="80">
					</a>
				</p>
	*}
			</div>
		</div>
			</div>{* -- END of main -- *}
		</div> {* -- END of fixedwidth -- *}
	</body>
</html>
