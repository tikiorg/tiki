<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html lang="en" xml:lang="en" xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title>{tr}Error: Unable to connect to the database !{/tr}</title>
	<link rel="stylesheet" href="styles/thenews.css" type="text/css">
</head>
<body >
	<div id="tiki-main">
		<div id="tiki-mid">
			<div style="margin:10px 30px;">
				<h1>
					<font color="red">{tr}TikiWiki is unable to connect to the database.{/tr}</font> 
					<a title="help" href="http://doc.tikiwiki.org/Installation" target="help"><img border="0" src="img/icons/help.gif" alt="Help" /></a>
				</h1>
	<p>{tr}The following error message was returned:{/tr}</p>
	<div class="simplebox">
		{$msg|escape}
	</div>
	<p>{tr}Things to check:{/tr}</p>
	<ul>
		<li>{tr}Is your database up and running?{/tr}</li>
		<li>{tr}Are your database login credentials correct?{/tr}</li>
		<li>{tr}Did you complete the <a href="tiki-install.php">Tiki Installer</a>?{/tr}</li>
	</ul>
	<p>{tr}Please see <a href="http://doc.tikiwiki.org/">the documentation</a> for more information.{/tr}</p>
</div>
		</div>
		<hr>
		<p align="center">
			<a href="http://www.tikiwiki.org" title="TikiWiki">
  			<img src="img/tiki/tikibutton2.png" alt="TikiWiki" border="0" height="31" width="80">
			</a>
		</p>
	</div>
</body>
</html>
