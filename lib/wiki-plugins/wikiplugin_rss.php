<?php
// Includes rss feed output in a wiki page
// Usage:
// {RSS(id=>feedId,max=>3,date=>1,author=>1,desc=>1)}{RSS}
//

function wikiplugin_rss_help() {
	return tra("~np~{~/np~RSS(id=>feedId,max=>3,date=>1,body=>1)}{RSS} Insert rss feed output into a wikipage");
}

function wikiplugin_rss($data,$params) {
	global $smarty;
	global $tikilib;
	global $dbTiki;
	global $rsslib;

	if (!isset($rsslib)) {
		include_once ('lib/rss/rsslib.php');
	}

	extract($params,EXTR_SKIP);

	if (!isset($max)) {$max='10';}
	if (!isset($id)) { $id=1; }
	if (!isset($date)) { $date=0; }
	if (!isset($desc)) { $desc=0; }
	if (!isset($author)) { $author=0; }

	$now = date("U");

	$rssdata = $rsslib->get_rss_module_content($id);
	$items = $rsslib->parse_rss_data($rssdata, $id);

	$repl="";		
	if ($items[0]["isTitle"]=="y") {
		$repl .= '<div class="wiki"><a target="_blank" href="'.$items[0]["link"].'">'.$items[0]["title"].'</a></div><br />'; 
		$items = array_slice ($items, 1);
	}

	if (count($items)<$max) $max = count($items);

	$repl .= '<table class="normal">';
	for ($j = 0; $j < $max; $j++) {
		$repl .= '<tr><td class="heading"><a class="tableheading" target="_blank" href="'.$items[$j]["link"].'"><b>'.$items[$j]["title"].'</b></a>';
		if ($author==1 || $date==1) $repl .= '&nbsp;&nbsp;&nbsp;(';
	    if ($author==1 && isset($items[$j]["author"]) && $items[$j]["author"] <> '')
	    	{
	    		$repl .= $items[$j]["author"];
	    		if ($date==1) $repl .= ', ';
	    	}
	    if ($date==1 && isset($items[$j]["pubDate"]) && $items[$j]["pubDate"] <> '')
	    	{ $repl .= ''.$items[$j]["pubDate"]; }
		if ($author==1 || $date==1) $repl .= ')';
		$repl .= '</td></tr>';
		if ($desc==1) {
			$repl .= '<tr><td class="even" colspan="2">'.html_entity_decode($items[$j]["description"]).'</td></tr>';
		    $repl .= '</tr>';
		}
	}
	$repl .= '</table>';
	return $repl;
	
	
	
	

/*

</tr>
{section name=changes loop=$listpages}
<tr>
{if $smarty.section.changes.index % 2}

<td class="odd">&nbsp;{$listpages[changes].contentId}&nbsp;</td>

{else}

<td class="even">&nbsp;{$listpages[changes].contentId}&nbsp;</td>

{/if}
</tr>
{sectionelse}
<tr><td colspan="6">
<b>{tr}No records found{/tr}</b>
</td></tr>
{/section}
</table>
*/
		
	
}

?>
