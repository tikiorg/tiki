{* $Header: /cvsroot/tikiwiki/tiki/templates/tiki-received_articles.tpl,v 1.11 2003-08-21 00:51:21 redflo Exp $ *}
<a class="pagetitle" href="tiki-received_articles.php">{tr}Received articles{/tr}</a><br/><br/>

{if $feature_help eq 'y'}
<a href="http://tikiwiki.org/tiki-index.php?page=Article" target="tikihelp" class="tikihelp" title="{tr}Tikiwiki.org help{/tr}: {tr}Received Articles{/tr}">
<img border='0' src='img/icons/help.gif' alt='{tr}help{/tr}' /></a>
{/if}

{if $feature_view_tpl eq 'y'}
<a href="tiki-edit_templates.php?template=templates/tiki-received_articles.tpl" target="tikihelp" class="tikihelp" title="{tr}View tpl{/tr}: {tr}received articles tpl{/tr}">
<img border='0' src='img/icons/info.gif' alt='{tr}edit template{/tr}' /></a>
{/if}

<br /><br />
{if $preview eq 'y'}
<h2>{tr}Preview{/tr}</h2>
<div class="articletitle">
<span class="titlea">{$title}</span><br/>
<span class="titleb">{tr}By:{/tr} {$authorName} {tr}on:{/tr} {$publishDate|tiki_short_datetime} (0 {tr}reads{/tr})</span>
</div>
<div class="articleheading">
<table width="100%" cellpadding="0" cellspacing="0">
<tr><td width="25%" valign="top">
{if $useImage eq 'y'}
  <img alt="{tr}Article image{/tr}" border="0" src="received_article_image.php?id={$receivedArticleId}" />
{else}
  <img alt="{tr}Topic image{/tr}" border="0" src="topic_image.php?id={$topic}" />
{/if}
</td><td width="75%" valign="top">
<span class="articleheading">{$parsed_heading}</span>
</td></tr>
</table>
</div>
<div class="articletrailer">
(xx bytes
)
</div>

<div class="articlebody">
{$parsed_body}
</div>
{/if}


{if $receivedArticleId > 0}
<h2>{tr}Edit received article{/tr}</h2>
<form action="tiki-received_articles.php" method="post">
<input type="hidden" name="receivedArticleId" value="{$receivedArticleId|escape}" />
<input type="hidden" name="created" value="{$created|escape}" />
<input type="hidden" name="image_name" value="{$image_name|escape}" />
<input type="hidden" name="image_size" value="{$image_size|escape}" />
<table class="normal">
<tr><td class="formcolor">{tr}Title{/tr}:</td><td class="formcolor"><input type="text" name="title" value="{$title|escape}" /></td></tr>
<tr><td class="formcolor">{tr}Author Name{/tr}:</td><td class="formcolor"><input type="text" name="authorName" value="{$authorName|escape}" /></td></tr>

<tr><td class="formcolor">{tr}Type{/tr}</td><td class="formcolor">
<select id='articletype' name='type' onChange='javascript:chgArtType();'>
<option value='Article' {if $type eq 'Article'}sselected="selected"{/if}>{tr}Article{/tr}</option>
<option value='Review' {if $type eq 'Review'}selected="selected"{/if}>{tr}Review{/tr}</option>
</select>
</select></td></tr>
<tr id='isreview' {if $type eq 'Article'}style="display:none;"{else}style="display:block;"{/if}><td class="formcolor">{tr}Rating{/tr}</td><td class="formcolor">
<select name='rating'>
<option value="10" {if $rating eq 10}selected="selected"{/if}>10</option>
<option value="9.5" {if $rating eq "9.5"}selected="selected"{/if}>9.5</option>
<option value="9" {if $rating eq 9}selected="selected"{/if}>9</option>
<option value="8.5" {if $rating eq "8.5"}selected="selected"{/if}>8.5</option>
<option value="8" {if $rating eq 8}selected="selected"{/if}>8</option>
<option value="7.5" {if $rating eq "7.5"}selected="selected"{/if}>7.5</option>
<option value="7" {if $rating eq 7}selected="selected"{/if}>7</option>
<option value="6.5" {if $rating eq "6.5"}selected="selected"{/if}>6.5</option>
<option value="6" {if $rating eq 6}selected="selected"{/if}>6</option>
<option value="5.5" {if $rating eq "5.5"}selected="selected"{/if}>5.5</option>
<option value="5" {if $rating eq 5}selected="selected"{/if}>5</option>
<option value="4.5" {if $rating eq "4.5"}selected="selected"{/if}>4.5</option>
<option value="4" {if $rating eq 4}selected="selected"{/if}>4</option>
<option value="3.5" {if $rating eq "3.5"}selected="selected"{/if}>3.5</option>
<option value="3" {if $rating eq 3}selected="selected"{/if}>3</option>
<option value="2.5" {if $rating eq "2.5"}selected="selected"{/if}>2.5</option>
<option value="2" {if $rating eq 2}selected="selected"{/if}>2</option>
<option value="1.5" {if $rating eq "1.5"}selected="selected"{/if}>1.5</option>
<option value="1" {if $rating eq 1}selected="selected"{/if}>1</option>
<option value="0.5" {if $rating eq "0.5"}selected="selected"{/if}>0.5</option>
</select>
</td></tr>



