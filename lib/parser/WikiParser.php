<?php
/* Jison generated parser */
class WikiParser {
	function WikiParser() {
		$this->lexer = new WikiParserLexer;
	}
	
	function trace() {}
	
	var $yy;
	/*
		js values
		symbols_: {"error":2,"wiki":3,"wiki_contents":4,"EOF":5,"contents":6,"plugin":7,"INLINE_PLUGIN":8,"PLUGIN_START":9,"PLUGIN_END":10,"content":11,"np_content":12,"CONTENT":13,"HTML":14,"LINK":15,"HORIZONTAL_BAR":16,"SMILE":17,"BOLD_START":18,"BOLD_END":19,"BOX_START":20,"BOX_END":21,"CENTER_START":22,"CENTER_END":23,"COLORTEXT_START":24,"COLORTEXT_END":25,"ITALIC_START":26,"ITALIC_END":27,"HEADER6_START":28,"HEADER6_END":29,"HEADER5_START":30,"HEADER5_END":31,"HEADER4_START":32,"HEADER4_END":33,"HEADER3_START":34,"HEADER3_END":35,"HEADER2_START":36,"HEADER2_END":37,"HEADER1_START":38,"HEADER1_END":39,"LINK_START":40,"LINK_END":41,"STRIKETHROUGH_START":42,"STRIKETHROUGH_END":43,"TABLE_START":44,"TABLE_END":45,"TITLEBAR_START":46,"TITLEBAR_END":47,"UNDERSCORE_START":48,"UNDERSCORE_END":49,"WIKILINK_START":50,"WIKILINK_END":51,"NP_CONTENT":52,"$accept":0,"$end":1},
		terminals_: {2:"error",5:"EOF",8:"INLINE_PLUGIN",9:"PLUGIN_START",10:"PLUGIN_END",13:"CONTENT",14:"HTML",15:"LINK",16:"HORIZONTAL_BAR",17:"SMILE",18:"BOLD_START",19:"BOLD_END",20:"BOX_START",21:"BOX_END",22:"CENTER_START",23:"CENTER_END",24:"COLORTEXT_START",25:"COLORTEXT_END",26:"ITALIC_START",27:"ITALIC_END",28:"HEADER6_START",29:"HEADER6_END",30:"HEADER5_START",31:"HEADER5_END",32:"HEADER4_START",33:"HEADER4_END",34:"HEADER3_START",35:"HEADER3_END",36:"HEADER2_START",37:"HEADER2_END",38:"HEADER1_START",39:"HEADER1_END",40:"LINK_START",41:"LINK_END",42:"STRIKETHROUGH_START",43:"STRIKETHROUGH_END",44:"TABLE_START",45:"TABLE_END",46:"TITLEBAR_START",47:"TITLEBAR_END",48:"UNDERSCORE_START",49:"UNDERSCORE_END",50:"WIKILINK_START",51:"WIKILINK_END",52:"NP_CONTENT"},
		productions_: [0,[3,2],[4,0],[4,1],[4,2],[4,3],[7,1],[7,3],[6,1],[6,1],[6,2],[6,2],[11,1],[11,1],[11,1],[11,1],[11,1],[11,3],[11,3],[11,3],[11,3],[11,3],[11,3],[11,3],[11,3],[11,3],[11,3],[11,3],[11,3],[11,3],[11,3],[11,3],[11,3],[11,3],[12,1]],
	*/
	var $symbols_ = array(
		"error"=>2,"wiki"=>3,"wiki_contents"=>4,"EOF"=>5,"contents"=>6,"plugin"=>7,"INLINE_PLUGIN"=>8,"PLUGIN_START"=>9,"PLUGIN_END"=>10,"content"=>11,"np_content"=>12,"CONTENT"=>13,"HTML"=>14,"LINK"=>15,"HORIZONTAL_BAR"=>16,"SMILE"=>17,"BOLD_START"=>18,"BOLD_END"=>19,"BOX_START"=>20,"BOX_END"=>21,"CENTER_START"=>22,"CENTER_END"=>23,"COLORTEXT_START"=>24,"COLORTEXT_END"=>25,"ITALIC_START"=>26,"ITALIC_END"=>27,"HEADER6_START"=>28,"HEADER6_END"=>29,"HEADER5_START"=>30,"HEADER5_END"=>31,"HEADER4_START"=>32,"HEADER4_END"=>33,"HEADER3_START"=>34,"HEADER3_END"=>35,"HEADER2_START"=>36,"HEADER2_END"=>37,"HEADER1_START"=>38,"HEADER1_END"=>39,"LINK_START"=>40,"LINK_END"=>41,"STRIKETHROUGH_START"=>42,"STRIKETHROUGH_END"=>43,"TABLE_START"=>44,"TABLE_END"=>45,"TITLEBAR_START"=>46,"TITLEBAR_END"=>47,"UNDERSCORE_START"=>48,"UNDERSCORE_END"=>49,"WIKILINK_START"=>50,"WIKILINK_END"=>51,"NP_CONTENT"=>52,'$accept'=>0,'$end'=>1
	);
	
	var $terminals_ = array(
		2=>"error",5=>"EOF",8=>"INLINE_PLUGIN",9=>"PLUGIN_START",10=>"PLUGIN_END",13=>"CONTENT",14=>"HTML",15=>"LINK",16=>"HORIZONTAL_BAR",17=>"SMILE",18=>"BOLD_START",19=>"BOLD_END",20=>"BOX_START",21=>"BOX_END",22=>"CENTER_START",23=>"CENTER_END",24=>"COLORTEXT_START",25=>"COLORTEXT_END",26=>"ITALIC_START",27=>"ITALIC_END",28=>"HEADER6_START",29=>"HEADER6_END",30=>"HEADER5_START",31=>"HEADER5_END",32=>"HEADER4_START",33=>"HEADER4_END",34=>"HEADER3_START",35=>"HEADER3_END",36=>"HEADER2_START",37=>"HEADER2_END",38=>"HEADER1_START",39=>"HEADER1_END",40=>"LINK_START",41=>"LINK_END",42=>"STRIKETHROUGH_START",43=>"STRIKETHROUGH_END",44=>"TABLE_START",45=>"TABLE_END",46=>"TITLEBAR_START",47=>"TITLEBAR_END",48=>"UNDERSCORE_START",49=>"UNDERSCORE_END",50=>"WIKILINK_START",51=>"WIKILINK_END",52=>"NP_CONTENT"
	);
	
	var $productions_ = array(0,array(3,2),array(4,0),array(4,1),array(4,2),array(4,3),array(7,1),array(7,3),array(6,1),array(6,1),array(6,2),array(6,2),array(11,1),array(11,1),array(11,1),array(11,1),array(11,1),array(11,3),array(11,3),array(11,3),array(11,3),array(11,3),array(11,3),array(11,3),array(11,3),array(11,3),array(11,3),array(11,3),array(11,3),array(11,3),array(11,3),array(11,3),array(11,3),array(11,3),array(12,1));
	
	var $debug = false;
	
	function performAction($yytext, $yyleng, $yylineno, $yy, $yystate, $S, $_S) {
		if ($this->debug == true) {
			print_r(array(
				"yytext"=>$yytext, 
				"yyleng"=>$yyleng,
				"yylineno"=>$yylineno,
				"yy"=>$yy,
				"yystate"=>$yystate,
				"S"=>$S,
				"_S"=>$_S
			));
			die;
		}
		$O = count($S) - 1;
		$r = 0;
		
		switch ($yystate) {
			case 1:return $S[$O-1];
			break;
			case 3:$S = $S[$O];
			break;
			case 4:$S = ($S[$O-1] ? $S[$O-1] : '') . ($S[$O] ? $S[$O] : '');
			break;
			case 5:$S = ($S[$O-2] ? $S[$O-2] : '') . ($S[$O-1] ? $S[$O-1] : '') . ($S[$O] ? $S[$O] : '');
			break;
			case 6:$S = plugin($S[$O]);
			break;
			case 7:
					$S[$O]->body = $S[$O-1];
					$S = plugin($S[$O]);
				
			break;
			case 8:$S = $S[$O];
			break;
			case 9:$S = $S[$O];
			break;
			case 10:$S = $S[$O-1] . $S[$O];
			break;
			case 11:$S = $S[$O-1] . $S[$O];
			break;
			case 12:$S = $S[$O];
			break;
			case 13:$S = isHtmlPermissible($S[$O]);
			break;
			case 14:$S = $S[$O];
			break;
			case 15:$S = $S[$O];
			break;
			case 16:$S = $S[$O];
			break;
			case 17:$S = "<b>" . $S[$O-1] . "</b>";
			break;
			case 18:$S = "<div style='border: solid 1px black;'>" . $S[$O-1] . "</div>";
			break;
			case 19:$S = "<center>" . $S[$O-1] . "</center>";
			break;
			case 20:
					$text = $S[$O-1]->split(':');
					$S = "<span style='color: #" . $text[0] . ";'>" . $text[1] . "</span>";
				
			break;
			case 21:$S = "<i>" . $S[$O-1] . "</i>";
			break;
			case 22:$S = "<h6>" . $S[$O-1] . "</h6>";
			break;
			case 23:$S = "<h5>" . $S[$O-1] . "</h5>";
			break;
			case 24:$S = "<h4>" . $S[$O-1] . "</h4>";
			break;
			case 25:$S = "<h3>" . $S[$O-1] . "</h3>";
			break;
			case 26:$S = "<h2>" . $S[$O-1] . "</h2>";
			break;
			case 27:$S = "<h1>" . $S[$O-1] . "</h1>";
			break;
			case 28:
					$link = $S[$O-1]->split('|');
					$href = $S[$O-1];
					$text = $S[$O-1];
					
					if ($S[$O-1]->match('/\|/')) {
						$href = $link[0];
						$text = $link[1];
					}
					
					$S = "<a href='" . $href . "'>" . $text  . "</a>";
				
			break;
			case 29:$S = "<span style='text-decoration: line-through;'>" . $S[$O-1] . "</span>";
			break;
			case 30:
					$tableContents = '';
					$rows = $S[$O-1]->split('<br />');
					for($i = 0; $i < count($rows); $i++) {
						$cells = $rows[$i].split('|');
						$tableContents .= "<tr>";
						for($j = 0; $j < count($cells); $j++) {
							$tableContents .= "<td>" . $cells[$j] . "</td>";
						}
						$tableContents .= "</tr>";
					}
					$S = "<table style='width: 100%;'>" . $tableContents . "</table>";
				
			break;
			case 31:$S = "<div class='titlebar'>" . $S[$O-1] . "</div>";
			break;
			case 32:$S = "<u>" + $S[$O-1] + "</u>";
			break;
			case 33:
					$wikilink = $S[$O-1]->split('|');
					$href = $S[$O-1];
					$text = $S[$O-1];
					
					if ($S[$O-1]->match('/\|/')) {
						$href = $wikilink[0];
						$text = $wikilink[1];
					}
					
					$S = "<a href='" + $href + "'>" + $text  + "</a>";
				
			break;
			case 34:$S = $S[$O];
			break;
		}
		
		return (object)array("r" => $r, "S" => $S);
	}
		
