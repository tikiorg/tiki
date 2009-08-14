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

// SUMIF {{{1
function SUMIF( ifList, tests, sumList )
{
	var testArray = tests.constructor.toString().indexOf( 'Array' ) != -1;
	var total = 0;

	for (var index = 0; index < sumList.length && index < ifList.length; index++) {
		if (!isNaN(sumList[index]))
		{
			var addValue = false;
			
			if (testArray)
			{
				for (var testIndex = 0; testIndex < tests.length && !addValue; testIndex++)
				{
					addValue = (tests[testIndex] == ifList[index]);
				}
			}
			else
			{
				addValue = (tests == ifList[index]);
			}

			if (addValue)
			{
				total += sumList[index];
			}
		}
	}
	
	return total;
}

// Extra section for display formats {{{1

var display;
display = new Object;

display.currency = function( value, before, after )
{
	if( before == null ) before = '';
	if( after == null ) after = '';

	var strval = String( Math.round( value * 100 ) / 100 );

	if( strval.lastIndexOf( "." ) == -1 )
		strval += ".00";
	
	while( strval.length - strval.lastIndexOf( "." ) < 3 )
		strval += "0";

	return before + strval + after;
}

display.currency_us = function( value )
{
	return display.currency( value, '$' );
}
display.currency_ca = function( value )
{
	return display.currency( value, '', '$' );
}
