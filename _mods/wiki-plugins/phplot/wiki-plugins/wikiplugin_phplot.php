<?php

// Plots a graph of the input data using PHPLOT's create_chart.php example script
//
// Parameters:
//   head -- the column header row
//
// Usage:
//   The data (and the head paramter) is given one row per line, with columns
//   separated by ~|~.
//
// Parameters:
//
// Mandatory:
// XSIZE_in=size x of the graph
// YSIZE_in=size y of the graph
// which_data_type=text-linear|linear-linear|function|linear-linear-error
// datarowM[N]=data on cell[M,N]
// which_plot_type=bars|lines|pie|linepoints|points|area
//
// Optional:
// which_dot=diamond|rect|circle|triangle|dot|line|halfline
// maxy_in=max height of graph in units of Y axis
// miny_in=min height of graph in units of Y axis
// ylbl=Y axis label
// xlbl=X axis label
// title=graph title
// which_vti=vertical tick increment
// which_hti=horizontal tick increment
// which_xap=x axis position
//
// {PHPLOT( head => header column 1 ~|~ header column 2 ~|~ header column 3 )}
// row 1 column 1 ~|~ row 1 column 2 ~|~ row 1 column 3
// row 2 column 1 ~|~ row 2 column 2 ~|~ row 2 column 3
// {PHPLOT}

function wikiplugin_phplot_help() {
	return tra("Plots a graph on the data using PHPlot").":<br />~np~{PHPLOT(which_data_type=>text-linear|linear-linear|function|linear-linear-error,which_plot_type=>bars|lines|pie|linepoints|points|area)}".tra("cells")."{PHPLOT}~/np~ - ''".tra("cells separated by ~|~")."''";
}

function wikiplugin_phplot($data, $params) {
	global $tikilib;

	// Constants. Must be customized to have the plugin installed
	// THIS ONE MUST BE SET BEFORE TRYING TO USE THE SCRIPT
	$PHPLOT_URL="http://www.phplot.com/new/create_chart.php";
	// Default X/Y sizes of draw
	$XSIZE=400;
	$YSIZE=300;
	
	$url="";
	// Parse the parameters
	$xspecified=0;$yspecified=0;
	foreach ($params as $paramname=>$value) {
		$url.=$paramname. "=" . $value ."&";
		$xespecified=($paramname=="XSIZE_in");
		$yespecified=($paramname=="YSIZE_in");
	}
	if (! ($xspecified)) { $url.="&XSIZE_in=" . $XSIZE; }
	if (! ($yspecified)) { $url.="&YSIZE_in=" . $YSIZE; }

	$lines = split("\n", $data);
	$lineno=0;
	foreach ($lines as $line) {
		$line = trim($line);

		if (strlen($line) > 0) {
			$parts = explode("~|~", $line);
			$row = "";
			$colno=0;
			foreach ($parts as $column) {
				$url.="&data_row" . $colno . "[" . $lineno ."]=" . $column;
				$colno++;
			}
			$lineno++;

		}
	}
	
	$wret="~np~ <img src=\"" . $PHPLOT_URL . "?" . $url . "\"> ~/np~";


	return $wret;
}

?>
