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
<table class="normal">
<tr><td class="formcolor">{tr}Quicklinks{/tr}</td><td class="formcolor">
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
<a class="link" href="javascript:setSomeElement('editwiki','::some::');">center</a> |
<a class="link" href="javascript:setSomeElement('editwiki','{literal}{{/literal}cookie}');">cookie</a> |
<a class="link" href="javascript:setSomeElement('editwiki','{literal}{{/literal}img src=?nocache=1 width= height= align= desc= link= }');">img nc</a> |
<a class="link" href="javascript:setSomeElement('editwiki','{literal}{{/literal}img src= width= height= align= desc= link= }');">img</a> ]
</td>
<tr><td class="formcolor">{tr}Smileys{/tr}</td><td class="formcolor">
<table>
     <tr>
          <td><a href="javascript:setSomeElement('editwiki','(:biggrin:)');"><img src="img/smiles/icon_biggrin.gif" alt="big grin" border="0" /></a></td>
          <td><a href="javascript:setSomeElement('editwiki','(:confused:)');"><img src="img/smiles/icon_confused.gif" alt="confused" border="0" /></a></td>
          <td><a href="javascript:setSomeElement('editwiki','(:cool:)');"><img src="img/smiles/icon_cool.gif" alt="cool" border="0" /></a></td>
          <td><a href="javascript:setSomeElement('editwiki','(:cry:)');"><img src="img/smiles/icon_cry.gif" alt="cry" border="0" /></a></td>
          <td><a href="javascript:setSomeElement('editwiki','(:eek:)');"><img src="img/smiles/icon_eek.gif" alt="eek" border="0" /></a></td>
          <td><a href="javascript:setSomeElement('editwiki','(:evil:)');"><img src="img/smiles/icon_evil.gif" alt="evil" border="0" /></a></td>
          <td><a href="javascript:setSomeElement('editwiki','(:exclaim:)');"><img src="img/smiles/icon_exclaim.gif" alt="exclaim" border="0" /></a></td>
          <td><a href="javascript:setSomeElement('editwiki','(:frown:)');"><img src="img/smiles/icon_frown.gif" alt="frown" border="0" /></a></td>
          <td><a href="javascript:setSomeElement('editwiki','(:idea:)');"><img src="img/smiles/icon_idea.gif" alt="idea" border="0" /></a></td>
          <td><a href="javascript:setSomeElement('editwiki','(:lol:)');"><img src="img/smiles/icon_lol.gif" alt="lol" border="0" /></a></td>
          <td><a href="javascript:setSomeElement('editwiki','(:mad:)');"><img src="img/smiles/icon_mad.gif" alt="mad" border="0" /></a></td>
          <td><a href="javascript:setSomeElement('editwiki','(:mrgreen:)');"><img src="img/smiles/icon_mrgreen.gif" alt="mr green" border="0" /></a></td>
          <td><a href="javascript:setSomeElement('editwiki','(:neutral:)');"><img src="img/smiles/icon_neutral.gif" alt="neutral" border="0" /></a></td>
          <td><a href="javascript:setSomeElement('editwiki','(:question:)');"><img src="img/smiles/icon_question.gif" alt="question" border="0" /></a></td>
          <td><a href="javascript:setSomeElement('editwiki','(:razz:)');"><img src="img/smiles/icon_razz.gif" alt="razz" border="0" /></a></td>
          <td><a href="javascript:setSomeElement('editwiki','(:redface:)');"><img src="img/smiles/icon_redface.gif" alt="redface" border="0" /></a></td>
          <td><a href="javascript:setSomeElement('editwiki','(:rolleyes:)');"><img src="img/smiles/icon_rolleyes.gif" alt="rolleyes" border="0" /></a></td>
          <td><a href="javascript:setSomeElement('editwiki','(:sad:)');"><img src="img/smiles/icon_sad.gif" alt="sad" border="0" /></a></td>
          <td><a href="javascript:setSomeElement('editwiki','(:smile:)');"><img src="img/smiles/icon_smile.gif" alt="smile" border="0" /></a></td>
          <td><a href="javascript:setSomeElement('editwiki','(:surprised:)');"><img src="img/smiles/icon_surprised.gif" alt="surprised" border="0" /></a></td>
          <td><a href="javascript:setSomeElement('editwiki','(:twisted:)');"><img src="img/smiles/icon_twisted.gif" alt="twisted" border="0" /></a></td>
          <td><a href="javascript:setSomeElement('editwiki','(:wink:)');"><img src="img/smiles/icon_wink.gif" alt="wink" border="0" /></a></td>
          <td><a href="javascript:setSomeElement('editwiki','(:arrow:)');"><img src="img/smiles/icon_arrow.gif" alt="arrow" border="0" /></a></td>
      </tr>    
      </table>