<tr><td class="formcolor">{tr}Use Image{/tr}:</td><td class="formcolor">
<select name="useImage">
<option value="y" {if $useImage eq 'y'}selected="selected"{/if}>{tr}yes{/tr}</option>
<option value="n" {if $useImage eq 'n'}selected="selected"{/if}>{tr}no{/tr}</option>
</select>
</td></tr>
<tr><td class="formcolor">{tr}Image x size{/tr}:</td><td class="formcolor"><input type="text" name="image_x" value="{$image_x|escape}" /></td></tr>
<tr><td class="formcolor">{tr}Image y size{/tr}:</td><td class="formcolor"><input type="text" name="image_y" value="{$image_y|escape}" /></td></tr>
<tr><td class="formcolor">{tr}Image name{/tr}:</td><td class="formcolor">{$image_name}</td></tr>
<tr><td class="formcolor">{tr}Image size{/tr}:</td><td class="formcolor">{$image_size}</td></tr>
{if $useImage eq 'y'}
<tr><td class="formcolor">{tr}Image{/tr}:</td><td class="formcolor">
<img alt="article image" width="{$image_x}" height="{$image_y}" src="received_article_image.php?id={$receivedArticleId}" />
</td></tr>
{/if}
<tr><td class="formcolor">{tr}Created{/tr}:</td><td class="formcolor">{$created|tiki_short_datetime}</td></tr>
<tr><td class="formcolor">{tr}Publishing date{/tr}:</td><td class="formcolor">
{html_select_date time=$publishDate end_year="+1"} at {html_select_time time=$publishDate display_seconds=false}
</td></tr>
<tr><td class="formcolor">{tr}Heading{/tr}:</td><td class="formcolor"><textarea rows="5" cols="40" name="heading">{$heading|escape}</textarea></td></tr>
<tr><td class="formcolor">{tr}Heading{/tr}:</td><td class="formcolor"><textarea rows="25" cols="40" name="body">{$body|escape}</textarea></td></tr>
<tr><td  class="formcolor">&nbsp;</td><td class="formcolor"><input type="submit" name="preview" value="{tr}Preview{/tr}" />&nbsp;<input type="submit" name="save" value="{tr}Save{/tr}" /></td></tr>
<tr><td class="formcolor">{tr}Accept Article{/tr}</td><td class="formcolor">
{tr}Topic{/tr}:<select name="topic">
{section name=t loop=$topics}
<option value="{$topics[t].topicId|escape}" {if $topic eq $topics[t].topicId}selected="selected"{/if}>{$topics[t].name}</option>
{/section}
</select><input type="submit" name="accept" value="{tr}accept{/tr}" /></td></tr>
</table>
</form>
{/if}

<h2>{tr}Received Articles{/tr}</h2>
<div  align="center">
<table class="findtable">
<tr><td class="findtable">{tr}Find{/tr}</td>
   <td class="findtable">
   <form method="get" action="tiki-received_articles.php">
     <input type="text" name="find" />
     <input type="submit" name="search" value="{tr}find{/tr}" />
     <input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
   </form>
   </td>