	var $table = array(array(3=>1,4=>2,5=>array(2,2),6=>3,8=>array(2,2),9=>array(2,2),11=>4,12=>5,13=>array(1,6),14=>array(1,7),15=>array(1,8),16=>array(1,9),17=>array(1,10),18=>array(1,11),20=>array(1,12),22=>array(1,13),24=>array(1,14),26=>array(1,15),28=>array(1,16),30=>array(1,17),32=>array(1,18),34=>array(1,19),36=>array(1,20),38=>array(1,21),40=>array(1,22),42=>array(1,23),44=>array(1,24),46=>array(1,25),48=>array(1,26),50=>array(1,27),52=>array(1,28)),array(1=>array(3)),array(5=>array(1,29),7=>30,8=>array(1,31),9=>array(1,32)),array(5=>array(2,3),8=>array(2,3),9=>array(2,3),10=>array(2,3),11=>33,12=>34,13=>array(1,6),14=>array(1,7),15=>array(1,8),16=>array(1,9),17=>array(1,10),18=>array(1,11),19=>array(2,3),20=>array(1,12),21=>array(2,3),22=>array(1,13),23=>array(2,3),24=>array(1,14),25=>array(2,3),26=>array(1,15),27=>array(2,3),28=>array(1,16),29=>array(2,3),30=>array(1,17),31=>array(2,3),32=>array(1,18),33=>array(2,3),34=>array(1,19),35=>array(2,3),36=>array(1,20),37=>array(2,3),38=>array(1,21),39=>array(2,3),40=>array(1,22),41=>array(2,3),42=>array(1,23),43=>array(2,3),44=>array(1,24),45=>array(2,3),46=>array(1,25),47=>array(2,3),48=>array(1,26),49=>array(2,3),50=>array(1,27),51=>array(2,3),52=>array(1,28)),array(5=>array(2,8),8=>array(2,8),9=>array(2,8),10=>array(2,8),13=>array(2,8),14=>array(2,8),15=>array(2,8),16=>array(2,8),17=>array(2,8),18=>array(2,8),19=>array(2,8),20=>array(2,8),21=>array(2,8),22=>array(2,8),23=>array(2,8),24=>array(2,8),25=>array(2,8),26=>array(2,8),27=>array(2,8),28=>array(2,8),29=>array(2,8),30=>array(2,8),31=>array(2,8),32=>array(2,8),33=>array(2,8),34=>array(2,8),35=>array(2,8),36=>array(2,8),37=>array(2,8),38=>array(2,8),39=>array(2,8),40=>array(2,8),41=>array(2,8),42=>array(2,8),43=>array(2,8),44=>array(2,8),45=>array(2,8),46=>array(2,8),47=>array(2,8),48=>array(2,8),49=>array(2,8),50=>array(2,8),51=>array(2,8),52=>array(2,8)),array(5=>array(2,9),8=>array(2,9),9=>array(2,9),10=>array(2,9),13=>array(2,9),14=>array(2,9),15=>array(2,9),16=>array(2,9),17=>array(2,9),18=>array(2,9),19=>array(2,9),20=>array(2,9),21=>array(2,9),22=>array(2,9),23=>array(2,9),24=>array(2,9),25=>array(2,9),26=>array(2,9),27=>array(2,9),28=>array(2,9),29=>array(2,9),30=>array(2,9),31=>array(2,9),32=>array(2,9),33=>array(2,9),34=>array(2,9),35=>array(2,9),36=>array(2,9),37=>array(2,9),38=>array(2,9),39=>array(2,9),40=>array(2,9),41=>array(2,9),42=>array(2,9),43=>array(2,9),44=>array(2,9),45=>array(2,9),46=>array(2,9),47=>array(2,9),48=>array(2,9),49=>array(2,9),50=>array(2,9),51=>array(2,9),52=>array(2,9)),array(5=>array(2,12),8=>array(2,12),9=>array(2,12),10=>array(2,12),13=>array(2,12),14=>array(2,12),15=>array(2,12),16=>array(2,12),17=>array(2,12),18=>array(2,12),19=>array(2,12),20=>array(2,12),21=>array(2,12),22=>array(2,12),23=>array(2,12),24=>array(2,12),25=>array(2,12),26=>array(2,12),27=>array(2,12),28=>array(2,12),29=>array(2,12),30=>array(2,12),31=>array(2,12),32=>array(2,12),33=>array(2,12),34=>array(2,12),35=>array(2,12),36=>array(2,12),37=>array(2,12),38=>array(2,12),39=>array(2,12),40=>array(2,12),41=>array(2,12),42=>array(2,12),43=>array(2,12),44=>array(2,12),45=>array(2,12),46=>array(2,12),47=>array(2,12),48=>array(2,12),49=>array(2,12),50=>array(2,12),51=>array(2,12),52=>array(2,12)),array(5=>array(2,13),8=>array(2,13),9=>array(2,13),10=>array(2,13),13=>array(2,13),14=>array(2,13),15=>array(2,13),16=>array(2,13),17=>array(2,13),18=>array(2,13),19=>array(2,13),20=>array(2,13),21=>array(2,13),22=>array(2,13),23=>array(2,13),24=>array(2,13),25=>array(2,13),26=>array(2,13),27=>array(2,13),28=>array(2,13),29=>array(2,13),30=>array(2,13),31=>array(2,13),32=>array(2,13),33=>array(2,13),34=>array(2,13),35=>array(2,13),36=>array(2,13),37=>array(2,13),38=>array(2,13),39=>array(2,13),40=>array(2,13),41=>array(2,13),42=>array(2,13),43=>array(2,13),44=>array(2,13),45=>array(2,13),46=>array(2,13),47=>array(2,13),48=>array(2,13),49=>array(2,13),50=>array(2,13),51=>array(2,13),52=>array(2,13)),array(5=>array(2,14),8=>array(2,14),9=>array(2,14),10=>array(2,14),13=>array(2,14),14=>array(2,14),15=>array(2,14),16=>array(2,14),17=>array(2,14),18=>array(2,14),19=>array(2,14),20=>array(2,14),21=>array(2,14),22=>array(2,14),23=>array(2,14),24=>array(2,14),25=>array(2,14),26=>array(2,14),27=>array(2,14),28=>array(2,14),29=>array(2,14),30=>array(2,14),31=>array(2,14),32=>array(2,14),33=>array(2,14),34=>array(2,14),35=>array(2,14),36=>array(2,14),37=>array(2,14),38=>array(2,14),39=>array(2,14),40=>array(2,14),41=>array(2,14),42=>array(2,14),43=>array(2,14),44=>array(2,14),45=>array(2,14),46=>array(2,14),47=>array(2,14),48=>array(2,14),49=>array(2,14),50=>array(2,14),51=>array(2,14),52=>array(2,14)),array(5=>array(2,15),8=>array(2,15),9=>array(2,15),10=>array(2,15),13=>array(2,15),14=>array(2,15),15=>array(2,15),16=>array(2,15),17=>array(2,15),18=>array(2,15),19=>array(2,15),20=>array(2,15),21=>array(2,15),22=>array(2,15),23=>array(2,15),24=>array(2,15),25=>array(2,15),26=>array(2,15),27=>array(2,15),28=>array(2,15),29=>array(2,15),30=>array(2,15),31=>array(2,15),32=>array(2,15),33=>array(2,15),34=>array(2,15),35=>array(2,15),36=>array(2,15),37=>array(2,15),38=>array(2,15),39=>array(2,15),40=>array(2,15),41=>array(2,15),42=>array(2,15),43=>array(2,15),44=>array(2,15),45=>array(2,15),46=>array(2,15),47=>array(2,15),48=>array(2,15),49=>array(2,15),50=>array(2,15),51=>array(2,15),52=>array(2,15)),array(5=>array(2,16),8=>array(2,16),9=>array(2,16),10=>array(2,16),13=>array(2,16),14=>array(2,16),15=>array(2,16),16=>array(2,16),17=>array(2,16),18=>array(2,16),19=>array(2,16),20=>array(2,16),21=>array(2,16),22=>array(2,16),23=>array(2,16),24=>array(2,16),25=>array(2,16),26=>array(2,16),27=>array(2,16),28=>array(2,16),29=>array(2,16),30=>array(2,16),31=>array(2,16),32=>array(2,16),33=>array(2,16),34=>array(2,16),35=>array(2,16),36=>array(2,16),37=>array(2,16),38=>array(2,16),39=>array(2,16),40=>array(2,16),41=>array(2,16),42=>array(2,16),43=>array(2,16),44=>array(2,16),45=>array(2,16),46=>array(2,16),47=>array(2,16),48=>array(2,16),49=>array(2,16),50=>array(2,16),51=>array(2,16),52=>array(2,16)),array(4=>35,6=>3,8=>array(2,2),9=>array(2,2),11=>4,12=>5,13=>array(1,6),14=>array(1,7),15=>array(1,8),16=>array(1,9),17=>array(1,10),18=>array(1,11),19=>array(2,2),20=>array(1,12),22=>array(1,13),24=>array(1,14),26=>array(1,15),28=>array(1,16),30=>array(1,17),32=>array(1,18),34=>array(1,19),36=>array(1,20),38=>array(1,21),40=>array(1,22),42=>array(1,23),44=>array(1,24),46=>array(1,25),48=>array(1,26),50=>array(1,27),52=>array(1,28)),array(4=>36,6=>3,8=>array(2,2),9=>array(2,2),11=>4,12=>5,13=>array(1,6),14=>array(1,7),15=>array(1,8),16=>array(1,9),17=>array(1,10),18=>array(1,11),20=>array(1,12),21=>array(2,2),22=>array(1,13),24=>array(1,14),26=>array(1,15),28=>array(1,16),30=>array(1,17),32=>array(1,18),34=>array(1,19),36=>array(1,20),38=>array(1,21),40=>array(1,22),42=>array(1,23),44=>array(1,24),46=>array(1,25),48=>array(1,26),50=>array(1,27),52=>array(1,28)),array(4=>37,6=>3,8=>array(2,2),9=>array(2,2),11=>4,12=>5,13=>array(1,6),14=>array(1,7),15=>array(1,8),16=>array(1,9),17=>array(1,10),18=>array(1,11),20=>array(1,12),22=>array(1,13),23=>array(2,2),24=>array(1,14),26=>array(1,15),28=>array(1,16),30=>array(1,17),32=>array(1,18),34=>array(1,19),36=>array(1,20),38=>array(1,21),40=>array(1,22),42=>array(1,23),44=>array(1,24),46=>array(1,25),48=>array(1,26),50=>array(1,27),52=>array(1,28)),array(4=>38,6=>3,8=>array(2,2),9=>array(2,2),11=>4,12=>5,13=>array(1,6),14=>array(1,7),15=>array(1,8),16=>array(1,9),17=>array(1,10),18=>array(1,11),20=>array(1,12),22=>array(1,13),24=>array(1,14),25=>array(2,2),26=>array(1,15),28=>array(1,16),30=>array(1,17),32=>array(1,18),34=>array(1,19),36=>array(1,20),38=>array(1,21),40=>array(1,22),42=>array(1,23),44=>array(1,24),46=>array(1,25),48=>array(1,26),50=>array(1,27),52=>array(1,28)),array(4=>39,6=>3,8=>array(2,2),9=>array(2,2),11=>4,12=>5,13=>array(1,6),14=>array(1,7),15=>array(1,8),16=>array(1,9),17=>array(1,10),18=>array(1,11),20=>array(1,12),22=>array(1,13),24=>array(1,14),26=>array(1,15),27=>array(2,2),28=>array(1,16),30=>array(1,17),32=>array(1,18),34=>array(1,19),36=>array(1,20),38=>array(1,21),40=>array(1,22),42=>array(1,23),44=>array(1,24),46=>array(1,25),48=>array(1,26),50=>array(1,27),52=>array(1,28)),array(4=>40,6=>3,8=>array(2,2),9=>array(2,2),11=>4,12=>5,13=>array(1,6),14=>array(1,7),15=>array(1,8),16=>array(1,9),17=>array(1,10),18=>array(1,11),20=>array(1,12),22=>array(1,13),24=>array(1,14),26=>array(1,15),28=>array(1,16),29=>array(2,2),30=>array(1,17),32=>array(1,18),34=>array(1,19),36=>array(1,20),38=>array(1,21),40=>array(1,22),42=>array(1,23),44=>array(1,24),46=>array(1,25),48=>array(1,26),50=>array(1,27),52=>array(1,28)),array(4=>41,6=>3,8=>array(2,2),9=>array(2,2),11=>4,12=>5,13=>array(1,6),14=>array(1,7),15=>array(1,8),16=>array(1,9),17=>array(1,10),18=>array(1,11),20=>array(1,12),22=>array(1,13),24=>array(1,14),26=>array(1,15),28=>array(1,16),30=>array(1,17),31=>array(2,2),32=>array(1,18),34=>array(1,19),36=>array(1,20),38=>array(1,21),40=>array(1,22),42=>array(1,23),44=>array(1,24),46=>array(1,25),48=>array(1,26),50=>array(1,27),52=>array(1,28)),array(4=>42,6=>3,8=>array(2,2),9=>array(2,2),11=>4,12=>5,13=>array(1,6),14=>array(1,7),15=>array(1,8),16=>array(1,9),17=>array(1,10),18=>array(1,11),20=>array(1,12),22=>array(1,13),24=>array(1,14),26=>array(1,15),28=>array(1,16),30=>array(1,17),32=>array(1,18),33=>array(2,2),34=>array(1,19),36=>array(1,20),38=>array(1,21),40=>array(1,22),42=>array(1,23),44=>array(1,24),46=>array(1,25),48=>array(1,26),50=>array(1,27),52=>array(1,28)),array(4=>43,6=>3,8=>array(2,2),9=>array(2,2),11=>4,12=>5,13=>array(1,6),14=>array(1,7),15=>array(1,8),16=>array(1,9),17=>array(1,10),18=>array(1,11),20=>array(1,12),22=>array(1,13),24=>array(1,14),26=>array(1,15),28=>array(1,16),30=>array(1,17),32=>array(1,18),34=>array(1,19),35=>array(2,2),36=>array(1,20),38=>array(1,21),40=>array(1,22),42=>array(1,23),44=>array(1,24),46=>array(1,25),48=>array(1,26),50=>array(1,27),52=>array(1,28)),array(4=>44,6=>3,8=>array(2,2),9=>array(2,2),11=>4,12=>5,13=>array(1,6),14=>array(1,7),15=>array(1,8),16=>array(1,9),17=>array(1,10),18=>array(1,11),20=>array(1,12),22=>array(1,13),24=>array(1,14),26=>array(1,15),28=>array(1,16),30=>array(1,17),32=>array(1,18),34=>array(1,19),36=>array(1,20),37=>array(2,2),38=>array(1,21),40=>array(1,22),42=>array(1,23),44=>array(1,24),46=>array(1,25),48=>array(1,26),50=>array(1,27),52=>array(1,28)),array(4=>45,6=>3,8=>array(2,2),9=>array(2,2),11=>4,12=>5,13=>array(1,6),14=>array(1,7),15=>array(1,8),16=>array(1,9),17=>array(1,10),18=>array(1,11),20=>array(1,12),22=>array(1,13),24=>array(1,14),26=>array(1,15),28=>array(1,16),30=>array(1,17),32=>array(1,18),34=>array(1,19),36=>array(1,20),38=>array(1,21),39=>array(2,2),40=>array(1,22),42=>array(1,23),44=>array(1,24),46=>array(1,25),48=>array(1,26),50=>array(1,27),52=>array(1,28)),array(4=>46,6=>3,8=>array(2,2),9=>array(2,2),11=>4,12=>5,13=>array(1,6),14=>array(1,7),15=>array(1,8),16=>array(1,9),17=>array(1,10),18=>array(1,11),20=>array(1,12),22=>array(1,13),24=>array(1,14),26=>array(1,15),28=>array(1,16),30=>array(1,17),32=>array(1,18),34=>array(1,19),36=>array(1,20),38=>array(1,21),40=>array(1,22),41=>array(2,2),42=>array(1,23),44=>array(1,24),46=>array(1,25),48=>array(1,26),50=>array(1,27),52=>array(1,28)),array(4=>47,6=>3,8=>array(2,2),9=>array(2,2),11=>4,12=>5,13=>array(1,6),14=>array(1,7),15=>array(1,8),16=>array(1,9),17=>array(1,10),18=>array(1,11),20=>array(1,12),22=>array(1,13),24=>array(1,14),26=>array(1,15),28=>array(1,16),30=>array(1,17),32=>array(1,18),34=>array(1,19),36=>array(1,20),38=>array(1,21),40=>array(1,22),42=>array(1,23),43=>array(2,2),44=>array(1,24),46=>array(1,25),48=>array(1,26),50=>array(1,27),52=>array(1,28)),array(4=>48,6=>3,8=>array(2,2),9=>array(2,2),11=>4,12=>5,13=>array(1,6),14=>array(1,7),15=>array(1,8),16=>array(1,9),17=>array(1,10),18=>array(1,11),20=>array(1,12),22=>array(1,13),24=>array(1,14),26=>array(1,15),28=>array(1,16),30=>array(1,17),32=>array(1,18),34=>array(1,19),36=>array(1,20),38=>array(1,21),40=>array(1,22),42=>array(1,23),44=>array(1,24),45=>array(2,2),46=>array(1,25),48=>array(1,26),50=>array(1,27),52=>array(1,28)),array(4=>49,6=>3,8=>array(2,2),9=>array(2,2),11=>4,12=>5,13=>array(1,6),14=>array(1,7),15=>array(1,8),16=>array(1,9),17=>array(1,10),18=>array(1,11),20=>array(1,12),22=>array(1,13),24=>array(1,14),26=>array(1,15),28=>array(1,16),30=>array(1,17),32=>array(1,18),34=>array(1,19),36=>array(1,20),38=>array(1,21),40=>array(1,22),42=>array(1,23),44=>array(1,24),46=>array(1,25),47=>array(2,2),48=>array(1,26),50=>array(1,27),52=>array(1,28)),array(4=>50,6=>3,8=>array(2,2),9=>array(2,2),11=>4,12=>5,13=>array(1,6),14=>array(1,7),15=>array(1,8),16=>array(1,9),17=>array(1,10),18=>array(1,11),20=>array(1,12),22=>array(1,13),24=>array(1,14),26=>array(1,15),28=>array(1,16),30=>array(1,17),32=>array(1,18),34=>array(1,19),36=>array(1,20),38=>array(1,21),40=>array(1,22),42=>array(1,23),44=>array(1,24),46=>array(1,25),48=>array(1,26),49=>array(2,2),50=>array(1,27),52=>array(1,28)),array(4=>51,6=>3,8=>array(2,2),9=>array(2,2),11=>4,12=>5,13=>array(1,6),14=>array(1,7),15=>array(1,8),16=>array(1,9),17=>array(1,10),18=>array(1,11),20=>array(1,12),22=>array(1,13),24=>array(1,14),26=>array(1,15),28=>array(1,16),30=>array(1,17),32=>array(1,18),34=>array(1,19),36=>array(1,20),38=>array(1,21),40=>array(1,22),42=>array(1,23),44=>array(1,24),46=>array(1,25),48=>array(1,26),50=>array(1,27),51=>array(2,2),52=>array(1,28)),array(5=>array(2,34),8=>array(2,34),9=>array(2,34),10=>array(2,34),13=>array(2,34),14=>array(2,34),15=>array(2,34),16=>array(2,34),17=>array(2,34),18=>array(2,34),19=>array(2,34),20=>array(2,34),21=>array(2,34),22=>array(2,34),23=>array(2,34),24=>array(2,34),25=>array(2,34),26=>array(2,34),27=>array(2,34),28=>array(2,34),29=>array(2,34),30=>array(2,34),31=>array(2,34),32=>array(2,34),33=>array(2,34),34=>array(2,34),35=>array(2,34),36=>array(2,34),37=>array(2,34),38=>array(2,34),39=>array(2,34),40=>array(2,34),41=>array(2,34),42=>array(2,34),43=>array(2,34),44=>array(2,34),45=>array(2,34),46=>array(2,34),47=>array(2,34),48=>array(2,34),49=>array(2,34),50=>array(2,34),51=>array(2,34),52=>array(2,34)),array(1=>array(2,1)),array(5=>array(2,4),6=>52,8=>array(2,4),9=>array(2,4),10=>array(2,4),11=>4,12=>5,13=>array(1,6),14=>array(1,7),15=>array(1,8),16=>array(1,9),17=>array(1,10),18=>array(1,11),19=>array(2,4),20=>array(1,12),21=>array(2,4),22=>array(1,13),23=>array(2,4),24=>array(1,14),25=>array(2,4),26=>array(1,15),27=>array(2,4),28=>array(1,16),29=>array(2,4),30=>array(1,17),31=>array(2,4),32=>array(1,18),33=>array(2,4),34=>array(1,19),35=>array(2,4),36=>array(1,20),37=>array(2,4),38=>array(1,21),39=>array(2,4),40=>array(1,22),41=>array(2,4),42=>array(1,23),43=>array(2,4),44=>array(1,24),45=>array(2,4),46=>array(1,25),47=>array(2,4),48=>array(1,26),49=>array(2,4),50=>array(1,27),51=>array(2,4),52=>array(1,28)),array(5=>array(2,6),8=>array(2,6),9=>array(2,6),10=>array(2,6),13=>array(2,6),14=>array(2,6),15=>array(2,6),16=>array(2,6),17=>array(2,6),18=>array(2,6),19=>array(2,6),20=>array(2,6),21=>array(2,6),22=>array(2,6),23=>array(2,6),24=>array(2,6),25=>array(2,6),26=>array(2,6),27=>array(2,6),28=>array(2,6),29=>array(2,6),30=>array(2,6),31=>array(2,6),32=>array(2,6),33=>array(2,6),34=>array(2,6),35=>array(2,6),36=>array(2,6),37=>array(2,6),38=>array(2,6),39=>array(2,6),40=>array(2,6),41=>array(2,6),42=>array(2,6),43=>array(2,6),44=>array(2,6),45=>array(2,6),46=>array(2,6),47=>array(2,6),48=>array(2,6),49=>array(2,6),50=>array(2,6),51=>array(2,6),52=>array(2,6)),array(4=>53,6=>3,8=>array(2,2),9=>array(2,2),10=>array(2,2),11=>4,12=>5,13=>array(1,6),14=>array(1,7),15=>array(1,8),16=>array(1,9),17=>array(1,10),18=>array(1,11),20=>array(1,12),22=>array(1,13),24=>array(1,14),26=>array(1,15),28=>array(1,16),30=>array(1,17),32=>array(1,18),34=>array(1,19),36=>array(1,20),38=>array(1,21),40=>array(1,22),42=>array(1,23),44=>array(1,24),46=>array(1,25),48=>array(1,26),50=>array(1,27),52=>array(1,28)),array(5=>array(2,10),8=>array(2,10),9=>array(2,10),10=>array(2,10),13=>array(2,10),14=>array(2,10),15=>array(2,10),16=>array(2,10),17=>array(2,10),18=>array(2,10),19=>array(2,10),20=>array(2,10),21=>array(2,10),22=>array(2,10),23=>array(2,10),24=>array(2,10),25=>array(2,10),26=>array(2,10),27=>array(2,10),28=>array(2,10),29=>array(2,10),30=>array(2,10),31=>array(2,10),32=>array(2,10),33=>array(2,10),34=>array(2,10),35=>array(2,10),36=>array(2,10),37=>array(2,10),38=>array(2,10),39=>array(2,10),40=>array(2,10),41=>array(2,10),42=>array(2,10),43=>array(2,10),44=>array(2,10),45=>array(2,10),46=>array(2,10),47=>array(2,10),48=>array(2,10),49=>array(2,10),50=>array(2,10),51=>array(2,10),52=>array(2,10)),array(5=>array(2,11),8=>array(2,11),9=>array(2,11),10=>array(2,11),13=>array(2,11),14=>array(2,11),15=>array(2,11),16=>array(2,11),17=>array(2,11),18=>array(2,11),19=>array(2,11),20=>array(2,11),21=>array(2,11),22=>array(2,11),23=>array(2,11),24=>array(2,11),25=>array(2,11),26=>array(2,11),27=>array(2,11),28=>array(2,11),29=>array(2,11),30=>array(2,11),31=>array(2,11),32=>array(2,11),33=>array(2,11),34=>array(2,11),35=>array(2,11),36=>array(2,11),37=>array(2,11),38=>array(2,11),39=>array(2,11),40=>array(2,11),41=>array(2,11),42=>array(2,11),43=>array(2,11),44=>array(2,11),45=>array(2,11),46=>array(2,11),47=>array(2,11),48=>array(2,11),49=>array(2,11),50=>array(2,11),51=>array(2,11),52=>array(2,11)),array(7=>30,8=>array(1,31),9=>array(1,32),19=>array(1,54)),array(7=>30,8=>array(1,31),9=>array(1,32),21=>array(1,55)),array(7=>30,8=>array(1,31),9=>array(1,32),23=>array(1,56)),array(7=>30,8=>array(1,31),9=>array(1,32),25=>array(1,57)),array(7=>30,8=>array(1,31),9=>array(1,32),27=>array(1,58)),array(7=>30,8=>array(1,31),9=>array(1,32),29=>array(1,59)),array(7=>30,8=>array(1,31),9=>array(1,32),31=>array(1,60)),array(7=>30,8=>array(1,31),9=>array(1,32),33=>array(1,61)),array(7=>30,8=>array(1,31),9=>array(1,32),35=>array(1,62)),array(7=>30,8=>array(1,31),9=>array(1,32),37=>array(1,63)),array(7=>30,8=>array(1,31),9=>array(1,32),39=>array(1,64)),array(7=>30,8=>array(1,31),9=>array(1,32),41=>array(1,65)),array(7=>30,8=>array(1,31),9=>array(1,32),43=>array(1,66)),array(7=>30,8=>array(1,31),9=>array(1,32),45=>array(1,67)),array(7=>30,8=>array(1,31),9=>array(1,32),47=>array(1,68)),array(7=>30,8=>array(1,31),9=>array(1,32),49=>array(1,69)),array(7=>30,8=>array(1,31),9=>array(1,32),51=>array(1,70)),array(5=>array(2,5),8=>array(2,5),9=>array(2,5),10=>array(2,5),11=>33,12=>34,13=>array(1,6),14=>array(1,7),15=>array(1,8),16=>array(1,9),17=>array(1,10),18=>array(1,11),19=>array(2,5),20=>array(1,12),21=>array(2,5),22=>array(1,13),23=>array(2,5),24=>array(1,14),25=>array(2,5),26=>array(1,15),27=>array(2,5),28=>array(1,16),29=>array(2,5),30=>array(1,17),31=>array(2,5),32=>array(1,18),33=>array(2,5),34=>array(1,19),35=>array(2,5),36=>array(1,20),37=>array(2,5),38=>array(1,21),39=>array(2,5),40=>array(1,22),41=>array(2,5),42=>array(1,23),43=>array(2,5),44=>array(1,24),45=>array(2,5),46=>array(1,25),47=>array(2,5),48=>array(1,26),49=>array(2,5),50=>array(1,27),51=>array(2,5),52=>array(1,28)),array(7=>30,8=>array(1,31),9=>array(1,32),10=>array(1,71)),array(5=>array(2,17),8=>array(2,17),9=>array(2,17),10=>array(2,17),13=>array(2,17),14=>array(2,17),15=>array(2,17),16=>array(2,17),17=>array(2,17),18=>array(2,17),19=>array(2,17),20=>array(2,17),21=>array(2,17),22=>array(2,17),23=>array(2,17),24=>array(2,17),25=>array(2,17),26=>array(2,17),27=>array(2,17),28=>array(2,17),29=>array(2,17),30=>array(2,17),31=>array(2,17),32=>array(2,17),33=>array(2,17),34=>array(2,17),35=>array(2,17),36=>array(2,17),37=>array(2,17),38=>array(2,17),39=>array(2,17),40=>array(2,17),41=>array(2,17),42=>array(2,17),43=>array(2,17),44=>array(2,17),45=>array(2,17),46=>array(2,17),47=>array(2,17),48=>array(2,17),49=>array(2,17),50=>array(2,17),51=>array(2,17),52=>array(2,17)),array(5=>array(2,18),8=>array(2,18),9=>array(2,18),10=>array(2,18),13=>array(2,18),14=>array(2,18),15=>array(2,18),16=>array(2,18),17=>array(2,18),18=>array(2,18),19=>array(2,18),20=>array(2,18),21=>array(2,18),22=>array(2,18),23=>array(2,18),24=>array(2,18),25=>array(2,18),26=>array(2,18),27=>array(2,18),28=>array(2,18),29=>array(2,18),30=>array(2,18),31=>array(2,18),32=>array(2,18),33=>array(2,18),34=>array(2,18),35=>array(2,18),36=>array(2,18),37=>array(2,18),38=>array(2,18),39=>array(2,18),40=>array(2,18),41=>array(2,18),42=>array(2,18),43=>array(2,18),44=>array(2,18),45=>array(2,18),46=>array(2,18),47=>array(2,18),48=>array(2,18),49=>array(2,18),50=>array(2,18),51=>array(2,18),52=>array(2,18)),array(5=>array(2,19),8=>array(2,19),9=>array(2,19),10=>array(2,19),13=>array(2,19),14=>array(2,19),15=>array(2,19),16=>array(2,19),17=>array(2,19),18=>array(2,19),19=>array(2,19),20=>array(2,19),21=>array(2,19),22=>array(2,19),23=>array(2,19),24=>array(2,19),25=>array(2,19),26=>array(2,19),27=>array(2,19),28=>array(2,19),29=>array(2,19),30=>array(2,19),31=>array(2,19),32=>array(2,19),33=>array(2,19),34=>array(2,19),35=>array(2,19),36=>array(2,19),37=>array(2,19),38=>array(2,19),39=>array(2,19),40=>array(2,19),41=>array(2,19),42=>array(2,19),43=>array(2,19),44=>array(2,19),45=>array(2,19),46=>array(2,19),47=>array(2,19),48=>array(2,19),49=>array(2,19),50=>array(2,19),51=>array(2,19),52=>array(2,19)),array(5=>array(2,20),8=>array(2,20),9=>array(2,20),10=>array(2,20),13=>array(2,20),14=>array(2,20),15=>array(2,20),16=>array(2,20),17=>array(2,20),18=>array(2,20),19=>array(2,20),20=>array(2,20),21=>array(2,20),22=>array(2,20),23=>array(2,20),24=>array(2,20),25=>array(2,20),26=>array(2,20),27=>array(2,20),28=>array(2,20),29=>array(2,20),30=>array(2,20),31=>array(2,20),32=>array(2,20),33=>array(2,20),34=>array(2,20),35=>array(2,20),36=>array(2,20),37=>array(2,20),38=>array(2,20),39=>array(2,20),40=>array(2,20),41=>array(2,20),42=>array(2,20),43=>array(2,20),44=>array(2,20),45=>array(2,20),46=>array(2,20),47=>array(2,20),48=>array(2,20),49=>array(2,20),50=>array(2,20),51=>array(2,20),52=>array(2,20)),array(5=>array(2,21),8=>array(2,21),9=>array(2,21),10=>array(2,21),13=>array(2,21),14=>array(2,21),15=>array(2,21),16=>array(2,21),17=>array(2,21),18=>array(2,21),19=>array(2,21),20=>array(2,21),21=>array(2,21),22=>array(2,21),23=>array(2,21),24=>array(2,21),25=>array(2,21),26=>array(2,21),27=>array(2,21),28=>array(2,21),29=>array(2,21),30=>array(2,21),31=>array(2,21),32=>array(2,21),33=>array(2,21),34=>array(2,21),35=>array(2,21),36=>array(2,21),37=>array(2,21),38=>array(2,21),39=>array(2,21),40=>array(2,21),41=>array(2,21),42=>array(2,21),43=>array(2,21),44=>array(2,21),45=>array(2,21),46=>array(2,21),47=>array(2,21),48=>array(2,21),49=>array(2,21),50=>array(2,21),51=>array(2,21),52=>array(2,21)),array(5=>array(2,22),8=>array(2,22),9=>array(2,22),10=>array(2,22),13=>array(2,22),14=>array(2,22),15=>array(2,22),16=>array(2,22),17=>array(2,22),18=>array(2,22),19=>array(2,22),20=>array(2,22),21=>array(2,22),22=>array(2,22),23=>array(2,22),24=>array(2,22),25=>array(2,22),26=>array(2,22),27=>array(2,22),28=>array(2,22),29=>array(2,22),30=>array(2,22),31=>array(2,22),32=>array(2,22),33=>array(2,22),34=>array(2,22),35=>array(2,22),36=>array(2,22),37=>array(2,22),38=>array(2,22),39=>array(2,22),40=>array(2,22),41=>array(2,22),42=>array(2,22),43=>array(2,22),44=>array(2,22),45=>array(2,22),46=>array(2,22),47=>array(2,22),48=>array(2,22),49=>array(2,22),50=>array(2,22),51=>array(2,22),52=>array(2,22)),array(5=>array(2,23),8=>array(2,23),9=>array(2,23),10=>array(2,23),13=>array(2,23),14=>array(2,23),15=>array(2,23),16=>array(2,23),17=>array(2,23),18=>array(2,23),19=>array(2,23),20=>array(2,23),21=>array(2,23),22=>array(2,23),23=>array(2,23),24=>array(2,23),25=>array(2,23),26=>array(2,23),27=>array(2,23),28=>array(2,23),29=>array(2,23),30=>array(2,23),31=>array(2,23),32=>array(2,23),33=>array(2,23),34=>array(2,23),35=>array(2,23),36=>array(2,23),37=>array(2,23),38=>array(2,23),39=>array(2,23),40=>array(2,23),41=>array(2,23),42=>array(2,23),43=>array(2,23),44=>array(2,23),45=>array(2,23),46=>array(2,23),47=>array(2,23),48=>array(2,23),49=>array(2,23),50=>array(2,23),51=>array(2,23),52=>array(2,23)),array(5=>array(2,24),8=>array(2,24),9=>array(2,24),10=>array(2,24),13=>array(2,24),14=>array(2,24),15=>array(2,24),16=>array(2,24),17=>array(2,24),18=>array(2,24),19=>array(2,24),20=>array(2,24),21=>array(2,24),22=>array(2,24),23=>array(2,24),24=>array(2,24),25=>array(2,24),26=>array(2,24),27=>array(2,24),28=>array(2,24),29=>array(2,24),30=>array(2,24),31=>array(2,24),32=>array(2,24),33=>array(2,24),34=>array(2,24),35=>array(2,24),36=>array(2,24),37=>array(2,24),38=>array(2,24),39=>array(2,24),40=>array(2,24),41=>array(2,24),42=>array(2,24),43=>array(2,24),44=>array(2,24),45=>array(2,24),46=>array(2,24),47=>array(2,24),48=>array(2,24),49=>array(2,24),50=>array(2,24),51=>array(2,24),52=>array(2,24)),array(5=>array(2,25),8=>array(2,25),9=>array(2,25),10=>array(2,25),13=>array(2,25),14=>array(2,25),15=>array(2,25),16=>array(2,25),17=>array(2,25),18=>array(2,25),19=>array(2,25),20=>array(2,25),21=>array(2,25),22=>array(2,25),23=>array(2,25),24=>array(2,25),25=>array(2,25),26=>array(2,25),27=>array(2,25),28=>array(2,25),29=>array(2,25),30=>array(2,25),31=>array(2,25),32=>array(2,25),33=>array(2,25),34=>array(2,25),35=>array(2,25),36=>array(2,25),37=>array(2,25),38=>array(2,25),39=>array(2,25),40=>array(2,25),41=>array(2,25),42=>array(2,25),43=>array(2,25),44=>array(2,25),45=>array(2,25),46=>array(2,25),47=>array(2,25),48=>array(2,25),49=>array(2,25),50=>array(2,25),51=>array(2,25),52=>array(2,25)),array(5=>array(2,26),8=>array(2,26),9=>array(2,26),10=>array(2,26),13=>array(2,26),14=>array(2,26),15=>array(2,26),16=>array(2,26),17=>array(2,26),18=>array(2,26),19=>array(2,26),20=>array(2,26),21=>array(2,26),22=>array(2,26),23=>array(2,26),24=>array(2,26),25=>array(2,26),26=>array(2,26),27=>array(2,26),28=>array(2,26),29=>array(2,26),30=>array(2,26),31=>array(2,26),32=>array(2,26),33=>array(2,26),34=>array(2,26),35=>array(2,26),36=>array(2,26),37=>array(2,26),38=>array(2,26),39=>array(2,26),40=>array(2,26),41=>array(2,26),42=>array(2,26),43=>array(2,26),44=>array(2,26),45=>array(2,26),46=>array(2,26),47=>array(2,26),48=>array(2,26),49=>array(2,26),50=>array(2,26),51=>array(2,26),52=>array(2,26)),array(5=>array(2,27),8=>array(2,27),9=>array(2,27),10=>array(2,27),13=>array(2,27),14=>array(2,27),15=>array(2,27),16=>array(2,27),17=>array(2,27),18=>array(2,27),19=>array(2,27),20=>array(2,27),21=>array(2,27),22=>array(2,27),23=>array(2,27),24=>array(2,27),25=>array(2,27),26=>array(2,27),27=>array(2,27),28=>array(2,27),29=>array(2,27),30=>array(2,27),31=>array(2,27),32=>array(2,27),33=>array(2,27),34=>array(2,27),35=>array(2,27),36=>array(2,27),37=>array(2,27),38=>array(2,27),39=>array(2,27),40=>array(2,27),41=>array(2,27),42=>array(2,27),43=>array(2,27),44=>array(2,27),45=>array(2,27),46=>array(2,27),47=>array(2,27),48=>array(2,27),49=>array(2,27),50=>array(2,27),51=>array(2,27),52=>array(2,27)),array(5=>array(2,28),8=>array(2,28),9=>array(2,28),10=>array(2,28),13=>array(2,28),14=>array(2,28),15=>array(2,28),16=>array(2,28),17=>array(2,28),18=>array(2,28),19=>array(2,28),20=>array(2,28),21=>array(2,28),22=>array(2,28),23=>array(2,28),24=>array(2,28),25=>array(2,28),26=>array(2,28),27=>array(2,28),28=>array(2,28),29=>array(2,28),30=>array(2,28),31=>array(2,28),32=>array(2,28),33=>array(2,28),34=>array(2,28),35=>array(2,28),36=>array(2,28),37=>array(2,28),38=>array(2,28),39=>array(2,28),40=>array(2,28),41=>array(2,28),42=>array(2,28),43=>array(2,28),44=>array(2,28),45=>array(2,28),46=>array(2,28),47=>array(2,28),48=>array(2,28),49=>array(2,28),50=>array(2,28),51=>array(2,28),52=>array(2,28)),array(5=>array(2,29),8=>array(2,29),9=>array(2,29),10=>array(2,29),13=>array(2,29),14=>array(2,29),15=>array(2,29),16=>array(2,29),17=>array(2,29),18=>array(2,29),19=>array(2,29),20=>array(2,29),21=>array(2,29),22=>array(2,29),23=>array(2,29),24=>array(2,29),25=>array(2,29),26=>array(2,29),27=>array(2,29),28=>array(2,29),29=>array(2,29),30=>array(2,29),31=>array(2,29),32=>array(2,29),33=>array(2,29),34=>array(2,29),35=>array(2,29),36=>array(2,29),37=>array(2,29),38=>array(2,29),39=>array(2,29),40=>array(2,29),41=>array(2,29),42=>array(2,29),43=>array(2,29),44=>array(2,29),45=>array(2,29),46=>array(2,29),47=>array(2,29),48=>array(2,29),49=>array(2,29),50=>array(2,29),51=>array(2,29),52=>array(2,29)),array(5=>array(2,30),8=>array(2,30),9=>array(2,30),10=>array(2,30),13=>array(2,30),14=>array(2,30),15=>array(2,30),16=>array(2,30),17=>array(2,30),18=>array(2,30),19=>array(2,30),20=>array(2,30),21=>array(2,30),22=>array(2,30),23=>array(2,30),24=>array(2,30),25=>array(2,30),26=>array(2,30),27=>array(2,30),28=>array(2,30),29=>array(2,30),30=>array(2,30),31=>array(2,30),32=>array(2,30),33=>array(2,30),34=>array(2,30),35=>array(2,30),36=>array(2,30),37=>array(2,30),38=>array(2,30),39=>array(2,30),40=>array(2,30),41=>array(2,30),42=>array(2,30),43=>array(2,30),44=>array(2,30),45=>array(2,30),46=>array(2,30),47=>array(2,30),48=>array(2,30),49=>array(2,30),50=>array(2,30),51=>array(2,30),52=>array(2,30)),array(5=>array(2,31),8=>array(2,31),9=>array(2,31),10=>array(2,31),13=>array(2,31),14=>array(2,31),15=>array(2,31),16=>array(2,31),17=>array(2,31),18=>array(2,31),19=>array(2,31),20=>array(2,31),21=>array(2,31),22=>array(2,31),23=>array(2,31),24=>array(2,31),25=>array(2,31),26=>array(2,31),27=>array(2,31),28=>array(2,31),29=>array(2,31),30=>array(2,31),31=>array(2,31),32=>array(2,31),33=>array(2,31),34=>array(2,31),35=>array(2,31),36=>array(2,31),37=>array(2,31),38=>array(2,31),39=>array(2,31),40=>array(2,31),41=>array(2,31),42=>array(2,31),43=>array(2,31),44=>array(2,31),45=>array(2,31),46=>array(2,31),47=>array(2,31),48=>array(2,31),49=>array(2,31),50=>array(2,31),51=>array(2,31),52=>array(2,31)),array(5=>array(2,32),8=>array(2,32),9=>array(2,32),10=>array(2,32),13=>array(2,32),14=>array(2,32),15=>array(2,32),16=>array(2,32),17=>array(2,32),18=>array(2,32),19=>array(2,32),20=>array(2,32),21=>array(2,32),22=>array(2,32),23=>array(2,32),24=>array(2,32),25=>array(2,32),26=>array(2,32),27=>array(2,32),28=>array(2,32),29=>array(2,32),30=>array(2,32),31=>array(2,32),32=>array(2,32),33=>array(2,32),34=>array(2,32),35=>array(2,32),36=>array(2,32),37=>array(2,32),38=>array(2,32),39=>array(2,32),40=>array(2,32),41=>array(2,32),42=>array(2,32),43=>array(2,32),44=>array(2,32),45=>array(2,32),46=>array(2,32),47=>array(2,32),48=>array(2,32),49=>array(2,32),50=>array(2,32),51=>array(2,32),52=>array(2,32)),array(5=>array(2,33),8=>array(2,33),9=>array(2,33),10=>array(2,33),13=>array(2,33),14=>array(2,33),15=>array(2,33),16=>array(2,33),17=>array(2,33),18=>array(2,33),19=>array(2,33),20=>array(2,33),21=>array(2,33),22=>array(2,33),23=>array(2,33),24=>array(2,33),25=>array(2,33),26=>array(2,33),27=>array(2,33),28=>array(2,33),29=>array(2,33),30=>array(2,33),31=>array(2,33),32=>array(2,33),33=>array(2,33),34=>array(2,33),35=>array(2,33),36=>array(2,33),37=>array(2,33),38=>array(2,33),39=>array(2,33),40=>array(2,33),41=>array(2,33),42=>array(2,33),43=>array(2,33),44=>array(2,33),45=>array(2,33),46=>array(2,33),47=>array(2,33),48=>array(2,33),49=>array(2,33),50=>array(2,33),51=>array(2,33),52=>array(2,33)),array(5=>array(2,7),8=>array(2,7),9=>array(2,7),10=>array(2,7),13=>array(2,7),14=>array(2,7),15=>array(2,7),16=>array(2,7),17=>array(2,7),18=>array(2,7),19=>array(2,7),20=>array(2,7),21=>array(2,7),22=>array(2,7),23=>array(2,7),24=>array(2,7),25=>array(2,7),26=>array(2,7),27=>array(2,7),28=>array(2,7),29=>array(2,7),30=>array(2,7),31=>array(2,7),32=>array(2,7),33=>array(2,7),34=>array(2,7),35=>array(2,7),36=>array(2,7),37=>array(2,7),38=>array(2,7),39=>array(2,7),40=>array(2,7),41=>array(2,7),42=>array(2,7),43=>array(2,7),44=>array(2,7),45=>array(2,7),46=>array(2,7),47=>array(2,7),48=>array(2,7),49=>array(2,7),50=>array(2,7),51=>array(2,7),52=>array(2,7)));
	
