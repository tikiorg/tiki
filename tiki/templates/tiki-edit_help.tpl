{* $Header: /cvsroot/tikiwiki/tiki/templates/tiki-edit_help.tpl,v 1.55.2.5 2007-12-04 21:59:38 nkoth Exp $ *}
{* Show wiki syntax and plugins help *}
{* TODO: Add links to add samples to edit form *}

<div class="wiki-edithelp"  id='edithelpzone' >
<div id="wikihelp-tab">
{if count($plugins) ne 0 and !($wysiwyg ne 'y' and $prefs.wysiwyg_wiki_parsed ne 'y' and $prefs.wysiwyg_wiki_semi_parsed ne 'y')}
  <div style="text-align: right;">
    <a href="javascript:hide('wikihelp-tab');show('wikiplhelp-tab');">{tr}Show Plugins Help{/tr}</a>
    <a title="{tr}Close{/tr}" href="javascript:flip('edithelpzone');">[x]</a>
  </div>
{/if}
<br />

<p>{tr}For more information, please see <a href="{$prefs.helpurl}Wiki Page Editor">Wiki Page Editor</a>{/tr}</p>

{if $prefs.feature_wiki_paragraph_formatting eq 'y' }
<p>{tr}Because the Wiki paragraph formatting feature is on, all groups of non-blank lines are collected into paragraphs.  Lines can be of any length, and will be wrapped together with the next line.  Paragraphs are separated by blank lines.{/tr}</p>
{else}
<p>{tr}Because the Wiki paragraph formatting feature is off, each line will be presented as you write it.  This means that if you want paragraphs to be wrapped properly, a paragraph should be all together on one line.{/tr}</p>
{/if}
{if $wysiwyg ne 'y' or ($wysiwyg eq 'y' and $prefs.wysiwyg_wiki_parsed eq 'y')}
{* show quicktags in help *}
<table width="95%" class="normal">
 <tr>
  <th>{tr}Quicktag{/tr}</th>
  <th>{tr}Description{/tr}</th>
  <th>{tr}Wiki Syntax{/tr}</th>
 </tr>
{section name=qtg loop=$quicktags}
 <tr>
  <td><img src='{$quicktags[qtg].tagicon}' alt='{tr}{$quicktags[qtg].taglabel}{/tr}' /></td>
  <td>{tr}{$quicktags[qtg].taglabel}{/tr}</td>
  <td>{$quicktags[qtg].taginsert|escape}</td>
 </tr>
{/section}
  </td>
 </tr>
