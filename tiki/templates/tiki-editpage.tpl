{if $preview}
{include file="tiki-preview.tpl"}
{/if}
<h1>{tr}Edit{/tr}: {$page}</h1>
<form method="post" action="tiki-editpage.php">
<textarea class="wikiedit" name="edit" rows="22" cols="80" wrap="virtual">{$pagedata}</textarea>
<br/>
<div align="center">{tr}Comment{/tr}: <input size="50" class="wikitext" name="comment" value="{$commentdata}" /></div>
{if $tiki_p_use_HTML eq 'y'}
<div align="center">{tr}Allow HTML{/tr}: <input type="checkbox" name="allowhtml" {if $allowhtml eq 'y'}checked="checked"{/if}/></div>
{/if}
<div align="center">
<input type="hidden" name="page" value="{$page}" />
<input type="submit" class="wikiaction" name="preview" value="{tr}preview{/tr}" />
<input type="submit" class="wikiaction" name="save" value="{tr}save{/tr}" />
</div>
</form>
<br/>
<div class="wiki-edithelp">
<p>
<a class="wiki">{tr}TextFormattingRules{/tr}</a><br />
<strong>{tr}Emphasis{/tr}:</strong> '<strong></strong>' {tr}for{/tr} <em>{tr}italics{/tr}</em>, _<em></em>_ {tr}for{/tr} <strong>{tr}bold{/tr}</strong>, '<strong></strong>'_<em></em>_ {tr}for{/tr} <em><strong>{tr}both{/tr}</strong></em><br />
<strong>{tr}Lists{/tr}:</strong> * {tr}for bullet lists{/tr}, # {tr}for numbered lists{/tr}, ;{tr}term{/tr}:{tr}definition{/tr} {tr}for definiton lists{/tr}<br/> 
<strong>{tr}References{/tr}:</strong> {tr}JoinCapitalizedWords{/tr} {tr}or use square brackets for an{/tr} {tr}external link{/tr}: [URL] or [URL|{tr}link_description{/tr}].<br />
<strong>{tr}Misc{/tr}</strong> "!", "!!", "!!!" {tr}make_headings{/tr}, "-<em></em>-<em></em>-<em></em>-" {tr}makes a horizontal rule{/tr}<br />
<strong>{tr}Title_bar{/tr}</strong> "-={tr}title{/tr}=-" {tr}creates a title bar{/tr}.<br/>
<strong>{tr}Images{/tr}</strong> "{tr}img{/tr} src=http://example.com/foo.jpg width=200 height=100 align=center link=http://www.yahoo.com desc=foo}" {tr}displays an image{/tr} {tr}height width desc link and align are optional{/tr}</br/> 
<strong>{tr}Tables{/tr}</strong> "%row1-col1&row1-col2&row1-col3\row2-col1&row2-col2&row3-col3%" {tr}creates a table{/tr}
</p>
</div>
