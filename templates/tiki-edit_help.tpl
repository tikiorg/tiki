{* $Header: /cvsroot/tikiwiki/tiki/templates/tiki-edit_help.tpl,v 1.24 2004-05-25 00:56:17 rlpowell Exp $ *}
{* Show wiki syntax and plugins help *}
{* TODO: Add links to add samples to edit form *}

<div class="wiki-edithelp"  id='edithelpzone' >
<div id="wikihelp-tab">
{if count($plugins) ne 0}
  <div style="text-align: right;">
    <a href="javascript:hide('wikihelp-tab');show('wikiplhelp-tab');">{tr}Show Plugins Help{/tr}</a>
    <a title="{tr}Close{/tr}" href="javascript:flip('edithelpzone');">[x]</a>
  </div>
{/if}
<br />

<p>{tr}For more information, please see <a
href="http://www.tikiwiki.org/tiki-index.php?page=WikiSyntax">WikiSyntax</a>
on <a href="http://www.tikiwiki.org">TikiWiki.org</a>.{/tr}</p>

<table width="100%">
<tr><td width="20%"><strong>{tr}Emphasis{/tr}:</strong></td><td> '<strong></strong>' {tr}for{/tr} <em>{tr}italics{/tr}</em>, _<em></em>_ {tr}for{/tr} <strong>{tr}bold{/tr}</strong>, '<strong></strong>'_<em></em>_ {tr}for{/tr} <em><strong>{tr}both{/tr}</strong></em></td></tr>
<tr><td><strong>{tr}Lists{/tr}:</strong></td><td> * {tr}for bullet lists{/tr}, # {tr}for numbered lists{/tr}, ;{tr}term{/tr}:{tr}definition{/tr} {tr}for definiton lists{/tr}</td></tr>
<tr><td><strong>{tr}Wiki References{/tr}:</strong></td><td> {tr}JoinCapitalizedWords or use{/tr} (({tr}page{/tr})) {tr}or{/tr} (({tr}page|desc{/tr})) {tr}for wiki references{/tr}, )){tr}SomeName{/tr}(( {tr}prevents referencing{/tr}</td></tr>
<tr><td><strong>{tr}External links{/tr}:</strong></td><td> {tr}use square brackets for an external link: [URL] or [URL|link_description] or [URL|description|nocache]  (that last form prevents the local Wiki from caching the page; please use that form for large pages!).{/tr}</td></tr>
<tr><td><strong>{tr}Multi-page pages{/tr}:</strong></td><td>{tr}use ...page... to separate pages{/tr}</td></tr>
<tr><td><strong>{tr}Misc{/tr}:</strong></td><td> "!", "!!", "!!!" {tr}make_headings{/tr}, "-<em></em>-<em></em>-<em></em>-" {tr}makes a horizontal rule{/tr} "==={tr}text{/tr}===" {tr}underlines text{/tr} "%%%" {tr}make a line break{/tr}</td></tr>
<tr><td><strong>{tr}Title bar{/tr}:</strong></td><td> "-={tr}title{/tr}=-" {tr}creates a title bar{/tr}.</td></tr>
<tr><td><strong>{tr}Images{/tr}:</strong></td><td> "{literal}{{/literal}img src=http://example.com/foo.jpg width=200 height=100 align=center link=http://www.yahoo.com desc=foo}" {tr}displays an image{/tr} {tr}height width desc link and align are optional{/tr}</td></tr>
<tr><td><strong>{tr}Non cacheable images{/tr}:</strong></td><td> "{literal}{{/literal}img src=http://example.com/foo.jpg?nocache=1 width=200 height=100 align=center link=http://www.yahoo.com desc=foo}" {tr}displays an image{/tr} {tr}height width desc link and align are optional{/tr}</td></tr>
{if $feature_drawings eq 'y'}
<tr><td><strong>{tr}Drawings{/tr}:</strong></td><td> {literal}{{/literal}draw name=foo} {tr}creates an editable drawing named foo{/tr}</td></tr>
{/if}
{if $feature_wiki_tables eq 'new'}
<tr><td><strong>{tr}Tables{/tr}:</strong></td><td> "||row1-col1|row1-col2|row1-col3<br />row2-col1|row2-col2col3||" {tr}creates a table{/tr}</td></tr>
{else}
<tr><td><strong>{tr}Tables{/tr}:</strong></td><td> "||row1-col1|row1-col2|row1-col3||row2-col1|row2-col2col3||" {tr}creates a table{/tr}</td></tr>
{/if}
<tr><td><strong>{tr}RSS feeds{/tr}:</strong></td><td> "{literal}{{/literal}rss id=n max=m{literal}}{/literal}" {tr}displays rss feed with id=n maximum=m items{/tr}</td></tr>
<tr><td><strong>{tr}Simple box{/tr}:</strong></td><td> "^{tr}Box content{/tr}^" {tr}Creates a box with the data{/tr}</td></tr>
<tr><td><strong>{tr}Dynamic content{/tr}:</strong></td><td> "{literal}{{/literal}content id=n}" {tr}Will be replaced by the actual value of the dynamic content block with id=n{/tr}</td></tr>
<tr><td><strong>{tr}Colored text{/tr}:</strong></td><td> "~~#FFEE33:{tr}some text{/tr}~~" {tr}Will display using the indicated HTML color{/tr}</td></tr>
<tr><td><strong>{tr}Center{/tr}:</strong></td><td> "::{tr}some text{/tr}::" {tr}Will display the text centered{/tr}</td></tr>
<tr><td><strong>{tr}Non parsed sections{/tr}:</strong></td><td> "~np~ {tr}data{/tr} ~/np~" {tr}Prevents wiki parsing of the enclosed data.{/tr}</td></tr>
<tr><td><strong>{tr}Preformated sections{/tr}:</strong></td><td> "~pp~ {tr}data{/tr} ~/pp~" {tr}Displays preformated text/code; no Wiki processing is done inside these sections (as with np), and the spacing is fixed (no word wrapping is done).{/tr}</td></tr>
<tr><td><strong>{tr}Square Brackets{/tr}:</strong></td><td> {tr}Use [[foo] to show [foo].{/tr}</td></tr>
<tr><td><strong>{tr}Block Preformatting{/tr}:</strong></td><td> {tr}Indent text with any number of spaces to turn it into a monospaced block that still follows other Wiki formatting instructions. It will be indended with the same number of spaces that you used.  Note that this mode does not preserve exact spacing and line breaks; use ~pp~...~/pp~ for that.{/tr}</td></tr>
<tr><td><strong>{tr}Dynamic variables{/tr}:</strong></td><td> "%{tr}name{/tr}%" {tr}Inserts an editable variable{/tr}</td></tr>
<tr><td><strong>{tr}Insert Module Output{/tr}:</strong></td><td> {tr}{literal}{MODULE(module=>some_module)}text{MODULE}{/literal}  can be used to insert the output of module "some_module" into your Wiki page. See <a href="http://tikiwiki.org/tiki-index.php?page=PluginModule">PluginModule</a> for more information. {/tr}</td></tr>
<tr><td><strong>{tr}Rendering Program Code{/tr}:</strong></td><td> {tr}{literal}{CODE()}some code{CODE} {/literal} will render "some code" as program code. This plugin has other options; see <a href="http://tikiwiki.org/tiki-index.php?page=PluginCode">PluginCode</a>.{/tr}</td></tr>
<tr><td><strong>{tr}Direction{/tr}:</strong></td><td>"{literal}{r2l}{/literal}", "{literal}{l2r}{/literal}", "{literal}{rm}{/literal}", "{literal}{lm}{/literal}"</td></tr>
<tr><td><strong>{tr}Misc{/tr}:</strong></td><td>"{literal}{toc}{/literal}", "{tr}{literal}{maketoc}{/literal}" prints out a table of contents for the current page based on ! headings,{/tr} "{literal}{cookie}{/literal}"</td></tr>
</table>
</div>

{if count($plugins) ne 0}
<div id="wikiplhelp-tab" style="display:none;">
  <div style="text-align: right;">
    <a href="javascript:hide('wikiplhelp-tab');show('wikihelp-tab');">{tr}Show Text Formatting Rules{/tr}</a>
    <a title="{tr}Close{/tr}" href="javascript:flip('edithelpzone');">[x]</a>
  </div>
<br />

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
