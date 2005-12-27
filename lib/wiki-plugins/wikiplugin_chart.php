<?php
/* Tiki-Wiki plugin chart
 *
 * Displays a chart from a tikisheet.
 */
function wikiplugin_chart_help() {
	return tra("Chart").":<br />~np~{CHART(id=>, type=>, width=>, height=>, value=> )}".tra("title")."{CHART}~/np~";
}
function wikiplugin_chart($data, $params) {
	extract ($params,EXTR_SKIP);

	if (!isset($id))
		return ("<b>missing id parameter for plugin</b><br />");
		
	if (!isset($type))
		return ("<b>missing type parameter for plugin</b><br />");

	$params = array( "sheetId" => $id, "graphic" => $type, "title" => $data );
	switch( $type )
	{
	case 'PieChartGraphic':
		if( !isset( $value ) )
			return "<b>missing value parameter for plugin</b><br />";

		$params['series[value]'] = $value;
		break;
	
	default:
		$params['independant'] = isset( $independant ) ? $independant : 'horizontal';
		$params['horizontal'] = isset( $horizontal ) ? $horizontal : 'bottom';
		$params['vertical'] = isset( $vertical ) ? $vertical : 'left';

		if( !isset( $x ) )
			return "<b>missing x parameter for plugin</b><br />";

		$params['series[x]'] = $x;

		for( $i = 0; isset( ${'y' . $i} ); $i++ )
			$params['series[y' . $i . ']'] = ${'y' . $i};

		break;
	}
	
	$params['series[color]'] = isset( $color ) ? $color : '';
	$params['series[style]'] = isset( $style ) ? $style : '';
	$params['series[label]'] = isset( $label ) ? $label : '';
	
	if( function_exists( 'imagepng' ) )
	{
		if( !isset( $width ) )
			return "<b>missing width parameter for plugin</b><br />";
		if( !isset( $height ) )
			return "<b>missing height parameter for plugin</b><br />";

		$params['width'] = $width;
		$params['height'] = $height;
			
		$disp = '<img src="' . _wikiplugin_chart_uri( $params, 'PNG' ) . '"/>'; 
	}
	elseif( function_exists( 'image_jpeg' ) )
	{
		if( !isset( $width ) )
			return "<b>missing width parameter for plugin</b><br />";
		if( !isset( $height ) )
			return "<b>missing height parameter for plugin</b><br />";

		$params['width'] = $width;
		$params['height'] = $height;
			
		$disp = '<img src="' . _wikiplugin_chart_uri( $params, 'JPEG' ) . '"/>'; 
	}
	elseif( function_exists( 'pdf_new' ) )
		$disp = tra( "Chart as PDF" );
	elseif( function_exists( 'ps_new' ) )
		$disp = tra( "Chart as PostScript" );
	else
		return "<b>no valid renderer for plugin</b><br />";

	if( function_exists( 'pdf_new' ) )
	{
		$params['format'] = isset( $format ) ? $format : 'A4';
		$params['orientation'] = isset( $orientation ) ? $orientation : 'landscape';

		$disp = '<a href="' . _wikiplugin_chart_uri( $params, 'PDF' ) . '">' . $disp . '</a>';
	}
	elseif( function_exists( 'ps_new' ) )
	{
		$params['format'] = isset( $format ) ? $format : 'A4';
		$params['orientation'] = isset( $orientation ) ? $orientation : 'landscape';

		$disp = '<a href="' . _wikiplugin_chart_uri( $params, 'PS' ) . '">' . $disp . '</a>';
	}

	return $disp;
}

function _wikiplugin_chart_uri( $params, $renderer )
{
	$params['renderer'] = $renderer;
	$array = array();
	foreach( $params as $key => $value )
		$array[] = rawurlencode( $key ) . '=' . rawurlencode( $value );
	
	return 'tiki-graph_sheet.php?' . implode( '&', $array );
}

?>