	var $defaultActions = array(29=>array(2,1));
	
	function popStack($n, $stack, $vstack, $lstack) {
		array_slice($stack, 0, 2 * $n);
		array_slice($vstack, 0, $n);
		array_slice($lstack, 0, $n);
	}
	
	function lex() {
		$token = $this->lexer->lex(); // $end = 1
		$token = (empty($token) ? 1 : $token);
		// if token isn't its numeric value, convert
		if (!is_numeric($token)) {
			$token = (array_key_exists($token, $this->symbols_) ? $this->symbols_[$token] : $token);
		}
		return $token;
	}
	
	function parseError($str, $hash) {
		throw new Exception($str);
	}
	
	function parse($input) {
		$self = $this;
		$stack = array(0);
		$vstack = array(null);
		// semantic value stack
		$lstack = array();
		//location stack
		$table = $this->table;
		$yytext = '';
		$yylineno = 0;
		$yyleng = 0;
		$shifts = 0;
		$reductions = 0;
		$recovering = 0;
		$TERROR = 2;
		$EOF = 1;
		
		$this->yy = (object)array();
		$this->lexer->setInput($input);
		$this->lexer->yy = $this->yy;
		$this->yy->lexer = $this->lexer;
		if (empty($this->lexer->yylloc)) $this->lexer->yylloc = (object)array();
		$yyloc = $this->lexer->yylloc;
		array_push($lstack, $yyloc);
		
		if (!empty($this->yy->parseError) && function_exists($this->yy->parseError)) $this->parseError = $this->yy->parseError;

		//$symbol, $preErrorSymbol, $state, $action, $a, $r, $yyval = array();
		//$p, $len, $newState, $expected, $recovered = false;
		
		$yyval = (object)array();
		$recovered = false;
		
		while (true) {
			// retreive state number from top of stack
			$state = $stack[count($stack) - 1];
			// use default actions if available
			if (array_key_exists($state, $this->defaultActions)) {
				$action = $this->defaultActions[$state];		
			} else {
				if (empty($symbol))
					$symbol = $this->lex();
				
				// read action for current state and first input
				if (array_key_exists($state, $table)) {
					if (array_key_exists($symbol, $table[$state])) {
						$action = $table[$state][$symbol];
					}
				}
			}
			
			if (empty($action) == true) {
				if (empty($recovering) == false) {
					// Report error
					$expected = array();
					foreach($table[$state] as $p) {
						if ($p > 2) {
							array_push($expected, implode($p));
						}
					}
					
					$errStr = 'Parse error on line ' . ($yylineno + 1) . ":\n" . $this->lexer->showPosition() . '\nExpecting ' . implode(', ', $expected);
			
					$this->lexer->parseError($errStr, array(
						"text"=> $this->lexer->match,
						"token"=> $symbol,
						"line"=> $this->lexer->yylineno,
						"loc"=> $yyloc,
						"expected"=> $expected
					));
				}
	
				// just recovered from another error
				if ($recovering == 3) {
					if ($symbol == $EOF) {
						throw new Exception($errStr || 'Parsing halted.');
					}
		
					// discard current lookahead and grab another
					$yyleng = $this->lexer->yyleng;
					$yytext = $this->lexer->yytext;
					$yylineno = $this->lexer->yylineno;
					$yyloc = $this->lexer->yylloc;
					$symbol = $this->lex();
				}
	
				// try to recover from error
				while (true) {
					// check for error recovery rule in this state
					if (array_key_exists($TERROR, $table[$state])) {
						break 2;
					}
					if ($state == 0) {
						throw new Exception($errStr || 'Parsing halted.');
					}
					//$this->popStack(1, $stack, $vstack);
					
					array_slice($stack, 0, 2 * 1);
					array_slice($vstack, 0, 1);
					
					$lenn = count($stack) - 1;
					
					$state = $stack[count($stack) - 1];
				}
	
				$preErrorSymbol = $symbol; // save the lookahead token
				$symbol = $TERROR; // insert generic error symbol as new lookahead
				$state = $stack[count($stack) - 1];
				if (array_key_exists($state, $table)) {
					if (array_key_exists($TERROR, $table[$state])) {
						$action = $table[$state][$TERROR];
					}
				}
				$recovering = 3; // allow 3 real symbols to be shifted before reporting a new error
			}
	
			// this shouldn't happen, unless resolve defaults are off
			if (is_array($action[0]) && count($action) > 1) {
				throw new Exception('Parse Error: multiple actions possible at state: ' . $state . ', token: ' . $symbol);
			}
			
			switch ($action[0]) {
				case 1:
					// shift
					//$this->shiftCount++;
					array_push($stack, $symbol);
					array_push($vstack, $this->lexer->yytext);
					array_push($lstack, $this->lexer->yylloc);
					array_push($stack, $action[1]); // push state
					$symbol = "";
					if (empty($preErrorSymbol)) { // normal execution/no error
						$yyleng = $this->lexer->yyleng;
						$yytext = $this->lexer->yytext;
						$yylineno = $this->lexer->yylineno;
						$yyloc = $this->lexer->yylloc;
						if ($recovering > 0) $recovering--;
					} else { // error just occurred, resume old lookahead f/ before error
						$symbol = $preErrorSymbol;
						$preErrorSymbol = "";
					}
					break;
		
				case 2:
					// reduce
					$len = $this->productions_[$action[1]][1];
					// perform semantic action
					$yyval->S = $vstack[count($vstack) - $len];// default to $S = $1
					// default location, uses first token for firsts, last for lasts
					$yyval->_S = (object)array(
                        "first_line"=> $lstack[count($lstack) - ($len || 1)]->first_line,
                        "last_line"=> $lstack[count($lstack) - 1]->last_line,
                        "first_column"=> $lstack[count($lstack) - ($len || 1)]->first_column,
                        "last_column"=> $lstack[count($lstack) - 1]->last_column
                    );
					$r = $this->performAction($yytext, $yyleng, $yylineno, $this->yy, $action[1], $vstack, $lstack);
					
					if (empty($r->r) == false) {
						return $r->r;
					}
					
					$yyval->S = (isset($r->S) ? $r->S : $r);
					
					// pop off stack		
					if ($len > 0) {
						$stack = array_slice($stack, 0, -1 * $len * 2);
						$vstack = array_slice($vstack, 0, -1 * $len);
						$lstack = array_slice($lstack, 0, -1 * $len);
					}
					
					array_push($stack, $this->productions_[$action[1]][0]); // push nonterminal (reduce)
					array_push($vstack, $yyval->S);
					array_push($lstack, $yyval->_S);
					
					// goto new state = table[STATE][NONTERMINAL]
					$newState = $table[$stack[count($stack) - 2]][$stack[count($stack) - 1]];
					array_push($stack, $newState);
					break;
		
				case 3:
					// accept
					return $yyval->S;
			}

		}

		return true;
	}
}

