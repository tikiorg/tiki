{title help="Spreadsheet"}{$title|escape}{/title}

<p>
  {$description|escape}
</p>

{if ($mode eq 'graph')}
<h2>{tr}Select Graphic Type{/tr}</h2>
<form method="get" action="tiki-graph_sheet.php">
<input type="hidden" name="sheetId" value="{$sheetId}"/>
<table>
<tr>
	<td><input type="radio" name="graphic" id="g_pie" value="PieChartGraphic"/> <label for='g_pie'>{tr}Pie Chart{/tr}</label></td>
	<td><input type="radio" name="graphic" id="g_mline" value="MultilineGraphic"/> <label for='g_mline'>{tr}Multiline{/tr}</label></td>
	<td><input type="radio" name="graphic" id="g_mbar" value="MultibarGraphic"/> <label for='g_mbar'>{tr}Multibar{/tr}</label></td>
	<td><input type="radio" name="graphic" id="g_stack" value="BarStackGraphic"/> <label for='g_stack'>{tr}Bar Stack{/tr}</label></td>
</tr>
<tr>
	<td><label for="g_pie"><img src="img/graph/graph.pie.png" alt="Pie Chart"/></label></td>
	<td><label for="g_mline"><img src="img/graph/graph.multiline.png" alt="Multiline"/>
	<td><label for="g_mbar"><img src="img/graph/graph.multibar.png" alt="Multibar"/>
	<td><label for="g_stack"><img src="img/graph/graph.barstack.png" alt="Bar Stack"/>
</tr>
</table>
{if $haspdflib or $hasps}
<div>
	<select name="format">
		<option>Letter</option>
		<option>Legal</option>
		<option>A4</option>
		<option>A3</option>
	</select>
	<select name="orientation">
		<option value="landscape">{tr}Landscape{/tr}</option>
		<option value="portrait">{tr}Portrait{/tr}</option>
	</select>
{if $haspdflib}
	<input type="submit" name="renderer" value="PDF"/>
{/if}
{if $hasps}
	<input type="submit" name="renderer" value="PS"/>
{/if}
</div>
{/if}
{if $hasgd}
<div>
	<input type="text" name="width" value="500" size="4"/>
	<input type="text" name="height" value="400" size="4"/>
	<input type="submit" name="renderer" value="PNG"/>
	<input type="submit" name="renderer" value="JPEG"/>
</div>
{/if}
</form>
{/if}
{if ($mode eq 'param')}
{jq}
{literal}
function renderWikiPlugin()
{
	var div = document.getElementById( 'plugin-desc' );

	var params = [
		_renVal( 'id', 'sheetId' ),
		_renVal( 'type', 'graphic' ),
		_renVal( 'format', 'format' ),
		_renVal( 'orientation', 'orientation' ),
{/literal}
{if $showgridparam}
		_renValRad( 'independant', 'independant' ),
		_renValRad( 'vertical', 'vertical' ),
		_renValRad( 'horizontal', 'horizontal' ),
{/if}
{section name=i loop=$series}
		_renVal( '{$series[i]}', 'series[{$series[i]}]' ),
{/section}
{literal}
		_renVal( 'width', 'width' ),
		_renVal( 'height', 'height' )
	];

	div.innerHTML = "{CHART(" + params.join( ", " ) + ")}" + document.chartParam.title.value + "{CHART}";
}

function _renVal( dest, control )
{
	var val = document.chartParam[control].value;
	
	if( val.indexOf( "," ) != -1 )
		return dest + '=>"' + val + '"';
	else
		return dest + '=>' + val;
}

function _renValRad( name )
{
	var rads = document.chartParam[name];

	for( i = 0; rads.length > i; i++ )
		if( rads[i].checked )
			return name + '=>' + rads[i].value;
}
{/literal}
{/jq}

<form name="chartParam" method="get" action="tiki-graph_sheet.php">
<input type="hidden" name="sheetId" value="{$sheetId}"/>
<input type="hidden" name="graphic" value="{$graph}"/>
<input type="hidden" name="renderer" value="{$renderer}"/>
<input type="hidden" name="format" value="{$format}"/>
<input type="hidden" name="orientation" value="{$orientation}"/>
<input type="hidden" name="width" value="{$im_width}"/>
<input type="hidden" name="height" value="{$im_height}"/>
<table class="formcolor">
	<tr>
		<td>{tr}Title:{/tr}</td>
		<td><input type="text" name="title" value="{$title}" onchange="renderWikiPlugin()"/></td>
	</tr>
{if $showgridparam}
	<tr>
		<td>{tr}Independant Scale:{/tr}</td>
		<td>
			<input type="radio" name="independant" value="horizontal" id="ind_ori_hori" checked="checked" onchange="renderWikiPlugin()" />
			<label for="ind_ori_hori">{tr}Horizontal{/tr}</label>
			<input type="radio" name="independant" value="vertical" id="ind_ori_verti" onchange="renderWikiPlugin()" />
			<label for="ind_ori_verti">{tr}Vertical{/tr}</label>
		</td>
	</tr>
	<tr>
		<td>{tr}Horizontal Scale:{/tr}</td>
		<td>
			<input type="radio" name="horizontal" value="bottom" id="hori_pos_bottom" checked="checked" onchange="renderWikiPlugin()" />
			<label for="hori_pos_bottom">{tr}Bottom{/tr}</label>
			<input type="radio" name="horizontal" value="top" id="hori_pos_top" onchange="renderWikiPlugin()" />
			<label for="hori_pos_top">{tr}Top{/tr}</label>
		</td>
	</tr>
	<tr>
		<td>{tr}Vertical Scale:{/tr}</td>
		<td>
			<input type="radio" name="vertical" value="left" id="verti_pos_left" checked="checked" onchange="renderWikiPlugin()" />
			<label for="verti_pos_left">{tr}Left{/tr}</label>
			<input type="radio" name="vertical" value="right" id="verti_pos_right" onchange="renderWikiPlugin()" />
			<label for="verti_pos_right">{tr}Right{/tr}</label>
		</td>
	</tr>
{/if}
	<tr>
		<td colspan="2">{tr}Series:{/tr}</td>
	</tr>
{section name=i loop=$series}
	<tr>
		<td>{$series[i]}</td>
		<td><input type="text" name="series[{$series[i]}]" onchange="renderWikiPlugin()"/></td>
	</tr>
{/section}
	<tr>
		<td colspan="2"><input type="submit" value="{tr}Show{/tr}" /></td>
	</tr>
</table>
{$dataGrid}
</form>
<h2>{tr}Wiki plug-in{/tr}</h2>
<div id="plugin-desc"></div>
{/if}
