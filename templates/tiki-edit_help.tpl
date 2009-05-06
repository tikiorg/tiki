{* $Id$ *}
{** \brief Show wiki syntax and plugins help *}
{* TODO: Add links to add samples to edit form *}

{add_help show='n' title="{tr}Wiki Help{/tr}" id="wiki_help"}

{if $prefs.feature_help eq 'y'} 
<p>{tr}For more information, please see <a href="{$prefs.helpurl}Wiki+Page+Editor" target="tikihelp" class="tikihelp" title="{tr}Wiki Page Editor{/tr}: {tr}More help on editing wiki pages{/tr}">Wiki Page Editor</a>{/tr} & <a href="{$prefs.helpurl}Wiki+Syntax" target="tikihelp" class="tikihelp" title="{tr}Wiki Syntax{/tr}: {tr}The syntax system used for creating pages in TikiWiki{/tr}">{tr}Wiki Syntax{/tr}</a>
</p>
{/if}
 
<table width="95%" class="normal">
 <tr>
	<th>{tr}Format{/tr}</th>
	<th>{tr}Wiki Syntax{/tr}</th>
</tr>
{cycle values="odd,even" print=false}
{if $wysiwyg ne 'y' or ($wysiwyg eq 'y' and $prefs.wysiwyg_wiki_parsed eq 'y')}
<tr><td width="20%" class="{cycle advance=false}"><img src='pics/icons/text_bold.png' alt='' /> <strong>{tr}Bold text{/tr}</strong></td><td class="{cycle}"> 2 {tr}underscores{/tr} "_". {tr}Example{/tr} __{tr}text{/tr}__ = <strong>text</strong></td></tr>
<tr><td width="20%" class="{cycle advance=false}"><img src='pics/icons/text_italic.png' alt='' /> <strong>{tr}Italic text{/tr}</strong></td><td class="{cycle}"> 2 {tr}single quotes{/tr} "'".  {tr}Example{/tr} ''{tr}text{/tr}'' = <em>text</em></td></tr>
<tr><td class="{cycle advance=false}"><img src='pics/icons/text_align_center.png' alt='' /> <strong>{tr}Centered text{/tr}</strong></td><td class="{cycle}"> 2 {tr}colons{/tr} ":". {tr}Example{/tr} ::{tr}some text{/tr}::</td></tr>
<tr><td class="{cycle advance=false}"><img src='pics/icons/text_underline.png' alt='' /> <strong>{tr}Underlined text{/tr}</strong></td><td class="{cycle}"> 3 {tr}equals{/tr} "=". {tr}Example{/tr} ==={tr}text{/tr}===</td></tr>
<tr><td class="{cycle advance=false}"><img src='pics/icons/text_strikethrough.png' alt='' /> <strong>{tr}Deleted text{/tr}</strong></td><td class="{cycle}"> {tr}2 dashes{/tr} "-". {tr}Example{/tr} --{tr}text{/tr}--</td></tr>
<tr><td class="{cycle advance=false}"><img src='pics/icons/box.png' alt='' /> <strong>{tr}Text box{/tr}</strong></td><td class="{cycle}"> {tr}One carat{/tr} "^". {tr}Creates a box with the data{/tr}. {tr}Example{/tr} "^{tr}Box content{/tr}^"</td></tr>
<tr><td class="{cycle advance=false}"><img src='pics/icons/text_padding_top.png' alt='' /> <strong>{tr}Title bar{/tr}</strong></td><td class="{cycle}"> "-={tr}Title{/tr}=-" {tr}creates a title bar{/tr}.</td></tr>
<tr><td class="{cycle advance=false}"><img src='pics/icons/palette.png' alt='' /> <strong>{tr}Colored text{/tr}</strong></td><td class="{cycle}"> "~~#FFEE33:{tr}some text{/tr}~~" {tr}or{/tr}  "~~yellow:{tr}some text{/tr}~~". {tr}Will display using the indicated HTML color or color name. Color name can contain two colors separated by a comma. In this case, the first color would be the foreground and the second one the background.{/tr}</td></tr>
<tr><td width="20%" class="{cycle advance=false}"><strong>{tr}Monospace font{/tr}</strong></td><td class="{cycle}">-+{tr}Code sample{/tr}+- {tr}for{/tr} <code>Code sample</code></td></tr>
<tr><td class="{cycle advance=false}"><img src='pics/icons/text_list_bullets.png' alt='' /> <img src='pics/icons/text_list_numbers.png' alt='' /> <strong>{tr}Lists{/tr}</strong></td><td class="{cycle}"> * {tr}for bullet lists{/tr}, # {tr}for numbered lists{/tr}, ;{tr}Word{/tr}{tr}definition{/tr} {tr}for definiton lists{/tr}</td></tr>
<tr><td class="{cycle advance=false}"><strong>{tr}Indentation{/tr}</strong></td><td class="{cycle}">+, ++ {tr}Creates an indentation for each plus(useful in list to continue at the same level){/tr}</td></tr>
<tr><td class="{cycle advance=false}"><img src='pics/icons/text_heading_1.png' alt='' /><img src='pics/icons/text_heading_2.png' alt='' /><img src='pics/icons/text_heading_3.png' alt='' /> <strong>{tr}Headings{/tr}</strong></td><td class="{cycle}"> "!", "!!", "!!!" {tr}make headings{/tr}</td></tr>
<tr><td class="{cycle advance=false}"><strong>{tr}Show/Hide{/tr}</strong></td><td class="{cycle}"> "!+", "!!-" {tr}show/hide heading section. + (shown) or - (hidden) by default{/tr}.</td></tr>
<tr><td class="{cycle advance=false}"><strong>{tr}Autonumbered Headings{/tr}</strong></td><td class="{cycle}"> "!#", "!!#", "!+#", "!-#" ... {tr}make autonumbered headings{/tr}</td></tr>
{if $prefs.feature_wiki_tables eq 'new'}
<tr><td class="{cycle advance=false}"><img src='pics/icons/table.png' alt='' /> <strong>{tr}Tables{/tr}</strong></td><td class="{cycle}"> "||{tr}row{/tr}1-{tr}col{/tr}1|{tr}row{/tr}1-{tr}col{/tr}2|{tr}row{/tr}1-{tr}col{/tr}3<br />{tr}row{/tr}2-{tr}col{/tr}1|{tr}row{/tr}2-{tr}col{/tr}2|{tr}row{/tr}2-{tr}col{/tr}3||" {tr}creates a table{/tr}</td></tr>
{else}
<tr><td class="{cycle advance=false}"><img src='pics/icons/table.png' alt='' /> <strong>{tr}Tables{/tr}</strong></td><td class="{cycle}"> "||{tr}row{/tr}1-{tr}col{/tr}1|{tr}row{/tr}1-{tr}col{/tr}2|{tr}row{/tr}1-{tr}col{/tr}3||{tr}row{/tr}2-{tr}col{/tr}1|{tr}row{/tr}2-{tr}col{/tr}2|{tr}row{/tr}2-{tr}col{/tr}3||" {tr}creates a table{/tr}</td></tr>
{/if}
<tr><td class="{cycle advance=false}"><img src='pics/icons/page.png' alt='' /> <strong>{tr}Horizontal rule{/tr}</strong></td><td class="{cycle}">"-<em></em>-<em></em>-<em></em>-" {tr}makes a horizontal rule{/tr}</td></tr>
{/if}{* wysiwyg *}