/* Jison generated lexer */
class WikiParserLexer {
	var $EOF = 1;
	var $S = "";
	var $yy = "";
	var $yylineno = "";
	var $yyleng = "";
	var $yytext = "";
	var $matched = "";
	var $match = "";
	var $conditionsStack = array();
	
	function lexer() {}
	
	function parseError($str, $hash) {
		throw new Exception($str);
	}
	
	function setInput($input) {
		$this->_input = $input;
		$this->_more = $this->_less = $this->done = false;
		$this->yylineno = $this->yyleng = 0;
		$this->yytext = $this->matched = $this->match = '';
		$this->conditionStack = array('INITIAL');
		$this->yylloc = (object)array(
			"first_line"=> 1,
			"first_column"=> 0,
			"last_line"=> 1,
			"last_column"=> 0
		);
		return $this;
	}
	
	function input() {
		$ch = $this->_input[0];
		$this->yytext += $ch;
		$this->yyleng++;
		$this->match += $ch;
		$this->matched += $ch;
		$lines = preg_match("\n", $ch);
		if (count($lines) > 0) $this->yylineno++;
		array_slice($this->_input, 1);
		return $ch;
	}
	
	function unput($ch) {
		$this->_input = $ch + $this->_input;
		return $this;
	}
	
	function more() {
		$this->_more = true;
		return $this;
	}
	
