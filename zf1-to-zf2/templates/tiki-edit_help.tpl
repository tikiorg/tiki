{* $Id$ *}
{* \brief Show wiki syntax help
 * included by tiki-show_help.tpl via smarty_block_add_help()
 * TODO: Add links to add samples to edit form *}

<h3>{tr}Wiki Syntax{/tr}</h3>
{if $prefs.feature_help eq 'y'}
	{remarksbox type="info" title="{tr}More information{/tr}"}
		<a href="{$prefs.helpurl}Wiki+Page+Editor" target="tikihelp" class="tikihelp alert-link" title="{tr}Wiki Page Editor:{/tr} {tr}More help on editing wiki pages{/tr}">
			{tr}Wiki Page Editor{/tr}
		</a>
		{tr}and{/tr}
		<a href="{$prefs.helpurl}Wiki+Syntax" target="tikihelp" class="tikihelp alert-link" title="{tr}Wiki Syntax:{/tr} {tr}The syntax system used for creating pages in Tiki{/tr}">
			{tr}Wiki Syntax{/tr}
		</a>
	{/remarksbox}
{/if}
<table class="table table-condensed table-hover">
	<th>
		{tr}Wiki Syntax{/tr}
	</th>
	{if (!isset($wysiwyg) or $wysiwyg ne 'y') or (isset($wysiwyg) and $wysiwyg eq 'y' and $prefs.wysiwyg_wiki_parsed eq 'y')}
		<tr>
			<td>
				{icon name='bold'} <strong>{tr}Bold text{/tr}</strong> &nbsp;&nbsp;&nbsp; __{tr}text{/tr}__
			</td>
		</tr>
		<tr>
			<td>
				{icon name='italic'} <strong>{tr}Italic text{/tr}</strong> &nbsp;&nbsp;&nbsp; 2 {tr}single quotes{/tr} ('). &nbsp;&nbsp;&nbsp; '"{tr}text{/tr}"'
			</td>
		</tr>
		<tr>
			<td>
				{icon name='underline'} <strong>{tr}Underlined text{/tr}</strong> &nbsp;&nbsp;&nbsp; ==={tr}text{/tr}===
			</td>
		</tr>
		<tr>
			<td>
				{icon name='font' istyle='color:red'} <strong>{tr}Colored text{/tr}</strong> <br/> ~~#FFEE33:{tr}text{/tr}~~ {tr}or{/tr} ~~yellow:{tr}text{/tr}~~. {tr}Will display using the indicated HTML color or color name. Color name can contain two colors separated by a comma. In this case, the first color would be the foreground and the second one the background.{/tr}
			</td>
		</tr>
		<tr>
			<td>
				{icon name='strikethrough'} <strong>{tr}Deleted text{/tr}</strong> &nbsp;&nbsp;&nbsp; {tr}2 dashes{/tr} "-". &nbsp;&nbsp;&nbsp; --{tr}text{/tr}--
			</td>
		</tr>
		<tr>
			<td>
				{icon name='h1'} <strong>{tr}Headings{/tr}</strong> <br/> !heading1, !!heading2, !!!heading3
			</td>
		</tr>
		<tr>
			<td>
				<strong>{tr}Show/Hide{/tr}</strong> <br/> !+, !!- {tr}show/hide heading section. + (shown) or - (hidden) by default{/tr}.
			</td>
		</tr>
		<tr>
			<td>
				<strong>{tr}Autonumbered Headings{/tr}</strong> <br/> !#, !!#, !+#, !-# ...
			</td>
		</tr>
	{/if}{* wysiwyg *}
	<tr>
		<td>
			<strong>{tr}Table of contents{/tr}</strong> <br/>{tr}{literal}{toc}{/literal}, {literal}{maketoc}{/literal} prints out a table of contents for the current page based on structures (toc) or ! headings (maketoc){/tr}. {tr}Common optional parameters for maketoc are: title|maxdepth|levels|nums, and for toc are: order|showdesc|shownum|structId|maxdepth|pagename.{/tr}
		</td>
	</tr>
	{if (!isset($wysiwyg) or $wysiwyg ne 'y') or (isset($wysiwyg) and $wysiwyg eq 'y' and $prefs.wysiwyg_wiki_parsed eq 'y')}
		<tr>
			<td>
				{icon name='horizontal-rule'} <strong>{tr}Horizontal rule{/tr}</strong> &nbsp;&nbsp;&nbsp; -<em></em>-<em></em>-<em></em>-
			</td>
		</tr>
		<tr>
			<td>
				{icon name='box'} <strong>{tr}Text box{/tr}</strong> &nbsp;&nbsp;&nbsp; ^{tr}Box content{/tr}^
			</td>
		</tr>
		<tr>
			<td>
				{icon name='align-center'} <strong>{tr}Centered text{/tr}</strong> &nbsp;&nbsp;&nbsp; {if $prefs.feature_use_three_colon_centertag eq 'y'}:::{tr}text{/tr}:::{else}::{tr}text{/tr}::{/if}
			</td>
		</tr>
	{/if}{* wysiwyg *}
	<tr>
		<td>
			{icon name='cog'} <strong>{tr}Dynamic variables{/tr}</strong> <br/> %{tr}Name{/tr}% {tr}Inserts an editable variable{/tr}
		</td>
	</tr>
	<tr>
		<td>
			{icon name='link-external'} <strong>{tr}External links{/tr}</strong> <br/> {tr}use square brackets for an external link: [URL], [URL|link_description],[URL|link_description|relation] or [URL|description|relation|nocache] (that last prevents the local Wiki from caching the linked page; relation can be used to insert rel attribute for the link - useful e.g. for shadowbox).{/tr}<br>{tr}For an external Wiki, use ExternalWikiName:PageName or ((External Wiki Name: Page Name)){/tr}
		</td>
	</tr>
	<tr>
		<td>
			<strong>{tr}Square Brackets{/tr}</strong> <br/> {tr}Use [[foo] to show [foo].{/tr}
		</td>
	</tr>
	<tr>
		<td>
			{icon name='link'} <strong>{tr}Wiki References{/tr}</strong> <br/> {if $prefs.feature_wikiwords eq 'y'}{tr}JoinCapitalizedWords or use{/tr} {/if}(({tr}page{/tr})) {tr}or{/tr} (({tr}page|description{/tr})) {tr}for wiki references{/tr}
			{if $prefs.wikiplugin_alink eq 'y'}
			, (({tr}page|#anchor{/tr})) {tr}or{/tr} (({tr}page|#anchor|desc{/tr})) {tr}for wiki heading/anchor references{/tr}
			{/if}
			{if $prefs.feature_wikiwords eq 'y'}, )){tr}SomeName{/tr}(( {tr}prevents referencing{/tr}{/if}
		</td>
	</tr>
	{if (!isset($wysiwyg) or $wysiwyg ne 'y') or (isset($wysiwyg) and $wysiwyg eq 'y' and $prefs.wysiwyg_wiki_parsed eq 'y')}
		<tr>
			<td>
				{icon name='list'} {icon name='list-numbered'} <strong>{tr}Lists{/tr}</strong> <br> * {tr}for bullet lists{/tr}, # {tr}for numbered lists{/tr}, ;{tr}Word:{/tr}{tr}definition{/tr} {tr}for definiton lists{/tr}
			</td>
		</tr>
		<tr>
			<td>
				<strong>{tr}Indentation{/tr}</strong> <br/>+, ++ {tr}Creates an indentation for each plus (useful in list to continue at the same level){/tr}
			</td>
		</tr>
		{if $prefs.feature_wiki_tables eq 'new'}
			<tr>
				<td>
					{icon name='table'} <strong>{tr}Tables{/tr}</strong> <br/> || {tr}row{/tr}1-{tr}col{/tr}1 | {tr}row{/tr}1-{tr}col{/tr}2 | {tr}row{/tr}1-{tr}col{/tr}3<br>{tr}row{/tr}2-{tr}col{/tr}1 | {tr}row{/tr}2-{tr}col{/tr}2 | {tr}row{/tr}2-{tr}col{/tr}3 ||
				</td>
			</tr>
		{else}
			<tr>
				<td>
					{icon name='table'} <strong>{tr}Tables{/tr}</strong> <br/> ||{tr}row{/tr}1-{tr}col{/tr}1|{tr}row{/tr}1-{tr}col{/tr}2|{tr}row{/tr}1-{tr}col{/tr}3||{tr}row{/tr}2-{tr}col{/tr}1|{tr}row{/tr}2-{tr}col{/tr}2|{tr}row{/tr}2-{tr}col{/tr}3||
				</td>
			</tr>
		{/if}
		<tr>
			<td>
				{icon name='title'} <strong>{tr}Title bar{/tr}</strong> &nbsp;&nbsp;&nbsp; -={tr}Title{/tr}=-
			</td>
		</tr>
		<tr>
			<td>
				<strong>{tr}Monospace font{/tr}</strong> &nbsp;&nbsp;&nbsp; -+{tr}Code sample{/tr}+-
			</td>
		</tr>
	{/if}{* wysiwyg *}
	<tr>
		<td>
			<strong>{tr}Line break{/tr}</strong> <br/>%%% {tr}(very useful especially in tables){/tr}
		</td>
	</tr>
	<tr>
		<td>
			<strong>{tr}Multi-page pages{/tr}</strong> <br/>{tr}Use{/tr} ...page... {tr}to separate pages{/tr}
		</td>
	</tr>
	<tr>
		<td>
			<strong>{tr}Non parsed sections{/tr}</strong> <br/> ~np~ {tr}data{/tr} ~/np~ {tr}Prevents wiki parsing of the enclosed data.{/tr}
		</td>
	</tr>
	<tr>
		<td>
			<strong>{tr}Preformated sections{/tr}</strong> <br/> {tr}~pp~ data ~/pp~ Displays preformated text/code; no Wiki processing is done inside these sections (as with np), and the spacing is fixed (no word wrapping is done). ~pre~ data ~/pre~ also displayes preformatted text with fixed spacing, but wiki processing still occurs on the text.{/tr}
		</td>
	</tr>
	<tr>
		<td>
			<strong>{tr}Comments{/tr}</strong> <br/> {tr}~tc~ Tiki Comment ~/tc~ makes a Tiki comment. It will be completely removed from the display, but saved in the file for future reference. ~hc~ HTML Comment ~/hc~ makes an HTML comment. It will be inserted as a comment in the output HTML; these are not normally displayed in browsers, but can be seen using "View Source" or similar.{/tr}
		</td>
	</tr>
	{if $prefs.feature_wiki_monosp eq 'y'}
		<tr>
			<td>
				<strong>{tr}Block Preformatting{/tr}</strong> <br/> {tr}Indent text with any number of spaces to turn it into a monospaced block that still follows other Wiki formatting instructions. It will be indended with the same number of spaces that you used. Note that this mode does not preserve exact spacing and line breaks; use ~pp~...~/pp~ for that.{/tr}
			</td>
		</tr>
	{/if}
	<tr>
		<td>
			<strong>{tr}Direction{/tr}</strong> <br/>{literal}{r2l}{/literal}, {literal}{l2r}{/literal}, {literal}{rm}{/literal}, {literal}{lm}{/literal}{tr}Insert resp. right-to-left and left-to-right text direction DIV (up to end of text) and markers for langages as arabic or hebrew.{/tr}
		</td>
	</tr>
	<tr>
		<td>
			<strong>{tr}Special characters{/tr}</strong> <br/>
			{literal}~hs~{/literal} {tr}hard space{/tr},
			{literal}~c~{/literal} &copy;,
			{literal}~amp~{/literal} &amp;,
			{literal}~lt~{/literal} &lt;,
			{literal}~gt~{/literal} &gt;,
			{literal}~ldq~{/literal} &ldquo;,
			{literal}~rdq~{/literal} &rdquo;,
			{literal}~lsq~{/literal} &lsquo;,
			{literal}~rsq~{/literal} &rsquo;,
			{literal}~--~{/literal} &mdash;,
			{literal}~bs~{/literal} &#92;,
			{tr}numeric between ~ for HTML numeric characters entity{/tr}
		</td>
	</tr>
</table>

{if $prefs.feature_wiki_paragraph_formatting eq 'y'}
	{remarksbox type="info" title="{tr}Note{/tr}" close="n"}
		{tr}Because the Wiki paragraph formatting feature is on, all groups of non-blank lines are collected into paragraphs. Lines can be of any length, and will be wrapped together with the next line. Paragraphs are separated by blank lines.{/tr}
	{/remarksbox}
{else}
	{remarksbox type="info" title="{tr}Note{/tr}" close="n"}
		{tr}Because the Wiki paragraph formatting feature is off, each line will be presented as you write it. This means that if you want paragraphs to be wrapped properly, a paragraph should be all together on one line.{/tr}
	{/remarksbox}
{/if}