</tr>
</table>
<table class="normal">
<tr>
<td class="heading"><a class="tableheading" href="tiki-received_articles.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'receivedArticleId_desc'}receivedArticleId_asc{else}receivedArticleId_desc{/if}">{tr}ID{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-received_articles.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'title_desc'}title_asc{else}title_desc{/if}">{tr}title{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-received_articles.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'receivedDate_desc'}receivedDate_asc{else}receivedDate_desc{/if}">{tr}Date{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-received_articles.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'receivedFromSite_desc'}receivedFromSite_asc{else}receivedFromSite_desc{/if}">{tr}Site{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-received_articles.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'receivedFromUser_desc'}receivedFromUser_asc{else}receivedFromUser_desc{/if}">{tr}User{/tr}</a></td>
<td class="heading">{tr}action{/tr}</td>
</tr>
{section name=user loop=$channels}
{if $smarty.section.user.index % 2}
<tr>
<td class="odd">{$channels[user].receivedArticleId}</td>
<td class="odd">{$channels[user].title}
{if $channels[user].type eq 'Review'}(r){/if}
</td>
<td class="odd">{$channels[user].receivedDate|tiki_short_datetime}</td>
<td class="odd">{$channels[user].receivedFromSite}</td>
<td class="odd">{$channels[user].receivedFromUser}</td>
<td class="odd">
   <a class="link" href="tiki-received_articles.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;receivedArticleId={$channels[user].receivedArticleId}"><img src='img/icons/edit.gif' alt='{tr}edit{/tr}' title='{tr}edit{/tr}' border='0' /></a>
   <a class="link" href="tiki-received_articles.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;remove={$channels[user].receivedArticleId}"><img src='img/icons2/delete.gif' alt='{tr}remove{/tr}' title='{tr}remove{/tr}' border='0' /></a>
   <!--<a class="link" href="tiki-received_articles.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;view={$channels[user].receivedArticleId}">{tr}view{/tr}</a>-->
   <!--<a class="link" href="tiki-received_articles.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;accept={$channels[user].receivedArticleId}">{tr}accept{/tr}</a>-->
</td>
</tr>
{else}
<tr>
<td class="even">{$channels[user].receivedArticleId}</td>
<td class="even">{$channels[user].title}
{if $channels[user].type eq 'Review'}(r){/if}
</td>
<td class="even">{$channels[user].receivedDate|tiki_short_datetime}</td>
<td class="even">{$channels[user].receivedFromSite}</td>
<td class="even">{$channels[user].receivedFromUser}</td>
<td class="even">
   <a class="link" href="tiki-received_articles.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;receivedArticleId={$channels[user].receivedArticleId}"><img src='img/icons/edit.gif' alt='{tr}edit{/tr}' title='{tr}edit{/tr}' border='0' /></a>
   <a class="link" href="tiki-received_articles.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;remove={$channels[user].receivedArticleId}"><img src='img/icons2/delete.gif' alt='{tr}remove{/tr}' title='{tr}remove{/tr}' border='0' /></a>
   <!--<a class="link" href="tiki-received_articles.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;view={$channels[user].receivedArticleId}">{tr}view{/tr}</a>-->
   <!--<a class="link" href="tiki-received_articles.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;accept={$channels[user].receivedArticleId}">{tr}accept{/tr}</a>-->
</td>
</tr>
{/if}
{/section}
</table>
<br/>
<div class="mini">
{if $prev_offset >= 0}
[<a class="prevnext" href="tiki-received_articles.php?offset={$prev_offset}&amp;sort_mode={$sort_mode}">{tr}prev{/tr}</a>]&nbsp;
{/if}
{tr}Page{/tr}: {$actual_page}/{$cant_pages}
{if $next_offset >= 0}
&nbsp;[<a class="prevnext" href="tiki-received_articles.php?offset={$next_offset}&amp;sort_mode={$sort_mode}">{tr}next{/tr}</a>]
{/if}
{if $direct_pagination eq 'y'}
<br/>
{section loop=$cant_pages name=foo}
{assign var=selector_offset value=$smarty.section.foo.index|times:$maxRecords}
<a class="prevnext" href="tiki-received_articles.php?offset={$selector_offset}&amp;sort_mode={$sort_mode}">
{$smarty.section.foo.index_next}</a>&nbsp;
{/section}
{/if}
</div>
</div>