	function pastInput() {
		$past = substr($this->matched, 0, count($this->matched) - count($this->match));
		return (strlen($past) > 20 ? '...' : '') . preg_replace("/\n/", "", substr($past, -20));
	}
	
	function upcomingInput() {
		$next = $this->match;
		if (strlen($next) < 20) {
			$next .= substr($this->_input, 0, 20 - strlen($next));
		}
		return preg_replace("/\n/", "", substr($next, 0, 20) . (strlen($next) > 20 ? '...' : ''));
	}
	
	function showPosition() {
		$pre = $this->pastInput();
		$c = implode(array(strlen($pre) + 1), "-");
		return $pre . $this->upcomingInput() . "\n" . $c . "^";
	}
	
	function next() {
		if ($this->done == true) {
			return $this->EOF;
		}
		
		if ($this->_input == false) $this->_input = "";
		if (empty($this->_input)) $this->done = true;

		if ($this->_more == false) {
			$this->yytext = '';
			$this->match = '';
		}
		
		$rules = $this->_currentRules();
		for ($i = 0; $i < count($rules); $i++) {
			preg_match($this->rules[$rules[$i]], $this->_input, $match);
			
			if ( isset($match) && isset($match[0]) ) {
				preg_match_all("/\n/", $match[0], $lines, PREG_PATTERN_ORDER);
				if (count($lines) > 1) $this->yylineno += count($lines);
				$this->yylloc = (object)array(
					"first_line"=> $this->yylloc->last_line,
					"last_line"=> $this->yylineno + 1,
					"first_column"=> $this->yylloc->last_column,
					"last_column"=> $lines ? count($lines[count($lines) - 1]) - 1 : $this->yylloc->last_column + count($match[0])
				);
				$this->yytext .= $match[0];
				$this->match .= $match[0];
				$this->matches = $match[0];
				$this->yyleng = strlen($this->yytext);
				$this->_more = false;
				$this->_input = substr($this->_input, strlen($match[0]), strlen($this->_input));
				$this->matched .= $match[0];
				$token = $this->performAction($this->yy, $this, $rules[$i],$this->conditionStack[count($this->conditionStack) - 1]);
				
				if (empty($token) == false) {
					return $token;
				} else {
					return;
				}
			}
		}
		
		if (empty($this->_input)) {
			return $this->EOF;
		} else {
			$this->parseError('Lexical error on line ' . ($this->yylineno + 1) . '. Unrecognized text.\n' . $this->showPosition(), array(
				"text"=> "",
				"token"=> null,
				"line"=> $this->yylineno
			));
		}
	}
	
