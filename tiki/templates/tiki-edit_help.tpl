{* $Header: /cvsroot/tikiwiki/tiki/templates/tiki-edit_help.tpl,v 1.18 2004-02-25 15:31:49 musus Exp $ *}
{* Show wiki syntax and plugins help *}
{* TODO: Add links to add samples to edit form *}

<div class="wiki-edithelp"  id="edithelpzone">
	<div id="help-bar">
		{if $feature_drawings eq 'y'}
			<span class="button2"><a class="linkbut" href="javascript:
hide('helpdynamic');
hide('helpformat');
hide('helpimg');
hide('helplinks');
hide('helplists');
hide('helpmisc');
hide('helptboxes');
hide('helpplugins');
show('helpdraw');">{tr}Drawings{/tr}</a></span>
		{/if}
		<span class="button2"><a class="linkbut" href="javascript:
hide('helpformat');
hide('helpimg');
hide('helplinks');
hide('helplists');
hide('helpmisc');
hide('helptboxes');
hide('helpplugins');
hide('helpdraw');
show('helpdynamic');">{tr}Dynamic content{/tr}</a></span>
<span class="button2"><a class="linkbut" href="javascript:
hide('helpimg');
hide('helplinks');
hide('helplists');
hide('helpmisc');
hide('helptboxes');
hide('helpplugins');
hide('helpdraw');
hide('helpdynamic');
show('helpformat');">{tr}Formatting{/tr}</a></span>
<span class="button2"><a class="linkbut" href="javascript:
hide('helpformat');
hide('helplinks');
hide('helplists');
hide('helpmisc');
hide('helptboxes');
hide('helpplugins');
hide('helpdraw');
hide('helpdynamic');
show('helpimg');">{tr}Images{/tr}</a></span>
<span class="button2"><a class="linkbut" href="javascript:
hide('helpformat');
hide('helpimg');
hide('helplists');
hide('helpmisc');
hide('helptboxes');
hide('helpplugins');
hide('helpdraw');
hide('helpdynamic');
show('helplinks');">{tr}Links{/tr}</a></span>
<span class="button2"><a class="linkbut" href="javascript:
hide('helpformat');
hide('helpimg');
hide('helplinks');
hide('helpmisc');
hide('helptboxes');
hide('helpplugins');
hide('helpdraw');
hide('helpdynamic');
show('helplists');">{tr}Lists{/tr}</a></span>
<span class="button2"><a class="linkbut" href="javascript:
hide('helpformat');
hide('helpimg');
hide('helplinks');
hide('helplists');
hide('helptboxes');
hide('helpplugins');
hide('helpdraw');
hide('helpdynamic');
show('helpmisc');">{tr}Misc{/tr}</a></span>
{if count($plugins) ne 0}
<span class="button2"><a class="linkbut" href="javascript:
hide('helpformat');
hide('helpimg');
hide('helplinks');
hide('helplists');
hide('helpdraw');
hide('helpmisc');
hide('helpdynamic');
hide('helptboxes');
show('helpplugins');">{tr}Plugins{/tr}</a></span>
{/if}
<span class="button2"><a class="linkbut" href="javascript:
hide('helpformat');
hide('helpimg');
hide('helplinks');
hide('helplists');
hide('helpdraw');
hide('helpmisc');
hide('helpdynamic');
hide('helpplugins');
show('helptboxes');">{tr}Tables/boxes{/tr}</a></span>
<span class="button2"><a class="linkbut" title="{tr}Close{/tr}" href="javascript:flip('edithelpzone');">{tr}X{/tr}</a></span>
</div> {* end helpbar *}
	{if $feature_drawings eq 'y'}
		<div id="helpdraw">
			{literal}{{/literal}draw name=foo}  creates the editable drawing foo
		</div>
	{/if}
	<div id="helpformat">
		Text Color<br />
		<div class="simplebox"><font color="blue">Changing the color of wiki text is as easy as 1,2,3.  First, 
		select 