</table>
{/if}
<table width="100%">
 <tr><th colspan="2">{tr}Wiki Help{/tr}</th></tr>
{if $wysiwyg ne 'y' or ($wysiwyg eq 'y' and $prefs.wysiwyg_wiki_parsed eq 'y')}
<tr><td width="20%"><strong>{tr}Bold text{/tr}:</strong></td><td> 2 {tr}underscores{/tr} "_". {tr}Example{/tr}: __{tr}text{/tr}__</td></tr>
<tr><td width="20%"><strong>{tr}Italic text{/tr}:</strong></td><td> 2 {tr}single quotes{/tr} "'". {tr}Example{/tr}: ''{tr}text{/tr}''</td></tr>
<tr><td><strong>{tr}Centered text{/tr}:</strong></td><td> 2 {tr}colons{/tr} ":". {tr}Example{/tr}: ::{tr}some text{/tr}::</td></tr>
<tr><td><strong>{tr}Underlined text{/tr}:</strong></td><td> 3 {tr}equals{/tr} "=". {tr}Example{/tr}: ==={tr}text{/tr}===</td></tr>
<tr><td><strong>{tr}Text box{/tr}:</strong></td><td> {tr}One carat{/tr} "^". {tr}Creates a box with the data{/tr}. {tr}Example{/tr}: "^{tr}Box content{/tr}^"</td></tr>
<tr><td><strong>{tr}Title bar{/tr}:</strong></td><td> "-={tr}Title{/tr}=-" {tr}creates a title bar{/tr}.</td></tr>
<tr><td><strong>{tr}Colored text{/tr}:</strong></td><td> "~~#FFEE33:{tr}some text{/tr}~~" {tr}or{/tr}  "~~yellow:{tr}some text{/tr}~~". {tr}Will display using the indicated HTML color or color name{/tr}</td></tr>
<tr><td><strong>{tr}Lists{/tr}:</strong></td><td> * {tr}for bullet lists{/tr}, # {tr}for numbered lists{/tr}, ;{tr}Word{/tr}:{tr}definition{/tr} {tr}for definiton lists{/tr}</td></tr>
<tr><td><strong>{tr}Indentation{/tr}:</strong></td><td>+, ++ {tr}Creates an indentation for each plus(useful in list to continue at the same level){/tr}</td></tr>
<tr><td><strong>{tr}Headings{/tr}:</strong></td><td> "!", "!!", "!!!" {tr}make headings{/tr}</td></tr>
<tr><td><strong>{tr}Show/Hide{/tr}:</strong></td><td> "!+", "!!-" {tr}show/hide heading section. + (shown) or - (hidden) by default{/tr}.</td></tr>
<tr><td><strong>{tr}Autonumbered Headings{/tr}:</strong></td><td> "!#", "!!#", "!+#", "!-#" ... {tr}make autonumbered headings{/tr}</td></tr>
{if $prefs.feature_wiki_tables eq 'new'}
<tr><td><strong>{tr}Tables{/tr}:</strong></td><td> "||{tr}row{/tr}1-{tr}col{/tr}1|{tr}row{/tr}1-{tr}col{/tr}2|{tr}row{/tr}1-{tr}col{/tr}3<br />{tr}row{/tr}2-{tr}col{/tr}1|{tr}row{/tr}2-{tr}col{/tr}2|{tr}row{/tr}2-{tr}col{/tr}3||" {tr}creates a table{/tr}</td></tr>
{else}
<tr><td><strong>{tr}Tables{/tr}:</strong></td><td> "||{tr}row{/tr}1-{tr}col{/tr}1|{tr}row{/tr}1-{tr}col{/tr}2|{tr}row{/tr}1-{tr}col{/tr}3||{tr}row{/tr}2-{tr}col{/tr}1|{tr}row{/tr}2-{tr}col{/tr}2|{tr}row{/tr}2-{tr}col{/tr}3||" {tr}creates a table{/tr}</td></tr>
{/if}
<tr><td><strong>{tr}Horizontal rule{/tr}:</strong></td><td>"-<em></em>-<em></em>-<em></em>-" {tr}makes a horizontal rule{/tr}</td></tr>
{/if}{* wysiwyg *}