	function lex() {
		$r = $this->next();
		if (empty($r) == false) {
			return $r;
		} else if ($this->done != true) {
			return $this->lex();
		}
	}
	
	function begin($condition) {
		array_push($this->conditionStack, $condition);
	}
	
	function popState() {
		return array_pop($this->conditionStack);
	}
	
	function _currentRules() {
		return $this->conditions[
			$this->conditionStack[
				count($this->conditionStack) - 1
			]
		]['rules'];
	}
	
	function performAction($yy, $yy_, $avoiding_name_collisions, $YY_START = null) {

		$YYSTATE = $YY_START;
		switch($avoiding_name_collisions) {
			case 0:
					$yy_->yytext = $yy_->yytext->substring(4, $yy_->yytext->length - 5);
					return 52;
				
			break;
			case 1:
					$pluginName = $yy_->yytext->match('/^\{([a-z]+)/')/*[1]*/;
					$pluginParams =  $yy_->yytext->match('/[ ].*?[}]|[/}]/');
					$yy_->yytext = array(
						"name"=> pluginName,
						"params"=> pluginParams,
						"body"=> ''
					);
					return 8;
				
			break;
			case 2:
					$pluginName = $yy_->yytext.match('/^\{([A-Z]+)/')/*[1]*/;
					$pluginParams =  $yy_->yytext.match('/[(].*?[)]/');
					
					if (!isset($yy->pluginStack)) $yy->pluginStack = array();
					array_push($yy->pluginStack, array(
						"name"=> $pluginName,
						"params"=> $pluginParams
					));
					
					return 9;
				
			break;
			case 3:
					if ($yy->pluginStack) {
						if (
							str_length($yy->pluginStack) &&
							$yy_->yytext->match($yy->pluginStack[str_length($yy->pluginStack) - 1]->name)
						) {
							$readyPlugin = $yy->pluginStack.pop();
							$yy_->yytext = $readyPlugin;
							return 10;
						}
					}
					return 'CONTENT';
				
			break;
			case 4:
					$yy_->yytext = "<hr />";
					return 16;
				
			break;
			case 5:
					$smile = $yy_->yytext->substring(2, strlen($yy_.yytext) - 2);
					$yy_->yytext = "<img src='img/smiles/icon_" + $smile + ".gif' alt='" + $smile + "' />";
					return 17;
				
			break;
			case 6:
					$smile = $yy_->yytext->substring(2, strlen($yy_->yytext) - 2);
					$yy_->yytext = "<img src='img/smiles/icon_" + $smile + ".gif' alt='" + $smile + "' />";
					return 13;
		
			break;
			case 7:$this->popState();				return 19;
			break;
			case 8:$this->begin('bold');				return 18;
			break;
			case 9:$this->popState();				return 21;
			break;
			case 10:$this->begin('box');				return 20;
			break;
			case 11:$this->popState();				return 23;
			break;
			case 12:$this->begin('center');			return 22;
			break;
			case 13:$this->popState();				return 25;
			break;
			case 14:$this->begin('colortext');		return 24;
			break;
			case 15:$this->popState();				return 29;
			break;
			case 16:$this->begin('header6');			return 28;
			break;
			case 17:$this->popState();				return 31;
			break;
			case 18:$this->begin('header5');			return 30;
			break;
			case 19:$this->popState();				return 33;
			break;
			case 20:$this->begin('header4');			return 32;
			break;
			case 21:$this->popState();				return 35;
			break;
			case 22:$this->begin('header3');			return 34;
			break;
			case 23:$this->popState();				return 37;
			break;
			case 24:$this->begin('header2');			return 36;
			break;
			case 25:$this->popState();				return 39;
			break;
			case 26:$this->begin('header1');			return 38;
			break;
			case 27:$this->popState();				return 27;
			break;
			case 28:$this->begin('italic');			return 26;
			break;
			case 29:$this->popState();				return 41;
			break;
			case 30:$this->begin('link');				return 40;
			break;
			case 31:$this->popState();				return 43;
			break;
			case 32:$this->begin('strikethrough');	return 42;
			break;
			case 33:$this->popState();				return 45;
			break;
			case 34:$this->begin('table');			return 44;
			break;
			case 35:$this->popState();				return 47;
			break;
			case 36:$this->begin('titlebar');			return 46;
			break;
			case 37:$this->popState();				return 49;
			break;
			case 38:$this->begin('underscore');		return 48;
			break;
			case 39:$this->popState();				return 51;
			break;
			case 40:$this->begin('wikilink');			return 50;
			break;
			case 41:return 14;
			break;
			case 42:return 13;
			break;
			case 43:
					$yy_->yytext = str_replace('/\n/', '<br />', $yy_->yytext);
					return 13;
				
			break;
			case 44:return 5;
			break;
		}
	}
	
