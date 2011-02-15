<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

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

function wikiplugin_gauge_info() {
	return array(
		'name' => tra('Gauge'),
		'documentation' => tra('PluginGauge'),
		'description' => tra('Displays a graphical gauge'),
		'prefs' => array('wikiplugin_gauge'),
		'body' => tra('description'),
		'params' => array(
			'value' => array(
				'required' => true,
				'name' => tra('Value'),
				'description' => tra('Current value to be represented by the gauge'),
				'default' => ''
			),
			'max' => array(
				'required' => false,
				'name' => tra('Maximum Value'),
				'description' => tra('Maximum possible value. Default: 100'),
				'default' => 100				
			),
			'label' => array(
				'required' => false,
				'name' => tra('Label'),
				'description' => tra('Label displayed on the left side of the gauge.'),
				'default' => ''
			),
			'color' => array(
				'required' => false,
				'name' => tra('Color'),
				'description' => tra('Main color of the gauge. Use HTML color codes or names.'),
				'default' => '#FF0000'
			),
			'bgcolor' => array(
				'required' => false,
				'name' => tra('Background Color'),
				'description' => tra('Background color of the gauge. Use HTML color codes or names.'),
				'default' => '#0000FF'
			),
			'size' => array(
				'required' => false,
				'name' => tra('Width'),
				'description' => tra('Bar width in pixels.'),
				'filter' => 'digits',
				'default' => 150
			),
			'labelsize' => array(
				'required' => false,
				'name' => tra('Label Width'),
				'description' => tra('Width in pixels allocated to the label.'),
				'filter' => 'digits',
				'default' => 50
			),
			'perc' => array(
				'required' => false,
				'name' => tra('Display Percentage'),
				'description' => tra('Set to true (Yes) to display a percentage of the maximum.'),
				'default' => false,
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Yes'), 'value' => true), 
					array('text' => tra('No'), 'value' => false)
				)
			),
			'showvalue' => array(
				'required' => false,
				'name' => tra('Display Value'),
				'description' => tra('Set to false (No) to hide the numeric value (shown by default).'),
				'default' => true,
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Yes'), 'value' => true), 
					array('text' => tra('No'), 'value' => false)
				)
			),
			'height' => array(
				'required' => false,
				'name' => tra('Height'),
				'description' => tra('Bar height in pixels.'),
				'filter' => 'digits',
				'default' => 14
			),
		),
	);
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

	if (!isset($showvalue)) {
		$showvalue = true;
	}
	
	if (!isset($perc)) {
		$perc = false;
	}

	if (!isset($showvalue)) {
		$showvalue = true;
	} else {
		$showvalue = (bool) $showvalue;
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
	$html .='</td>' . ($showvalue ? $perc_td : '') . '<td>&nbsp;</td></tr>';

	if (!empty($data)) {
		$html .= '<tr><td><small>' . $data . '</small></td></tr>';
	}

	$html .= "</table>";
	return $html;
}
