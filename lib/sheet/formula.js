/**
 * TikiSheet Client-side grid manipulation.
 * By Louis-Philippe Huberdeau
 * 2004
 */

// Aliases to JavaScript functions {{{1
alias = new Object;

alias.SQRT = "Math.sqrt";
// }}}1

// Defined functions {{{1

// AVG {{{2
AVG = function( list )
{
	return SUM( list ) / list.length;
}

// MAX {{{2
MAX = function( list )
{
	var max = 0;

	for( key in list )
		if( !isNaN( list[key] ) && list[key] > max )
			max = list[key];

	return max;
}

// MIN {{{2
MIN = function( list )
{
	var min = null;

	for( key in list )
		if( ( !isNaN( list[key] ) && list[key] < min ) || min == null )
			min = list[key];

	return min;
}

// SUM {{{2
SUM = function( list )
{
	var total = 0;

	for( key in list )
		if( !isNaN( list[key] ) )
			total += list[key];

	return total;
}

// }}}1
