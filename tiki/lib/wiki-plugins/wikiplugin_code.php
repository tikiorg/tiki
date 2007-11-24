<?php
// $Header: /cvsroot/tikiwiki/tiki/lib/wiki-plugins/wikiplugin_code.php,v 1.22.2.1 2007-11-24 15:28:41 nyloth Exp $
// Displays a snippet of code
// Parameters: ln => line numbering (default false)
// Example:
// {CODE()}
//  print("foo");
// {CODE}
function wikiplugin_code_help() {
//	$help = "Displays a snippet of code.\n";
//	$help.= "Parameters\n";
//	$help.= "* ln=>1 provides line-numbering\n";
//	$help.= "* colors=>php highlights phpcode (other syntaxes to come)\n";
//	$help.= "( note : those parameters are exclusive for now )\n";
//	$help.= "* caption=>provides a caption for the code\n";
//	$help.= "* wrap=>allows line wrapping in the code\n";
//	$help.= "* wiki=>allow wiki interpolation of the code\n";
	$help = tra("Displays a snippet of code").":<br />~np~{CODE(ln=>1,colors=>php|highlights|phpcode,caption=>caption text,wrap=>1,wiki=>1,rtl=>1)}".tra("code")."{CODE}~/np~ - ''".tra("note: colors and ln are exclusive")."''";
	return tra($help);
}

function wikiplugin_code($data, $params) {
	if( is_array( $params ) ) {
		extract ($params,EXTR_SKIP);
	}
	$code = $data;
	$out = '';
	if (isset($caption)) {
		$out .= '<div class="codecaption">'.$caption.'</div>';
	}
	if (isset($rtl) && $rtl == 1) {
		$out .= '<div dir="rtl">'; // force writing the code right to left
	} else {
		$out .= '<div dir="ltr">'; // default is left to right
	}
	if (isset($colors) and ($colors == 'php')) {
		$out.= "<div class='codelisting'>~np~".highlight_string(TikiLib::htmldecode(trim($code)),1)."~/np~</div>";
	} else {
		if (isset($ln) && $ln == 1) {
			$lines = explode("\n", trim($code));
			$code = '';
			$i = 1; 
			foreach ($lines as $line) {
				$code .= sprintf("% 3d",$i) . ' . ' . $line . "\n";
				$i++;
			}
		}
		if (isset($wrap) && $wrap == 1) {
			if (isset($wiki) && $wiki == 1) {
				$out.= "<div class='codelisting'>". $code."</div>";
			} else {
				$code = preg_replace("/\n/", "<br />", $code);
				$out.= "<div class='codelisting'>~np~".$code."~/np~</div>";
			}
		} else {
			if (isset($wiki) && $wiki == 1) {
				$out.= "<pre class='codelisting'>". $code."</pre>";
			} else {
				$out.= "<pre class='codelisting'>~np~".$code."~/np~</pre>";
			}
		}
	}
	$out = str_replace("\\", "\\\\", $out);//prevents vanishing of backslash occurences
	$out = str_replace("$", "\\$", $out);//prevents vanishing of e.g. $1 strings from code listing
	$out .= '</div>'; 
	return $out;
}

?>
