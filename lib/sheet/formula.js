/**
 * TikiSheet Client-side grid manipulation.
 * By Louis-Philippe Huberdeau
 * 2004
 */

// AVG {{{1
function AVG( list )
{
	return SUM( list ) / list.length;
}

// MAX {{{1
function MAX( list )
{
	var max = 0;

	for( key in list )
		if( !isNaN( list[key] ) && list[key] > max )
			max = list[key];

	return max;
}

// MIN {{{1
function MIN( list )
{
	var min = null;

	for( key in list )
		if( ( !isNaN( list[key] ) && list[key] < min ) || min == null )
			min = list[key];

	return min;
}

function SQRT( value )
{
	return Math.sqrt( value );
}

// SUM {{{1
function SUM( list )
{
	var total = 0;

	for( key in list )
		if( !isNaN( list[key] ) )
			total += list[key];

	return total;
}

