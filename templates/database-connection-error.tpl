<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html lang="en" xml:lang="en" xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<title>{tr}System error.{/tr}</title>
	<link rel="stylesheet" href="styles/fivealive.css" type="text/css"/>
	<style type="text/css" media="screen">
		html {ldelim}
			background-color: #fff;
		{rdelim}
		#centercolumn {ldelim}
			padding: 4em 10em;
		{rdelim}
	</style>
</head>
<body class="tiki_wiki">
<div id="fixedwidth"> {* enables fixed-width layouts *}
	<div id="main">
	<div id="siteheader">
			<div id="sitelogo" style="text-align: center; padding-left: 70px;">
				<img style="border: medium none ;" alt="Site Logo" src="img/tiki/tikisitelogo.png" />
			</div>
	</div>

	<div id="tiki-main">
		<div id="tiki-mid">
			<div style="margin:10px 30px;">
				<h1 class="center">
					{tr}System error.{/tr}
				</h1>
				{if $prefs.error_reporting_level or ( $tiki_p_admin eq 'y' and $prefs.error_reporting_adminonly eq 'y' )}
					<div class="left">
						<p>{tr}The following error message was returned:{/tr}</p>
						<strong>
							<pre>{$msg|escape|nl2br}</pre>
						</strong>

						{if $requires_update}
							<p>
								{tr}Database is not currently up to date! Visit <a href="tiki-install.php">Tiki Installer</a> to resolve this issue.{/tr}
								{tr}If you have shell (SSH) access, you can also use the following, on the command line, from the root of your Tiki installation:{/tr} php installer/shell.php
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
				
					<div class="wikitext" style="border: solid 1px #ccc; margin: 1em auto; padding: 1em; text-align: left; width: 30%;">
						<p>Things to check:</p>
						<ol class="fancylist">
							<li><p>Is your database up and running?</p></li>
							<li><p>Is your database corrupt? Please see <a target="_blank" href="http://doc.tikiwiki.org/Repair+Database">how to repair your database</a></p></li>					
							<li><p>Are your database credentials (username, database name, etc) accurate?</p></li>
							<li><p>Did you complete the <a href="tiki-install.php">Tiki Installer?</a></p></li>
						</ol>
					</div>

					<p>Please see <a target="_blank" href="http://doc.tikiwiki.org/">the documentation</a> for more information.</p>
				</div>
			{else}
				<p>An error occured while performing the request.</p>
			{/if}

			<hr/>

			<p align="center">
				<a target="_blank" href="http://www.tikiwiki.org" title="TikiWiki CMS/Groupware">
				<img src="img/tiki/tikibutton2.png" alt="TikiWiki CMS/Groupware" border="0" height="31" width="80"/>
				</a>
			</p>
		</div>
	</div>
		</div>{* -- END of main -- *}
	</div> {* -- END of fixedwidth -- *}
</body>
</html>