<tr><td><strong>{tr}Wiki References{/tr}:</strong></td><td> {tr}JoinCapitalizedWords or use{/tr} (({tr}page{/tr})) {tr}or{/tr} (({tr}page|desc{/tr})) {tr}for wiki references{/tr}, (({tr}page|#anchor{/tr})) {tr}or{/tr} (({tr}page|#anchor|desc{/tr})) {tr}for wiki heading/anchor references{/tr}, )){tr}SomeName{/tr}(( {tr}prevents referencing{/tr}</td></tr>
<tr><td><strong>{tr}External links{/tr}:</strong></td><td> {tr}use square brackets for an external link: [URL] or [URL|link_description] or [URL|description|nocache]  (that last form prevents the local Wiki from caching the page; please use that form for large pages!).{/tr}<br />{tr}For an external Wiki, use ExternalWikiName:PageName or ((External Wiki Name: Page Name)){/tr}</td></tr>
<tr><td><strong>{tr}Images{/tr}:</strong></td><td> "{literal}{{/literal}img src=http://example.com/foo.jpg width=200 height=100 align=center imalign=right link=http://www.yahoo.com desc=foo alt=txt usemap=name class=xyz}" {tr}displays an image{/tr} {tr}height width desc link and align are optional{/tr}</td></tr>
<tr><td><strong>{tr}Non cacheable images{/tr}:</strong></td><td> "{literal}{{/literal}img src=http://example.com/foo.jpg?nocache=1 width=200 height=100 align=center link=http://www.yahoo.com desc=foo}" {tr}displays an image{/tr} {tr}height width desc link and align are optional{/tr}</td></tr>
<tr><td><strong>{tr}Line break{/tr}:</strong></td><td>"%%%" {tr}(very useful especially in tables){/tr}</td></tr>
{if $prefs.feature_drawings eq 'y'}
<tr><td><strong>{tr}Drawings{/tr}:</strong></td><td> {literal}{{/literal}draw name=foo} {tr}creates the editable drawing foo{/tr}</td></tr>
{/if}
<tr><td><strong>{tr}Multi-page pages{/tr}:</strong></td><td>{tr}Use{/tr} ...page... {tr}to separate pages{/tr}</td></tr>
<tr><td><strong>{tr}Wiki File Attachments{/tr}:</strong></td><td> {literal}{{/literal}file name=file.ext desc="description text" page="wiki page name" showdesc=1} {tr}Creates a link to the named file.  If page is not given, the file must be attached to the current page.  If desc is not given, the file name is used for the link text, unless showdesc is used, which makes the file description be used for the link text.  If image=1 is given, the attachment is treated as an image and is displayed directly on the page; no link is generated.{/tr}</td></tr>
<tr><td><strong>{tr}RSS feeds{/tr}:</strong></td><td> "{literal}{{/literal}rss id=n max=m{literal}}{/literal}" {tr}displays rss feed with id=n maximum=m items{/tr}</td></tr>
<tr><td><strong>{tr}Dynamic content{/tr}:</strong></td><td> "{literal}{{/literal}content id=n}" {tr}Will be replaced by the actual value of the dynamic content block with id=n{/tr}</td></tr>
<tr><td><strong>{tr}Dynamic variables{/tr}:</strong></td><td> "%{tr}Name{/tr}%" {tr}Inserts an editable variable{/tr}</td></tr>
<tr><td><strong>{tr}Non parsed sections{/tr}:</strong></td><td> "~np~ {tr}data{/tr} ~/np~" {tr}Prevents wiki parsing of the enclosed data.{/tr}</td></tr>
<tr><td><strong>{tr}Preformated sections{/tr}:</strong></td><td> {tr}"~pp~ data ~/pp~" Displays preformated text/code; no Wiki processing is done inside these sections (as with np), and the spacing is fixed (no word wrapping is done).  "~pre~ data ~/pre~" also displayes preformatted text with fixed spacing, but wiki processing still occurs on the text.{/tr}</td></tr>
<tr><td><strong>{tr}Comments{/tr}:</strong></td><td> {tr}"~tc~ Tiki Comment ~/tc~" makes a Tiki comment.  It will be completely removed from the display, but saved in the file for future reference.  "~hc~ HTML Comment ~/hc~" makes an HTML comment.  It will be inserted as a comment in the output HTML; these are not normally displayed in browsers, but can be seen using "View Source" or similar.{/tr}</td></tr>
<tr><td><strong>{tr}Square Brackets{/tr}:</strong></td><td> {tr}Use [[foo] to show [foo].{/tr}</td></tr>
<tr><td><strong>{tr}Block Preformatting{/tr}:</strong></td><td> {tr}Indent text with any number of spaces to turn it into a monospaced block that still follows other Wiki formatting instructions. It will be indended with the same number of spaces that you used.  Note that this mode does not preserve exact spacing and line breaks; use ~pp~...~/pp~ for that.{/tr}</td></tr>
<tr><td><strong>{tr}Direction{/tr}:</strong></td><td>"{literal}{r2l}{/literal}", "{literal}{l2r}{/literal}", "{literal}{rm}{/literal}", "{literal}{lm}{/literal}"{tr}Insert resp. right-to-left and left-to-right text direction DIV (up to end of text) and markers for langages as arabic or hebrew.{/tr}</td></tr>
<tr><td><strong>{tr}Table of contents{/tr}</strong></td><td>{tr}"{literal}{toc}{/literal}", "{literal}{maketoc}{/literal}" prints out a table of contents for the current page based on structures (toc) or ! headings (maketoc){/tr}</td></tr>
<tr><td><strong>{tr}Misc{/tr}:</strong></td><td>"{literal}{cookie}, {poll}{/literal}"</td></tr>
</table>
</div>

{if count($plugins) ne 0}
<div id="wikiplhelp-tab" style="display:none;">
  <div style="text-align: right;">
    <a href="javascript:hide('wikiplhelp-tab');show('wikihelp-tab');">{tr}Show Text Formatting Rules{/tr}</a>
    <a title="{tr}Close{/tr}" href="javascript:flip('edithelpzone');">[x]</a>
  </div>
<br />

{tr}Note that plugin arguments can be enclosed with double quotes (&quot;); this allows them to contain , or = or &gt;{/tr}.

<table width="100%">
{section name=i loop=$plugins}
 <tr>
  <td width="20%"><code>{$plugins[i].name}</code></td>
  <td>{if $plugins[i].help eq ''}{tr}No description available{/tr}{else}{$plugins[i].help}{/if}</td>
 </tr>
{/section}
</table>
</div>
{/if}
</div>
