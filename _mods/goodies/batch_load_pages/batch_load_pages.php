<?php
require_once ('tiki-setup.php');

echo "2 params file=file_name and max=max_line_length ( for php5.0.4 max is mandatory and not null)<br />";
echo "The file must be a csv file and must contain all or some of the fields: name, hits, data, lastModif, comment, user, ip, description,lang,category<br />";
echo "A cvs file has a first line describing the fields separated by comma, and each next line is a data with the values of the fields separated by comma ( if a value has a comma put the value between double-quotes and if it has double-quote, double the double-quotes<br />";
echo "The page is only created if it does not exist. The category must exist. The category can be added to an alerady existing page<br />";
echo "You must be admin to use this batch<br />";
ini_set('max_execution_time', 0);

if ($tiki_p_admin != 'y') {
	$smarty->assign('msg', tra("You do not have permission to use this feature"));
	$smarty->display("error.tpl");
	die;
}	

if (empty($_REQUEST['file'] )) {
	echo "Syntax batch_load_pages.php?file=pages.csv&max=30000";
	exit;
}
if (!($handle = fopen($_REQUEST['file'], "r"))) {
	echo("can not open the file: ". $_REQUEST['file']);
	exit;
}
if (!isset($_REQUEST['max'])) {
	$_REQUEST['max'] = 0;
}
if (($headings = fgetcsv($handle, $_REQUEST['max'])) === FALSE) {
	echo "First line incorrect";
	exit;
}
$nbColumns = count($headings);
if (!$nbColumns) {
	echo "First line empty";
	exit;
}

for ($i = 0; $i < $nbColumns; $i++) {
	$headings[$i] = trim($headings[$i]);
}

$data = array();
while (($page = fgetcsv($handle, $_REQUEST['max'])) !== FALSE) {
	$data['name'] = '';
	$data['hits'] = '';
	$data['data'] = '';
	$data['lastModif'] = date('U');
	$data['comment'] = '';
	$data['user'] = '';
	$data['ip'] = '';
	$data['description'] = '';
	$data['lang'] = '';
	$data['is_html'] = false;
	$data['category'] = '';
	foreach ($page as $key => $value) {
        $data[$headings[$key]] = $value;
	}
	if ($data['is_html'] == 'y'|| $data['is_html'] == 'yes')
		$data['is_html'] = true;
	else
		$data['is_html'] = false;
	echo $data['name']."<br />";		
	$tikilib->create_page($data['name'], $data['hits'], $data['data'], $data['lastModif'], $data['comment'], $data ['user'], $data['ip'],$data['description'], $data['lang'], $data['is_html']);

	if (!empty($data['category']) && ($page_info = $tikilib->get_page_info($data['name']))) {
		global $categlib; include_once('lib/categories/categlib.php');
		if (!($catObjectId = $categlib->is_categorized('wiki page', $data['name']))) {
			$catObjectId = $categlib->add_categorized_object('wiki page', $data['name'], $page_info['description'], $data['name'], 'tiki-index.php?page='.urlencode($data['name']));
		}
		$query = "select `categId` from `tiki_categories` where `name`=?";
		$categId = $tikilib->getOne($query, array($data['category']));
		if (empty($categId))
			$categId = $categlib->add_category(0, $data['category'], '');
		if ($categId)
			$categlib->categorize($catObjectId, $categId);
		else
			echo "Incorrect category: ".$data['category'];
	}
}

?>