<tr><td class="{cycle advance=false}"><img src='pics/icons/page_link.png' alt='' /> <strong>{tr}Wiki References{/tr}</strong></td><td class="{cycle}"> {tr}JoinCapitalizedWords or use{/tr} (({tr}page{/tr})) {tr}or{/tr} (({tr}page|desc{/tr})) {tr}for wiki references{/tr}, (({tr}page|#anchor{/tr})) {tr}or{/tr} (({tr}page|#anchor|desc{/tr})) {tr}for wiki heading/anchor references{/tr}, )){tr}SomeName{/tr}(( {tr}prevents referencing{/tr}</td></tr>
<tr><td class="{cycle advance=false}"><img src='pics/icons/world_link.png' alt='' /> <strong>{tr}External links{/tr}</strong></td><td class="{cycle}"> {tr}use square brackets for an external link: [URL], [URL|link_description],[URL|link_description|relation] or [URL|description|relation|nocache] (that last prevents the local Wiki from caching the linked page; relation can be used to insert rel attribute for the link - useful e.g. for shadowbox).{/tr}<br />
{tr}For an external Wiki, use ExternalWikiName:PageName or ((External Wiki Name: Page Name)){/tr}</td></tr>
<tr><td class="{cycle advance=false}"><img src='pics/icons/picture.png' alt='' /> <strong>{tr}Images{/tr}</strong></td><td class="{cycle}"> "{literal}{{/literal}img src=http://example.com/foo.jpg width=200 height=100 align=center imalign=right link=http://www.yahoo.com title='Yahoo Website' rel='shadowbox[gallery];type=img' desc=foo alt=txt usemap=name class=xyz}" {tr}displays an image{/tr} ({tr}imalign, height, width, desc, link, rel, title, usemap, class and align are optional{/tr})</td></tr>
<tr><td class="{cycle advance=false}"><strong>{tr}Non cacheable images{/tr}</strong></td><td class="{cycle}"> "{literal}{{/literal}img src=http://example.com/foo.jpg?nocache=1 width=200 height=100 align=center link=http://www.yahoo.com desc=foo}" {tr}displays an image{/tr} {tr}height width desc link and align are optional{/tr}</td></tr>
<tr><td class="{cycle advance=false}"><strong>{tr}Line break{/tr}</strong></td><td class="{cycle}">"%%%" {tr}(very useful especially in tables){/tr}</td></tr>
{if $prefs.feature_drawings eq 'y'}
<tr><td class="{cycle advance=false}"><strong>{tr}Drawings{/tr}</strong></td><td class="{cycle}"> {literal}{{/literal}draw name=foo} {tr}creates the editable drawing foo{/tr}</td></tr>
{/if}
<tr><td class="{cycle advance=false}"><strong>{tr}Multi-page pages{/tr}</strong></td><td class="{cycle}">{tr}Use{/tr} ...page... {tr}to separate pages{/tr}</td></tr>
<tr><td class="{cycle advance=false}"><strong>{tr}Wiki File Attachments{/tr}</strong></td><td class="{cycle}"> {literal}{{/literal}file name=file.ext desc="description text" page="wiki page name" showdesc=1} {tr}Creates a link to the named file.  If page is not given, the file must be attached to the current page.  If desc is not given, the file name is used for the link text, unless showdesc is used, which makes the file description be used for the link text.  If image=1 is given, the attachment is treated as an image and is displayed directly on the page; no link is generated.{/tr}</td></tr>
<tr><td class="{cycle advance=false}"><strong>{tr}RSS feeds{/tr}</strong></td><td class="{cycle}"> "{literal}{{/literal}rss id=n max=m{literal}}{/literal}" {tr}displays rss feed with id=n maximum=m items{/tr}</td></tr>
<tr><td class="{cycle advance=false}"><strong><img src='pics/icons/database_refresh.png' alt='' />{tr}Dynamic content{/tr}</strong></td><td class="{cycle}"> "{literal}{{/literal}content id=n}" {tr}Will be replaced by the actual value of the dynamic content block with id=n{/tr}</td></tr>
<tr><td class="{cycle advance=false}"><strong><img src='pics/icons/database_gear.png' alt='' />{tr}Dynamic variables{/tr}</strong></td><td class="{cycle}"> "%{tr}Name{/tr}%" {tr}Inserts an editable variable{/tr}</td></tr>
<tr><td class="{cycle advance=false}"><strong>{tr}Non parsed sections{/tr}</strong></td><td class="{cycle}"> "~np~ {tr}data{/tr} ~/np~" {tr}Prevents wiki parsing of the enclosed data.{/tr}</td></tr>
<tr><td class="{cycle advance=false}"><strong>{tr}Preformated sections{/tr}</strong></td><td class="{cycle}"> {tr}"~pp~ data ~/pp~" Displays preformated text/code; no Wiki processing is done inside these sections (as with np), and the spacing is fixed (no word wrapping is done).  "~pre~ data ~/pre~" also displayes preformatted text with fixed spacing, but wiki processing still occurs on the text.{/tr}</td></tr>
<tr><td class="{cycle advance=false}"><strong>{tr}Comments{/tr}</strong></td><td class="{cycle}"> {tr}"~tc~ Tiki Comment ~/tc~" makes a Tiki comment.  It will be completely removed from the display, but saved in the file for future reference.  "~hc~ HTML Comment ~/hc~" makes an HTML comment.  It will be inserted as a comment in the output HTML; these are not normally displayed in browsers, but can be seen using "View Source" or similar.{/tr}</td></tr>
<tr><td class="{cycle advance=false}"><strong>{tr}Square Brackets{/tr}</strong></td><td class="{cycle}"> {tr}Use [[foo] to show [foo].{/tr}</td></tr>
<tr><td class="{cycle advance=false}"><strong>{tr}Block Preformatting{/tr}</strong></td><td class="{cycle}"> {tr}Indent text with any number of spaces to turn it into a monospaced block that still follows other Wiki formatting instructions. It will be indended with the same number of spaces that you used.  Note that this mode does not preserve exact spacing and line breaks; use ~pp~...~/pp~ for that.{/tr}</td></tr>
<tr><td class="{cycle advance=false}"><strong>{tr}Direction{/tr}</strong></td><td class="{cycle}">"{literal}{r2l}{/literal}", "{literal}{l2r}{/literal}", "{literal}{rm}{/literal}", "{literal}{lm}{/literal}"{tr}Insert resp. right-to-left and left-to-right text direction DIV (up to end of text) and markers for langages as arabic or hebrew.{/tr}</td></tr>
<tr><td class="{cycle advance=false}"><strong>{tr}Table of contents{/tr}</strong></td><td class="{cycle}">{tr}"{literal}{toc}{/literal}", "{literal}{maketoc}{/literal}" prints out a table of contents for the current page based on structures (toc) or ! headings (maketoc){/tr}</td></tr>
<tr><td class="{cycle advance=false}"><strong>{tr}Special characters{/tr}</strong></td><td class="{cycle}">
"{literal}~hs~{/literal}" {tr}hard space{/tr},
"{literal}~c~{/literal}" &copy;,
"{literal}~amp~{/literal}" &amp;,
"{literal}~lt~{/literal}" &lt;,
"{literal}~gt~{/literal}" &gt;,
"{literal}~ldq~{/literal}" &ldquo;,
"{literal}~rdq~{/literal}" &rdquo;,
"{literal}~lsq~{/literal}" &lsquo;,
"{literal}~rsq~{/literal}" &rsquo;,
"{literal}~--~{/literal}" &mdash;,
"{literal}~bs~{/literal}" &#92;,
{tr}numeric between ~ for html numeric characters entity{/tr}</td></tr>
<tr><td class="{cycle advance=false}"><strong>{tr}Misc{/tr}</strong></td><td class="{cycle}">"{literal}{cookie}, {poll}{/literal}"</td></tr>
</table>

{if $prefs.feature_wiki_paragraph_formatting eq 'y' }
<p>{tr}Because the Wiki paragraph formatting feature is on, all groups of non-blank lines are collected into paragraphs.  Lines can be of any length, and will be wrapped together with the next line.  Paragraphs are separated by blank lines.{/tr}</p>
{else}
<p>{tr}Because the Wiki paragraph formatting feature is off, each line will be presented as you write it.  This means that if you want paragraphs to be wrapped properly, a paragraph should be all together on one line.{/tr}</p>
{/if}

{/add_help}

{if count($plugins) ne 0}
{add_help show='n' title="{tr}Plugin Help{/tr}" id="plugin_help"}
<h3>{tr}Plugins{/tr}{if $prefs.feature_help eq 'y'} <a href="{$prefs.helpurl}Plugins" target="tikihelp" class="tikihelp" title="{tr}Plugins{/tr}:{tr}Wiki plugins extend the function of wiki syntax with more specialized commands.{/tr}">{icon _id='help' style="vertical-align:middle"}</a>
{/if}</h3>
<p>{tr}Note that plugin arguments can be enclosed with double quotes (&quot;); this allows them to contain , or = or &gt;{/tr}.</p>

{if $tiki_p_admin eq 'y'}
<a href="tiki-admin.php?page=textarea" target="tikihelp" class="tikihelp">Activate/deactivate plugins</a>
{/if}

<br />
<table width="95%" class="normal">
	<tr><th>{tr}Description{/tr}</th></tr>
  {cycle values="even,odd" print=false}
  {section name=i loop=$plugins}
    <tr>
      <td class="{cycle advance=false}">
        {if $plugins[i].help eq ''}
          {tr}No description available{/tr}
        {else}
          {$plugins[i].help}
        {/if}
      </td>
    </tr>
  {cycle print=false}
  {/section}
</table>
{/add_help}
{/if}
