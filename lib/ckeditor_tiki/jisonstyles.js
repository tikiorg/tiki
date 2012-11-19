/*
 * $Id: tikistyles.js 39469 2012-01-12 21:13:48Z changi67 $
 * (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
 *
 * All Rights Reserved. See copyright.txt for details and a complete list of authors.
 * Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
 *
 * Ckeditor styles definition for Tiki 6
 */


CKEDITOR.stylesSet.add('tikistyles',
	[
		//{name:'Normal',element:'p'},
		{
			name:'Title Bar',
			element:'div',
			attributes:{
				'class':'titlebar',
				'data-t':'tb',
				'data-i':'0'
			}
		},
		{name:'Simple Box',element:'div',attributes:{'class':'simplebox'}},
		{name:'Code',element:'div',attributes:{'class':'code'}}
		/*
		 "preFormattedText" =>           "pp",
		 "bold" =>                       "b",
		 "box" =>                        "bx",
		 "center" =>                     "c",
		 "noParse" =>                    "np",
		 "code" =>                       "cd",
		 "color" =>                      "clr",
		 "italic" =>                     "i",
		 "l2r" =>                        "l2r",
		 "r2l" =>                        "r2l",
		 "header" =>                     "hdr",
		 "horizontalRow" =>              "hr",
		 "listParent" =>                 "lp",
		 "listUnordered" =>              "lu",
		 "listOrdered" =>                "lh",
		 "listToggleUnordered" =>        "ltu",
		 "listToggleOrdered" =>          "lto",
		 "listBreak" =>                  "lb",
		 "listDefinitionParent" =>       "ldp",
		 "listDefinition" =>             "ld",
		 "listDefinitionDescription" =>  "ldd",
		 "listEmpty" =>                  "le",
		 "line" =>                       "ln",
		 "forcedLineEnd" =>              "fln",
		 "unlink" =>                     "ul",
		 "link" =>                       "l",
		 "linkWord" =>                   "lw",
		 "linkNp" =>                     "lnp",
		 "linkExternal" =>               "el",
		 "wikiLink" =>                   "wl",
		 "strike" =>                     "stk",
		 "doubleDash" =>                 "dd",
		 "table" =>                      "t",
		 "tableRow" =>                   "tr",
		 "tableData" =>                  "td",
		 "titleBar" =>                   "tb",
		 "underscore" =>                 "u",
		 "comment" =>                    "cm",
		 "plugin" =>                     "pl",
		*/
	]);

