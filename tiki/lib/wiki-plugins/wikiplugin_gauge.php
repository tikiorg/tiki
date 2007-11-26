<?php

// Displays a graphical GAUGE
// Usage:
// {GAUGE(params)}description{GAUGE}
// Description is optional and will be displayed below the gauge if present
// Parameters:
//   color      bar color
//   bgcolor	background color
//   max	    maximum possible value (default=100, when value > max, max=value)   
//   value	    current value (REQUIRED)
//   size	    Bar size 
//   label      label leftside of bar
//   labelsize  labelsize
//   perc	    If true then a percentage is displayed
//   height	    Bar height
// EXAMPLE:
//
// {GAUGE(perc=>true,label=>happy users,labelsize=>90,value=>35,bgcolor=>#EEEEEE,height=>20)}happy users over total{GAUGE}

function wikiplugin_gauge_help() {
	return tra("Displays a graphical GAUGE").":<br />~np~{GAUGE(color=>,bgcolor=>,max=>,value=>,size=>,label=>,labelsize=>,perc=>,height=>)}".tra("description")."{GAUGE}~/np~";
}

function wikiplugin_gauge($data, $params) {
	extract ($params,EXTR_SKIP);

	if (!isset($value)) {
		return ("<b>missing value parameter for plugin</b><br />");
	}

	if (!isset($size)) {
		$size = 150;
	}

	if (!isset($height)) {
		$height = 14;
	}

	if (!isset($bgcolor)) {
		$bgcolor = '#0000FF';
	}

	if (!isset($color)) {
		$color = '#FF0000';
	}

	if (!isset($perc)) {
		$perc = false;
	}
	
	if (!isset($max) or !$max) {
	   $max = 100;
	} 
    if ($max < $value) {
        //	maximum exceeded then change color
        $color = '#0E0E0E';
        $maxexceeded = true;
    	$max = $value; 
    } else {
    	$maxexceeded = false;
    }
    	
	if (!isset($labelsize)) {
		$labelsize = 50;
	} 

	if (!isset($label)) {
		$label_td = '';
	} else {
        $label_td = '<td width="' . $labelsize . '">' . $label . '&nbsp;</td>'; 
    }

    if ($maxexceeded) {
		$perc_td = '<td align="right" width="55">*******</td>';
    } else {	
	    if ($perc) {
	    	$perc = number_format($value / $max * 100, 2);
            $perc_td ='<td align="right" width="55">&nbsp;' . $perc . '%</td>';
    	} else {
    		$perc = number_format($value, 2);
            $perc_td ='<td align="right" width="55">&nbsp;' . $perc . '</td>';
	    }
	}	

	$h_size = floor($value / $max * 100);
    $h_size_rest = 100-$h_size;

    if ($h_size == 100) {
        $h_td = '<td style="background-color:' . $color . ';">&nbsp;</td>';
        } else {
            if ($h_size_rest == 100) {
                $h_td = '<td style="background-color:' . $bgcolor . ';">&nbsp;</td>';
            } else {
                $h_td = '<td style="background-color:' . $color . ';" width="' . $h_size . '%' .'">&nbsp;</td>';
                $h_td .= '<td style="background-color:' . $bgcolor . ';" width="' . $h_size_rest .  '%' . '">&nbsp;</td>';
            }
        }


	$html  ='<table border="0" cellpadding="0" cellspacing="0"><tr>' . $label_td . '<td width="' . $size . '" height="' . $height . '">';
	$html .='<table border="0" cellpadding="0" cellspacing="0" width="100%"><tr>' . $h_td . '</tr></table>';
	$html .='</td>' . $perc_td . '<td>&nbsp;</td></tr>';

	if (!empty($data)) {
		$html .= '<tr><td><small>' . $data . '</small></td></tr>';
	}

	$html .= "</table>";
	return $html;
}

?>
