<html>
<head>
	<title>Parser Diff</title>
<?php

require_once('tiki-setup.php');
global $tikilib, $prefs;

$_REQUEST = array_merge(array(
	"page" => "HomePage",
	"id" => ""
), $_REQUEST);

$data = $tikilib->getOne('SELECT data FROM tiki_pages WHERE pageName = ? OR page_id = ?', array($_REQUEST['page'], $_REQUEST['id']));

$prefs['feature_jison_wiki_parser'] = '';

$oldParsed = $tikilib->parse_data($data);

$prefs['feature_jison_wiki_parser'] = 'y';

$newParsed = $tikilib->parse_data($data);

include_once('lib/diff/difflib.php');

$diff = diff2($oldParsed, $newParsed);
?>
</head>
<body>
	<table style="width: 100%;">
		<tr>
			<td>Old Parser</td>
			<td>New Parser</td>
		</tr>
		<tr>
			<td><?php echo $oldParsed; ?></td>
			<td><?php echo $newParsed; ?></td>
		</tr>
	</table>
	<table style="width: 100%;">
		<tr>
			<td colspan="2">Diff</td>
		</tr>
		<?php echo $diff; ?>
	</table>
</body>
</html>