</td>
<!--<a class="link" href="javascript:setSomeElement('editwiki',"''text here''");">i</a>-->
<tr><td class="formcolor">{tr}edit{/tr}</td><td class="formcolor">
<textarea id='editwiki' class="wikiedit" name="edit" rows="22" cols="80" wrap="virtual">{$pagedata}</textarea>
</td>
{if $page ne 'SandBox'}
<tr><td class="formcolor">{tr}Comment{/tr}:</td><td class="formcolor"><input size="50" class="wikitext" name="comment" value="{$commentdata}" /></td>
{/if}
{if $tiki_p_use_HTML eq 'y'}
<tr><td class="formcolor">{tr}Allow HTML{/tr}: </td><td class="formcolor"><input type="checkbox" name="allowhtml" {if $allowhtml eq 'y'}checked="checked"{/if}/></td>
{/if}
{if $wiki_spellcheck eq 'y'}
<tr><td class="formcolor">{tr}Spellcheck{/tr}: </td><td class="formcolor"><input type="checkbox" name="spellcheck" {if $spellcheck eq 'y'}checked="checked"{/if}/></td>
{/if}
<input type="hidden" name="page" value="{$page}" />
<tr><td class="formcolor">&nbsp;</td><td class="formcolor"><input type="submit" class="wikiaction" name="preview" value="{tr}preview{/tr}" /></td>
<tr><td class="formcolor">&nbsp;</td><td class="formcolor"><input type="submit" class="wikiaction" name="save" value="{tr}save{/tr}" /></td>
</tr>
</table>
</form>
<br/>
<!--<a href="javascript:replaceSome('editwiki','foo','bar');">foo2bar</a>-->
<div class="wiki-edithelp">
<p>
<a class="wiki">{tr}TextFormattingRules{/tr}</a><br />
<strong>{tr}Emphasis{/tr}:</strong> '<strong></strong>' {tr}for{/tr} <em>{tr}italics{/tr}</em>, _<em></em>_ {tr}for{/tr} <strong>{tr}bold{/tr}</strong>, '<strong></strong>'_<em></em>_ {tr}for{/tr} <em><strong>{tr}both{/tr}</strong></em><br />
<strong>{tr}Lists{/tr}:</strong> * {tr}for bullet lists{/tr}, # {tr}for numbered lists{/tr}, ;{tr}term{/tr}:{tr}definition{/tr} {tr}for definiton lists{/tr}<br/> 
<strong>{tr}Wiki References{/tr}:</strong> {tr}JoinCapitalizedWords or use{/tr} ((page)) {tr}for wiki references{/tr} ))SomeName(( {tr}prevents referencing{/tr}<br/>
<strong>{tr}External links{/tr}:</strong> {tr}use square brackets for an{/tr} {tr}external link{/tr}: [URL] or [URL|{tr}link_description{/tr}] or [URL|description|nocache].<br />
<strong>{tr}Misc{/tr}</strong> "!", "!!", "!!!" {tr}make_headings{/tr}, "-<em></em>-<em></em>-<em></em>-" {tr}makes a horizontal rule{/tr}<br />
<strong>{tr}Title bar{/tr}</strong> "-={tr}title{/tr}=-" {tr}creates a title bar{/tr}.<br/>
<strong>{tr}Images{/tr}</strong> "{literal}{{/literal}img src=http://example.com/foo.jpg width=200 height=100 align=center link=http://www.yahoo.com desc=foo}" {tr}displays an image{/tr} {tr}height width desc link and align are optional{/tr}<br/> 
<strong>{tr}Non cacheable images{/tr}</strong> "{literal}{{/literal}img src=http://example.com/foo.jpg?nocache=1 width=200 height=100 align=center link=http://www.yahoo.com desc=foo}" {tr}displays an image{/tr} {tr}height width desc link and align are optional{/tr}<br/> 
<strong>{tr}Tables{/tr}</strong> "||row1-col1|row1-col2|row1-col3||row2-col1|row2-col2|row3-col3||" {tr}creates a table{/tr}<br/>
<strong>{tr}RSS feeds{/tr}</strong> "{literal}{{/literal}rss id=n max=m{literal}}{/literal}" {tr}displays rss feed with id=n maximum=m items{/tr}<br/>
<strong>{tr}Simple box{/tr}</strong> "^{tr}Box content{/tr}^" {tr}Creates a box with the data{/tr}<br/>
<strong>{tr}Dynamic content{/tr}</strong> "{literal}{{/literal}content id=n}" {tr}Will be replaced by the actual value of the dynamic content block with id=n{/tr}<br/>
<strong>{tr}Colored text{/tr}</strong> "~~#FFEE33:some text~~" {tr}Will display using the indicated HTML color{/tr}<br/>
<strong>{tr}Center{/tr}</strong> "::some text::" {tr}Will display the text centered{/tr}<br/>
</p>
</div>