</font></div>
		Italics<br />
		<div class="simplebox">{tr}To{/tr} <i>{tr}italicize{/tr}</i> {tr}a word or phrase using Wiki syntax, simply surround it with two single quotes (apostrophes), like{/tr} ''<i>{tr}this{/tr}</i>''. {tr}If you wish to italicize an entire phrase, just{/tr} ''<i>{tr}surround the entire phrase{/tr}</i>''. 
		{tr}Note: insert note about italics not carrying over line breaks.{/tr}<br />
		{tr}Syntax: ''italics''{/tr}<br />
		{tr}Example: "My friend Jane was ''very'' excited to get her new car."{/tr}<br />
		{tr}Displayed: "My friend Jane was <i>very</i> excited to get her new car."{/tr}</div>

		{tr}Underlining{/tr}
		<div class="simplebox"><u>{tr}Underlining{/tr}</u> {tr}text is similar to{/tr} <i>{tr}italicizing{/tr}</i> text. 
		{tr}You can underline an entire phrase by{/tr} ===<u>{tr}surrounding it with three equals signs on either side{/tr}</u>===, 
		{tr}or just a single{/tr} ===<u>{tr}word{/tr}</u>===.<br />
		{tr}Syntax: ===underlined==={/tr}<br />
		{tr}Example: "My friend Scott is ===very=== nervous about his date on Friday."{/tr}<br />
		Displayed: "My friend Scott is <u>very</u> nervous about his date on Friday."</div>

		{tr}Bold{/tr}
		<div class="simplebox"><b>{tr}Bolding{/tr}</b> {tr}text is (surprisingly enough) very similiar to{/tr} <i>{tr}italicizing{/tr}</i> 
		&amp; <u>{tr}underlining{/tr}</u>. {tr}To make text bold, surround the{/tr} __<b>{tr}word{/tr}</b>__ {tr}or phrase with{/tr} 
		__<b>{tr}two underscores on either side{/tr}</b>__.
		{tr}Syntax: __bold__{/tr}<br />
		{tr}Example: "__When__ are we going to the movies?"{/tr}<br />
		{tr}Displayed:{/tr} "<b>{tr}When{/tr}</b> {tr}are we going to the movies?"{/tr}</div>

		{tr}Combinations of Tags{/tr}
		<div class="simplebox">{tr}Tikiwiki gives you even more choices by allowing you to simply combine tags to form different formatting combinations. For example,{/tr} ''__<i><b>{tr}this text is both italicized &amp; bold{/tr}</b></i>__''. __===<b><u>{tr}This text is both bold &amp; underlined{/tr}</u></b>===__. ''__<i><u>{tr}This text is both italicized &amp; underlined{/tr}</u></i>__''.</div>
		{tr}Colored text{/tr}
		<div class="simplebox">
			~~#FFEE33:{tr}your text{/tr}~~ Will display using the indicated HTML color
		::{tr}your text{/tr}:: Will display the text centered
	</div></div>
	<div id="helpimg">
		{tr}Images{/tr}
		<div class="simplebox">
			"img src=http://example.com/foo.jpg width=200 height=100 align=center link=http://www.yahoo.com desc=foo" 
			displays an image. height, width, desc, link, and alignment are optional
		</div>

		{tr}Non-cacheable images{/tr}
		<div class="simplebox">
			img src=http://example.com/foo.jpg?nocache=1 width=200 height=100 align=center link=http://www.yahoo.com desc=foo 
			displays an image. height, width, desc, link, and align are optional
		</div>
	</div>
	<div id="helplinks">
		{tr}WikiLinks{/tr}
		<div class="simplebox">
		(({tr}WikiPageName{/tr})) would automatically link to the wiki page called "WikiPageName" with the text of the link displayed as, "WikiPageName"
		(({tr}WikiPageName|description{/tr})) would automatically create the link for the page called "WikiPageName"for wiki references
		)){tr}SomeName{/tr}(( prevents referencing
		</div>

		{tr}External links{/tr}
		<div class="simplebox">
		{tr}use square brackets for an external link{/tr}: [URL]
		[URL|{tr}link_description{/tr}]
		[URL|{tr}description{/tr}|nocache]
		</div>
	</div>
	<div id="helplists">
		Lists:
		<div class="simplebox">
		* {tr}for bullet lists{/tr}, 
		# {tr}for numbered lists{/tr}, 
		;{tr}term{/tr}:{tr}definition{/tr} {tr}for definiton lists{/tr}
		</div>
	</div>
	<div id="helptboxes">
		{if $feature_wiki_tables eq 'new'}
			Tables:
			<div class="simplebox">
			"||row1-col1|row1-col2|row1-col3\nrow2-col1|row2-col2col3||"  creates a table
			</div>
		{else}
			Tables:
		<div class="simplebox">
			"||row1-col1|row1-col2|row1-col3||row2-col1|row2-col2col3||" {tr}creates a table{/tr}
		</div>
		{/if}
		Simple box:
		<div class="simplebox">
		"^{tr}Box content{/tr}^" Creates a box
		</div>
	</div>
	<div id="helpmisc">
		"!", "!!", "!!!" {tr}make_headings{/tr}, 
		"-<em></em>-<em></em>-<em></em>-" {tr}makes a horizontal rule{/tr} 
		"-={tr}title{/tr}=-" {tr}creates a title bar{/tr}
		use ...page... to separate pages
		Non parsed sections:
		"~np~ {tr}data{/tr} ~/np~" Prevents parsing data
	</div>
	<div id="helpdynamic">
		RSS feeds:
			rss id=n max=m  displays rss feed with id=n maximum=m items
		Dynamic variables:
			"%{tr}name{/tr}%" Inserts an editable variable
		Dynamic content:
			content id=n  Will be replaced by the actual value of the dynamic content block with id=n
	</div>
{if count($plugins) ne 0}
	<div id="helpplugins">
	<code>{$plugins[i].name}</code>{if $plugins[i].help eq ''}{tr}No description available{/tr}{else}{$plugins[i].help}{/if}
	</div>
{/if}
<div class="simplebox">
For more information, please see <a href="http://www.tikiwiki.org/tiki-index.php?page=WikiSyntax">WikiSyntax</a> on <a href="http://www.tikiwiki.org">TikiWiki.org</a>.
</div>
</div>
