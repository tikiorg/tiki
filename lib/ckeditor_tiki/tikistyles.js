/* 
 * $Id$
 * (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
 * 
 * All Rights Reserved. See copyright.txt for details and a complete list of authors.
 * Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
 * 
 * Ckeditor styles definition for Tiki 6
 */


/* If your changes here do not make any change in the wysiwyg editor, the reason is, ckeditor stores things in your browser cache.
 * Good news: most users can see your modifications, it's just you.
 * During the time when you wish to try changes here, uncomment the following line. You may need to reload a page with an open editor the first time.
 * Then comment it again because it kills ckeditor performance.
 */
/*
  CKEDITOR.timestamp= +new Date;
*/
CKEDITOR.stylesSet.add('tikistyles',
	[
	 {name:'Normal',element:'p'},
	 {name:'Title Bar',element:'div',attributes:{'class':'titlebar'}},
	 {name:'Simple Box',element:'div',attributes:{'class':'simplebox'}},
	 {name:'Code',element:'div',attributes:{'class':'code'}}
	]);

