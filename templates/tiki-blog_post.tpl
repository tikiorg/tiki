{if $preview eq 'y'}
{include file=tiki-preview_post.tpl}
{/if}
<a class="pagetitle" href="tiki-blog_post.php?blogId={$blogId}">{tr}Edit Post{/tr}</a><br/><br/>
[{if $blogId > 0 }
<a class="bloglink" href="tiki-view_blog.php?blogId={$blogId}">view blog</a>|
{/if}
<a class="bloglink" href="tiki-list_blogs.php">list blogs</a>]
<br/><br/>
<form method="post" action="tiki-blog_post.php">
<input type="hidden" name="postId" value="{$postId}" />
<table class="editblogform">
<tr><td class="editblogform">{tr}Blog{/tr}</td><td class="editblogform">
<select name="blogId">
{section name=ix loop=$blogs}
<option value="{$blogs[ix].blogId}" {if $blogs[ix].blogId eq $blogId}selected="selected"{/if}>{$blogs[ix].title}</option>
{/section}
</select>
</td></tr>
<tr><td class="editblogform">&nbsp;</td><td class="editblogform">
   
     <table>
     <tr>
          <td><a href="javascript:setSomeElement('blogedit','(:biggrin:)');"><img src="img/smiles/icon_biggrin.gif" alt="big grin" border="0" /></a></td>
          <td><a href="javascript:setSomeElement('blogedit','(:confused:)');"><img src="img/smiles/icon_confused.gif" alt="confused" border="0" /></a></td>
          <td><a href="javascript:setSomeElement('blogedit','(:cool:)');"><img src="img/smiles/icon_cool.gif" alt="cool" border="0" /></a></td>
          <td><a href="javascript:setSomeElement('blogedit','(:cry:)');"><img src="img/smiles/icon_cry.gif" alt="cry" border="0" /></a></td>
          <td><a href="javascript:setSomeElement('blogedit','(:eek:)');"><img src="img/smiles/icon_eek.gif" alt="eek" border="0" /></a></td>
          <td><a href="javascript:setSomeElement('blogedit','(:evil:)');"><img src="img/smiles/icon_evil.gif" alt="evil" border="0" /></a></td>
          <td><a href="javascript:setSomeElement('blogedit','(:exclaim:)');"><img src="img/smiles/icon_exclaim.gif" alt="exclaim" border="0" /></a></td>
          <td><a href="javascript:setSomeElement('blogedit','(:frown:)');"><img src="img/smiles/icon_frown.gif" alt="frown" border="0" /></a></td>
          <td><a href="javascript:setSomeElement('blogedit','(:idea:)');"><img src="img/smiles/icon_idea.gif" alt="idea" border="0" /></a></td>
          <td><a href="javascript:setSomeElement('blogedit','(:lol:)');"><img src="img/smiles/icon_lol.gif" alt="lol" border="0" /></a></td>
          <td><a href="javascript:setSomeElement('blogedit','(:mad:)');"><img src="img/smiles/icon_mad.gif" alt="mad" border="0" /></a></td>
          <td><a href="javascript:setSomeElement('blogedit','(:mrgreen:)');"><img src="img/smiles/icon_mrgreen.gif" alt="mr green" border="0" /></a></td>
          <td><a href="javascript:setSomeElement('blogedit','(:neutral:)');"><img src="img/smiles/icon_neutral.gif" alt="neutral" border="0" /></a></td>
          <td><a href="javascript:setSomeElement('blogedit','(:question:)');"><img src="img/smiles/icon_question.gif" alt="question" border="0" /></a></td>
          <td><a href="javascript:setSomeElement('blogedit','(:razz:)');"><img src="img/smiles/icon_razz.gif" alt="razz" border="0" /></a></td>
          <td><a href="javascript:setSomeElement('blogedit','(:redface:)');"><img src="img/smiles/icon_redface.gif" alt="redface" border="0" /></a></td>
          <td><a href="javascript:setSomeElement('blogedit','(:rolleyes:)');"><img src="img/smiles/icon_rolleyes.gif" alt="rolleyes" border="0" /></a></td>
          <td><a href="javascript:setSomeElement('blogedit','(:sad:)');"><img src="img/smiles/icon_sad.gif" alt="sad" border="0" /></a></td>
          <td><a href="javascript:setSomeElement('blogedit','(:smile:)');"><img src="img/smiles/icon_smile.gif" alt="smile" border="0" /></a></td>
          <td><a href="javascript:setSomeElement('blogedit','(:surprised:)');"><img src="img/smiles/icon_surprised.gif" alt="surprised" border="0" /></a></td>
          <td><a href="javascript:setSomeElement('blogedit','(:twisted:)');"><img src="img/smiles/icon_twisted.gif" alt="twisted" border="0" /></a></td>
          <td><a href="javascript:setSomeElement('blogedit','(:wink:)');"><img src="img/smiles/icon_wink.gif" alt="wink" border="0" /></a></td>
          <td><a href="javascript:setSomeElement('blogedit','(:arrow:)');"><img src="img/smiles/icon_arrow.gif" alt="arrow" border="0" /></a></td>
      </tr>    
      </table>


</td></tr>
<tr><td class="formcolor">{tr}Quicklinks{/tr}</td><td class="formcolor">
{assign var=area_name value="blogedit"}
{include file=tiki-edit_help_tool.tpl}
</td>
<tr><td class="editblogform">{tr}Data{/tr}</td><td class="editblogform"><textarea id='blogedit' class="wikiedit" name="data" rows="20" cols="80" wrap="virtual">{$data}</textarea></td></tr>
</td></tr>
{if $blog_spellcheck eq 'y'}
<tr><td class="editblogform">{tr}Spellcheck{/tr}: </td><td class="editblogform"><input type="checkbox" name="spellcheck" {if $spellcheck eq 'y'}checked="checked"{/if}/></td>
{/if}
<tr><td class="editblogform">&nbsp;</td><td class="editblogform"><input type="submit" class="wikiaction" name="preview" value="{tr}preview{/tr}" />
<input type="submit" class="wikiaction" name="save" value="{tr}save{/tr}" /></td></tr>
</table>
</form>
<br/>
<div class="wiki-edithelp">
<p>
<a class="wiki">{tr}TextFormattingRules{/tr}</a><br />
<strong>{tr}Allowed HTML:{/tr}:</strong>&lt;a&gt;,&lt;p&gt;,&lt;img&gt;,&lt;b&gt;,&lt;i&gt;<br/>
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
