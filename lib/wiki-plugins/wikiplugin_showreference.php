<?php
function wikiplugin_showreference_info()
{
	return array(
		'name' => tra('Add Bibliography'),
		'description' => tra('Add bibliography listing in the footer of wiki pages.'),
		'format' => 'html',
		'prefs' => array('wikiplugin_showreference','feature_references'),
		'params' => array(
			'title' => array(
				'required' => false,
				'name' => tra('Title'),
				'description' => tra('Title to be displayed in the bibliography listing. Default title is \'Bibliography\'.'),
				'default' => 'Bibliography',
			),
			'showtitle' => array(
				'required' => false,
				'name' => tra('Show Title'),
				'description' => tra('Show bibliography title. Title is shown by default.'),
				'options' => array(
					array('text' => tra('Yes'), 'value' => 'yes'), 
					array('text' => tra('No'), 'value' => 'no'), 
				)
			),
			'hlevel' => array(
				'required' => false,
				'name' => tra('Header Tag'),
				'description' => tra('The html header tag level of the title. If \'none\', no header tag is used. Default: 1'),
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('0'), 'value' => '0'), 
					array('text' => tra('1'), 'value' => '1'), 
					array('text' => tra('2'), 'value' => '2'), 
					array('text' => tra('3'), 'value' => '3'), 
					array('text' => tra('4'), 'value' => '4'), 
					array('text' => tra('5'), 'value' => '5'), 
					array('text' => tra('6'), 'value' => '6'), 
					array('text' => tra('7'), 'value' => '7'), 
					array('text' => tra('8'), 'value' => '8'), 
				),
				'default' => '',
			),
		),
	);
}

function wikiplugin_showreference($data,$params) {

	global $prefs;
	
	$params['title'] = trim($params['title']);
	$params['showtitle'] = trim($params['showtitle']);
	$params['hlevel'] = trim($params['hlevel']);

	$title = 'Bibliography';
	if(isset($params['title']) && $params['title']!=''){
		$title = $params['title'];
	}

	if(isset($params['showtitle']) && $params['showtitle']!=''){
		$showtitle = $params['showtitle'];
	}
	if($showtitle=='yes'){
		$showtitle = 1;
	}else{
		$showtitle = 0;
	}

	$hlevel_start = '<H1>';
	$hlevel_end = '</H1>';
	if(isset($params['hlevel']) && $params['hlevel']!=''){
		if($params['hlevel']!='0'){
			$hlevel_start = '<H'.$params['hlevel'].'>';
			$hlevel_end = '</H'.$params['hlevel'].'>';
		}else{
			$hlevel_start = '';
			$hlevel_end = '';
		}
	}else{
		$hlevel_start = '<H1>';
		$hlevel_end = '</H1>';
	}

	if($prefs['wikiplugin_showreference'] == 'y'){

		if(strstr($_SERVER['SCRIPT_NAME'], 'tiki-print.php')){
			
			$page = urldecode($_REQUEST['page']);

			$page_id = TikiLib::lib('tiki')->get_page_id_from_name($page);

		}else{
			
			$object = current_object();

			$page_id = TikiLib::lib('tiki')->get_page_id_from_name($object['object']);
		
		}

		$tags = array('~biblio_code~'=>'biblio_code', '~author~'=>'author', '~title~'=>'title', '~year~'=>'year', '~part~'=>'part', '~uri~'=>'uri', '~code~'=>'code', '~publisher~'=>'publisher', '~location~'=>'location');

		$htm = '';

		include_once ("lib/references/referenceslib.php");

		$referencesLib = new referencesLib();

		$references = $referencesLib->list_assoc_references($page_id);
		// echo '<pre>';print_r($references);die;

		$referencesData = array();
		$is_global = 1;
		if(isset($GLOBALS['referencesData']) && is_array($GLOBALS['referencesData'])){
			$referencesData = $GLOBALS['referencesData'];
			$is_global = 1;
		}else{
			foreach($references['data'] as $data){
				array_push($referencesData, $data['biblio_code']);
			}
			$is_global = 0;
		}
		// echo '<pre>';print_r($referencesData);die;

		if(is_array($referencesData)){
			
			$referencesData = array_unique($referencesData);
			
			$htm .= '<div class="references">';
			
			if($showtitle){
				$htm .= $hlevel_start.$title.$hlevel_end;
			}

			$htm .= '<hr>';

			$htm .= '<ul style="list-style: none outside none;">';

			if(count($referencesData)){
				$values = $referencesLib->get_reference_from_code_and_page($referencesData, $page_id);
			}else{
				$values = array();
			}
			// $values = $references;
			// echo '<pre>';print_r($values);die;

			if($is_global){
				$excluded = array();
				foreach($references['data'] as $key=>$value){
					if(!array_key_exists($key, $values['data'])){
						$excluded[$key] = $references['data'][$key]['biblio_code'];
					}
				}
				foreach($excluded as $ex){
					array_push($referencesData, $ex);
				}
			}

			// $referencesData = array_unique($referencesData);

			foreach ($referencesData as $index=>$ref){
				// echo "<br>index=",$index;
				// echo ', ',$arr['biblio_code'];
				// die;
				// echo '->',$is_global,'<br>';
				// echo '<pre>';print_r($referencesData);die;
				
				$ref_no = $index+1;

				$text = '';
				$cssClass = '';
				if(array_key_exists($ref, $values['data'])){
					
					if($values['data'][$ref]['style'] != ''){
						$cssClass = $values['data'][$ref]['style'];
					}

				// echo '<pre>';print_r($values['data']);die;
					$text = parseTemplate($tags, $ref, $values['data']);
					// echo '<br>text=',$text;die;
				}else{
					if(array_key_exists($ref, $excluded)){
				// echo '<pre>';print_r($references['data']);die;
						$text = parseTemplate($tags, $ref, $references['data']);
					}
				}
				$anchor = "<a name='".$ref."'>&nbsp;</a>";
				if(strlen($text)){
					$htm .= "<li class='".$cssClass."'>" . $ref_no .". ". $text .$anchor. '</li>';
				}else{
					$htm .= "<li class='".$cssClass."' style='font-style:italic'>" . $ref_no .'. missing bibliography definition'.$anchor.'</li>';
				}
			}
			
			$htm .= '</ul>';

			$htm .= '<hr>';

			$htm .= '</div>';
			
		}
		
		return $htm;
	}
}

function parseTemplate($tags, $ref, $values){

	$text = $values[$ref]['template'];
	if($text == ''){
		$text = '~title~, ~part~, ~author~, ~location~, ~year~, ~publisher~, ~code~';
	}

	if($text != ''){
		foreach($tags as $tag=>$val){
			if($values[$ref][$val] == ''){
				$pos = strpos($text, $tag);
				$len = strlen($tag);
				$prevWhiteSpace = $text[$pos-1];

				if($prevWhiteSpace != ' ' && $pos){
					$text = str_replace($text[$pos-1], '', $text);
				}

				$pos = strpos($text, $tag);
				$len = strlen($tag);
				$postWhiteSpace = $text[$pos+$len];
				if($postWhiteSpace != ' ' && $pos){
					$text = str_replace($text[$pos+$len], '', $text);
				}
				$text = str_replace($tag, $values[$ref][$val], $text);
			}else{
				$text = str_replace($tag, $values[$ref][$val], $text);
			}
		}
	}
	return $text;
}
?>