{if $preview}
{include file="tiki-preview.tpl"}
{/if}
<h1>{tr}Edit{/tr}: {$page}</h1>
{if $page eq 'SandBox'}
<div class="wikitext">
{tr}The SandBox is a page where you can practice your editing skills, use the preview feature
to preview the appeareance of the page, no versions are stored for this page.{/tr}
</div>
{/if}
<form  method="post" action="tiki-editpage.php">
<div align="center">
[ <a class="link" href="javascript:setSomeElement('editwiki','__text here__');">b</a> |
<a class="link" href="javascript:setSomeElement('editwiki','||r1c1|r1c2||r2c1|r2c2||');">tbl</a> |
<a class="link" href="javascript:setSomeElement('editwiki','[http://|desc]');">a</a> |
<a class="link" href="javascript:setSomeElement('editwiki','!text');">h1</a> |
<a class="link" href="javascript:setSomeElement('editwiki','!!text');">h2</a> |
<a class="link" href="javascript:setSomeElement('editwiki','!!!text');">h3</a> |
<a class="link" href="javascript:setSomeElement('editwiki','-=text=-');">title</a> |
<a class="link" href="javascript:setSomeElement('editwiki','^text^');">box</a> |
<a class="link" href="javascript:setSomeElement('editwiki','{literal}{{/literal}rss id= }');">rss</a> |
<a class="link" href="javascript:setSomeElement('editwiki','{literal}{{/literal}content id= }');">dcs</a> |
<a class="link" href="javascript:setSomeElement('editwiki','---');">line</a> |
<a class="link" href="javascript:setSomeElement('editwiki','{literal}{{/literal}img src=?nocache=1 width= height= align= desc= link= }');">img nc</a> |
<a class="link" href="javascript:setSomeElement('editwiki','{literal}{{/literal}img src= width= height= align= desc= link= }');">img</a> ]
<!--<a class="link" href="javascript:setSomeElement('editwiki',"''text here''");">i</a>-->
<textarea id='editwiki' class="wikiedit" name="edit" rows="22" cols="80" wrap="virtual">{$pagedata}</textarea>
<br/>
{if $page ne 'SandBox'}
<div align="center">{tr}Comment{/tr}: <input size="50" class="wikitext" name="comment" value="{$commentdata}" /></div>
{/if}
{if $tiki_p_use_HTML eq 'y'}
<div align="center">{tr}Allow HTML{/tr}: <input type="checkbox" name="allowhtml" {if $allowhtml eq 'y'}checked="checked"{/if}/></div>
{/if}
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
<strong>{tr}References{/tr}:</strong> {tr}JoinCapitalizedWords{/tr} {tr}or use square brackets for an{/tr} {tr}external link{/tr}: [URL] or [URL|{tr}link_description{/tr}] or [URL|description|nocache].<br />
<strong>{tr}Misc{/tr}</strong> "!", "!!", "!!!" {tr}make_headings{/tr}, "-<em></em>-<em></em>-<em></em>-" {tr}makes a horizontal rule{/tr}<br />
<strong>{tr}Title_bar{/tr}</strong> "-={tr}title{/tr}=-" {tr}creates a title bar{/tr}.<br/>
<strong>{tr}Images{/tr}</strong> "{literal}{{/literal}img src=http://example.com/foo.jpg width=200 height=100 align=center link=http://www.yahoo.com desc=foo}" {tr}displays an image{/tr} {tr}height width desc link and align are optional{/tr}<br/> 
<strong>{tr}Non cacheable images{/tr}</strong> "{literal}{{/literal}img src=http://example.com/foo.jpg?nocache=1 width=200 height=100 align=center link=http://www.yahoo.com desc=foo}" {tr}displays an image{/tr} {tr}height width desc link and align are optional{/tr}<br/> 
<strong>{tr}Tables{/tr}</strong> "||row1-col1|row1-col2|row1-col3||row2-col1|row2-col2|row3-col3||" {tr}creates a table{/tr}<br/>
<strong>{tr}RSS feeds{/tr}</strong> "{literal}{{/literal}rss id=n max=m{literal}}{/literal}" {tr}displays rss feed with id=n maximum=m items{/tr}<br/>
<strong>{tr}Simple box{/tr}</strong> "^{tr}Box content{/tr}^" {tr}Creates a box with the data{/tr}<br/>
<strong>{tr}Dynamic content{/tr}</strong> "{literal}{{/literal}content id=n}" {tr}Will be replaced by the actual value of the dynamic content block with id=n{/tr}<br/>
</p>
</div>