	/* js values
		lexer.rules = [/^~np~(.|\n)*?~\/np~/,/^\{[a-z]+.*?\}/,/^\{[A-Z]+\(.*?\)\}/,/^\{[A-Z]+\}/,/^---/,/^\(:[a-z]+:\)/,/^\[\[.*?/,/^[_][_]/,/^[_][_]/,/^[\^]/,/^[\^]/,/^[:][:]/,/^[:][:]/,/^[\~][\~]/,/^[\~][\~][#]/,/^[\n]/,/^[\n](!!!!!!)/,/^[\n]/,/^[\n](!!!!!)/,/^[\n]/,/^[\n](!!!!)/,/^[\n]/,/^[\n](!!!)/,/^[\n]/,/^[\n](!!)/,/^[\n]/,/^[\n](!)/,/^['][']/,/^['][']/,/^(\])/,/^(\[)/,/^[-][-]/,/^[-][-]/,/^[|][|]/,/^[|][|]/,/^[=][-]/,/^[-][=]/,/^[=][=][=]/,/^[=][=][=]/,/^[)][)]/,/^[(][(]/,/^<(.|\n)*?>/,/^(.)/,/^(\n)/,/^$/];
		lexer.conditions = {"bold":{"rules":[0,1,2,3,4,5,6,7,8,10,12,14,16,18,20,22,24,26,28,30,32,34,36,38,40,41,42,43,44],"inclusive":true},"box":{"rules":[0,1,2,3,4,5,6,8,9,10,12,14,16,18,20,22,24,26,28,30,32,34,36,38,40,41,42,43,44],"inclusive":true},"center":{"rules":[0,1,2,3,4,5,6,8,10,11,12,14,16,18,20,22,24,26,28,30,32,34,36,38,40,41,42,43,44],"inclusive":true},"colortext":{"rules":[0,1,2,3,4,5,6,8,10,12,13,14,16,18,20,22,24,26,28,30,32,34,36,38,40,41,42,43,44],"inclusive":true},"italic":{"rules":[0,1,2,3,4,5,6,8,10,12,14,16,18,20,22,24,26,27,28,30,32,34,36,38,40,41,42,43,44],"inclusive":true},"header6":{"rules":[0,1,2,3,4,5,6,8,10,12,14,15,16,18,20,22,24,26,28,30,32,34,36,38,40,41,42,43,44],"inclusive":true},"header5":{"rules":[0,1,2,3,4,5,6,8,10,12,14,16,17,18,20,22,24,26,28,30,32,34,36,38,40,41,42,43,44],"inclusive":true},"header4":{"rules":[0,1,2,3,4,5,6,8,10,12,14,16,18,19,20,22,24,26,28,30,32,34,36,38,40,41,42,43,44],"inclusive":true},"header3":{"rules":[0,1,2,3,4,5,6,8,10,12,14,16,18,20,21,22,24,26,28,30,32,34,36,38,40,41,42,43,44],"inclusive":true},"header2":{"rules":[0,1,2,3,4,5,6,8,10,12,14,16,18,20,22,23,24,26,28,30,32,34,36,38,40,41,42,43,44],"inclusive":true},"header1":{"rules":[0,1,2,3,4,5,6,8,10,12,14,16,18,20,22,24,25,26,28,30,32,34,36,38,40,41,42,43,44],"inclusive":true},"link":{"rules":[0,1,2,3,4,5,6,8,10,12,14,16,18,20,22,24,26,28,29,30,32,34,36,38,40,41,42,43,44],"inclusive":true},"strikethrough":{"rules":[0,1,2,3,4,5,6,8,10,12,14,16,18,20,22,24,26,28,30,31,32,34,36,38,40,41,42,43,44],"inclusive":true},"table":{"rules":[0,1,2,3,4,5,6,8,10,12,14,16,18,20,22,24,26,28,30,32,33,34,36,38,40,41,42,43,44],"inclusive":true},"titlebar":{"rules":[0,1,2,3,4,5,6,8,10,12,14,16,18,20,22,24,26,28,30,32,34,35,36,38,40,41,42,43,44],"inclusive":true},"underscore":{"rules":[0,1,2,3,4,5,6,8,10,12,14,16,18,20,22,24,26,28,30,32,34,36,37,38,40,41,42,43,44],"inclusive":true},"wikilink":{"rules":[0,1,2,3,4,5,6,8,10,12,14,16,18,20,22,24,26,28,30,32,34,36,38,39,40,41,42,43,44],"inclusive":true},"INITIAL":{"rules":[0,1,2,3,4,5,6,8,10,12,14,16,18,20,22,24,26,28,30,32,34,36,38,40,41,42,43,44],"inclusive":true}};return lexer;})()
	*/
	var $rules = array(
		'/^~np~(.|\n)*?~\/np~/',
		'/^\{[a-z]+.*?\}/',
		'/^\{[A-Z]+\(.*?\)\}/',
		'/^\{[A-Z]+\}/',
		'/^---/',
		'/^\(:[a-z]+:\)/',
		'/^\[\[.*?/',
		'/^[_][_]/',
		'/^[_][_]/',
		'/^[\^]/',
		'/^[\^]/',
		'/^[:][:]/',
		'/^[:][:]/',
		'/^[\~][\~]/',
		'/^[\~][\~][#]/',
		'/^[\n]/',
		'/^[\n](!!!!!!)/',
		'/^[\n]/',
		'/^[\n](!!!!!)/',
		'/^[\n]/',
		'/^[\n](!!!!)/',
		'/^[\n]/',
		'/^[\n](!!!)/',
		'/^[\n]/',
		'/^[\n](!!)/',
		'/^[\n]/',
		'/^[\n](!)/',
		"/^['][']/",
		"/^['][']/",
		'/^(\])/',
		'/^(\[)/',
		'/^[-][-]/',
		'/^[-][-]/',
		'/^[|][|]/',
		'/^[|][|]/',
		'/^[=][-]/',
		'/^[-][=]/',
		'/^[=][=][=]/',
		'/^[=][=][=]/',
		'/^[)][)]/',
		'/^[(][(]/',
		'/^<(.|\n)*?>/',
		'/^(.)/',
		'/^(\n)/',
		'/^$/'
	);
	
