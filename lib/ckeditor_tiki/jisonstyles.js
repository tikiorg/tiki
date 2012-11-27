/*
 * $Id: tikistyles.js 39469 2012-01-12 21:13:48Z changi67 $
 * (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
 *
 * All Rights Reserved. See copyright.txt for details and a complete list of authors.
 * Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
 *
 * Ckeditor styles definition for Tiki 6
 */


CKEDITOR.stylesSet.add('tikistyles',[
	{
		name:'Title Bar',
		element:'div',
		attributes:{
			'class':'titlebar',
			'data-t': jisonSyntax.titleBar
		}
	},
	{
		name:'Simple Box',
		element:'div',
		attributes:{
			'class':'simplebox',
			'data-t': jisonSyntax.box
		}
	},
	{
		name:'Code',
		element:'code',
		attributes:{
			'class':'code',
			'data-t': jisonSyntax.code
		}
	},
	{
		name: 'Pre-formatted Text',
		element:'pre',
		attributes:{
			'data-t':jisonSyntax.preFormattedText
		}
	},
	{
		name: 'Bold',
		element:'strong',
		attributes:{
			'data-t':jisonSyntax.bold
		}
	},
	{
		name: 'Center',
		element:'div',
		attributes:{
			style:'text-align: center;',
			'data-t':jisonSyntax.center
		}
	},
	{
		name: 'No Parse',
		element:'span',
		attributes:{
			'class':'noParse',
			'data-t':jisonSyntax.noParse
		}
	},
	{
		name: 'Italic',
		element:'em',
		attributes:{
			'data-t':jisonSyntax.italic
		}
	},
	{
		name: 'Left to Right',
		element:'div',
		attributes:{
			dir:'ltr',
			'data-t':jisonSyntax.l2r
		}
	},
	{
		name: 'Right to Left',
		element:'div',
		attributes:{
			dir:'rtl',
			'data-t':jisonSyntax.r2l
		}
	},
	{
		name: 'Header 1',
		element:'h1',
		attributes:{
			'data-t':jisonSyntax.header
		}
	},
	{
		name: 'Header 2',
		element:'h2',
		attributes:{
			'data-t':jisonSyntax.header
		}
	},
	{
		name: 'Header 3',
		element:'h3',
		attributes:{
			'data-t':jisonSyntax.header
		}
	},
	{
		name: 'Header 4',
		element:'h4',
		attributes:{
			'data-t':jisonSyntax.header
		}
	},
	{
		name: 'Header 5',
		element:'h5',
		attributes:{
			'data-t':jisonSyntax.header
		}
	},
	{
		name: 'Header 6',
		element:'h6',
		attributes:{
			'data-t':jisonSyntax.header
		}
	},
	{
		name: 'Horizontal Row',
		element:'hr',
		attributes:{
			'data-t':jisonSyntax.horizontalRow
		}
	},
	{
		name: 'Unlink',
		element:'span',
		attributes:{
			'data-t':jisonSyntax.unlink
		}
	},
	{
		name: 'Strike',
		element:'strike',
		attributes:{
			'data-t':jisonSyntax.strike
		}
	},
	{
		name: 'Underscore',
		element:'u',
		attributes:{
			'data-t':jisonSyntax.underscore
		}
	},
	{
		name: 'Comment',
		element:'span',
		attributes:{
			'data-t':jisonSyntax.comment
		}
	}
]);

