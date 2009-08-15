<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html lang="en" xml:lang="en" xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<title>{tr}Error: Unable to connect to the database !{/tr}</title>
	<link rel="stylesheet" href="styles/strasa.css" type="text/css"/>
	<style type="text/css" media="screen">
		html {
			background-color: #fff;
		}
		#centercolumn {
			padding: 4em 10em;
		}
	</style>
</head>
<body class="tiki_wiki" style="text-align: center;">
	<div id="siteheader">
			<div id="sitelogo" style="text-align: center; padding-left: 70px;">
				<img style="border: medium none ;" alt="Site Logo" src="img/tiki/tiki3.png" />
			</div>
	</div>

	<div id="tiki-main">
		<div id="tiki-mid">
			<div style="margin:10px 30px;">
				<h1>
					{tr}TikiWiki CMS/Groupware is unable to connect to the database.{/tr}
				</h1>
				<p>{tr}The following error message was returned:{/tr}</p>
				<strong>
					{$msg|escape}
				</strong>
				
				<div class="wikitext" style="border: solid 1px #ccc; margin: 1em auto; padding: 1em; text-align: left; width: 30%;">
				<p>Things to check:</p>
				<ol class="fancylist">
					<li><p>Is your database up and running?</p></li>
					<li><p>Are your database login credentials correct?</p></li>
					<li><p>Did you complete the <a href="tiki-install.php"> Tiki Installer?</a></p></li>
				</ol>
				</div>

				<p>Please see <a href="http://doc.tikiwiki.org/">the documentation</a> for more information.</p>
			</div>

			<hr/>

			<p align="center">
				<a href="http://www.tikiwiki.org" title="TikiWiki CMS/Groupware">
				<img src="img/tiki/tikibutton2.png" alt="TikiWiki CMS/Groupware" border="0" height="31" width="80"/>
				</a>
			</p>
		</div>
	</div>
</body>
</html>