	var $conditions = array(
		"bold"=>array("rules"=>array(0,1,2,3,4,5,6,7,8,10,12,14,16,18,20,22,24,26,28,30,32,34,36,38,40,41,42,43,44),"inclusive"=>true),"box"=>array("rules"=>array(0,1,2,3,4,5,6,8,9,10,12,14,16,18,20,22,24,26,28,30,32,34,36,38,40,41,42,43,44),"inclusive"=>true),"center"=>array("rules"=>array(0,1,2,3,4,5,6,8,10,11,12,14,16,18,20,22,24,26,28,30,32,34,36,38,40,41,42,43,44),"inclusive"=>true),"colortext"=>array("rules"=>array(0,1,2,3,4,5,6,8,10,12,13,14,16,18,20,22,24,26,28,30,32,34,36,38,40,41,42,43,44),"inclusive"=>true),"italic"=>array("rules"=>array(0,1,2,3,4,5,6,8,10,12,14,16,18,20,22,24,26,27,28,30,32,34,36,38,40,41,42,43,44),"inclusive"=>true),"header6"=>array("rules"=>array(0,1,2,3,4,5,6,8,10,12,14,15,16,18,20,22,24,26,28,30,32,34,36,38,40,41,42,43,44),"inclusive"=>true),"header5"=>array("rules"=>array(0,1,2,3,4,5,6,8,10,12,14,16,17,18,20,22,24,26,28,30,32,34,36,38,40,41,42,43,44),"inclusive"=>true),"header4"=>array("rules"=>array(0,1,2,3,4,5,6,8,10,12,14,16,18,19,20,22,24,26,28,30,32,34,36,38,40,41,42,43,44),"inclusive"=>true),"header3"=>array("rules"=>array(0,1,2,3,4,5,6,8,10,12,14,16,18,20,21,22,24,26,28,30,32,34,36,38,40,41,42,43,44),"inclusive"=>true),"header2"=>array("rules"=>array(0,1,2,3,4,5,6,8,10,12,14,16,18,20,22,23,24,26,28,30,32,34,36,38,40,41,42,43,44),"inclusive"=>true),"header1"=>array("rules"=>array(0,1,2,3,4,5,6,8,10,12,14,16,18,20,22,24,25,26,28,30,32,34,36,38,40,41,42,43,44),"inclusive"=>true),"link"=>array("rules"=>array(0,1,2,3,4,5,6,8,10,12,14,16,18,20,22,24,26,28,29,30,32,34,36,38,40,41,42,43,44),"inclusive"=>true),"strikethrough"=>array("rules"=>array(0,1,2,3,4,5,6,8,10,12,14,16,18,20,22,24,26,28,30,31,32,34,36,38,40,41,42,43,44),"inclusive"=>true),"table"=>array("rules"=>array(0,1,2,3,4,5,6,8,10,12,14,16,18,20,22,24,26,28,30,32,33,34,36,38,40,41,42,43,44),"inclusive"=>true),"titlebar"=>array("rules"=>array(0,1,2,3,4,5,6,8,10,12,14,16,18,20,22,24,26,28,30,32,34,35,36,38,40,41,42,43,44),"inclusive"=>true),"underscore"=>array("rules"=>array(0,1,2,3,4,5,6,8,10,12,14,16,18,20,22,24,26,28,30,32,34,36,37,38,40,41,42,43,44),"inclusive"=>true),"wikilink"=>array("rules"=>array(0,1,2,3,4,5,6,8,10,12,14,16,18,20,22,24,26,28,30,32,34,36,38,39,40,41,42,43,44),"inclusive"=>true),"INITIAL"=>array("rules"=>array(0,1,2,3,4,5,6,8,10,12,14,16,18,20,22,24,26,28,30,32,34,36,38,40,41,42,43,44),"inclusive"=>true)
	);
}

//this is for testing, and should be removed when it goes production
$wikiParser = new WikiParser;

print_r($wikiParser->parse("
__bold ''italic''__
!HEader
"));