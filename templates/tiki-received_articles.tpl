<a class="pagetitle" href="tiki-received_articles.php">Received articles</a><br/><br/>
{if $preview eq 'y'}
<h2>Preview</h2>
<div class="articletitle">
<span class="titlea">{$title}</span><br/>
<span class="titleb">{tr}By:{/tr}{$authorName} {tr}on:{/tr}{$publishDate|date_format:"%a %d of %b, %Y [%H:%M:%S]"} (0 {tr}reads{/tr})</span>
</div>
<div class="articleheading">
<table width="100%" cellpadding="0" cellspacing="0">
<tr><td width="25%" valign="top">
{if $useImage eq 'y'}
<img alt="theimage" border="0" src="received_article_image.php?id={$receivedArticleId}" />
{else}
<img alt="theimage" border="0" src="topic_image.php?id={$topic}" />
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
<input type="hidden" name="receivedArticleId" value="{$receivedArticleId}" />
<input type="hidden" name="created" value="{$created}" />
<input type="hidden" name="image_name" value="{$image_name}" />
<input type="hidden" name="image_size" value="{$image_size}" />
<table class="normal">
<tr><td class="formcolor">{tr}Title{/tr}:</td><td class="formcolor"><input type="text" name="title" value="{$title}" /></td></tr>
<tr><td class="formcolor">{tr}Author Name{/tr}:</td><td class="formcolor"><input type="text" name="authorName" value="{$authorName}" /></td></tr>
<tr><td class="formcolor">{tr}Use Image{/tr}:</td><td class="formcolor">
<select name="useImage">
<option value="y" {if $useImage eq 'y'}selected="selected"{/if}>{tr}yes{/tr}</option>
<option value="n" {if $useImage eq 'n'}selected="selected"{/if}>{tr}no{/tr}</option>
</select>
</td></tr>
<tr><td class="formcolor">{tr}Image x size{/tr}:</td><td class="formcolor"><input type="text" name="image_x" value="{$image_x}" /></td></tr>
<tr><td class="formcolor">{tr}Image y size{/tr}:</td><td class="formcolor"><input type="text" name="image_y" value="{$image_y}" /></td></tr>
<tr><td class="formcolor">{tr}Image name{/tr}:</td><td class="formcolor">{$image_name}</td></tr>
<tr><td class="formcolor">{tr}Image size{/tr}:</td><td class="formcolor">{$image_size}</td></tr>
{if $useImage eq 'y'}
<tr><td class="formcolor">{tr}Image{/tr}:</td><td class="formcolor">
<img alt="article image" width="{$image_x}" height="{$image_y}" src="received_article_image.php?id={$receivedArticleId}" />
</td></tr>
{/if}
<tr><td class="formcolor">{tr}Created{/tr}:</td><td class="formcolor">{$created|date_format:"%a %d of %b, %Y [%H:%M:%S]"}</td></tr>
<tr><td class="formcolor">{tr}Publishing date{/tr}:</td><td class="formcolor">
{html_select_date time=$publishDate end_year="+1"} at {html_select_time time=$publishDate display_seconds=false}
</td></tr>
<tr><td class="formcolor">{tr}Heading{/tr}:</td><td class="formcolor"><textarea rows="5" cols="40" name="heading">{$heading}</textarea></td></tr>
<tr><td class="formcolor">{tr}Heading{/tr}:</td><td class="formcolor"><textarea rows="25" cols="40" name="body">{$body}</textarea></td></tr>
<tr><td  class="formcolor">&nbsp;</td><td class="formcolor"><input type="submit" name="preview" value="{tr}Preview{/tr}" />&nbsp;<input type="submit" name="save" value="{tr}Save{/tr}" /></td></tr>
<tr><td class="formcolor">{tr}Accept Article{/tr}</td><td class="formcolor">
{tr}Topic{/tr}:<select name="topic">
{section name=t loop=$topics}
<option value="{$topics[t].topicId}" {if $topic eq $topics[t].topicId}selected="selected"{/if}>{$topics[t].name}</option>
{/section}
</select><input type="submit" name="accept" value="{tr}accept{/tr}" /></td></tr>
</table>
</form>
{/if}

<h2>Received Articles</h2>
<div  align="center">
<table class="findtable">
<tr><td class="findtable">{tr}Find{/tr}</td>
   <td class="findtable">
   <form method="get" action="tiki-received_articles.php">
     <input type="text" name="find" />
     <input type="submit" value="find" name="search" />
     <input type="hidden" name="sort_mode" value="{$sort_mode}" />
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
<td class="odd">{$channels[user].title}</td>
<td class="odd">{$channels[user].receivedDate|date_format:"%a %d of %b, %Y [%H:%M:%S]"}</td>
<td class="odd">{$channels[user].receivedFromSite}</td>
<td class="odd">{$channels[user].receivedFromUser}</td>
<td class="odd">
   <a class="link" href="tiki-received_articles.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;remove={$channels[user].receivedArticleId}">{tr}remove{/tr}</a>
   <a class="link" href="tiki-received_articles.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;receivedArticleId={$channels[user].receivedArticleId}">{tr}edit{/tr}</a>
   <!--<a class="link" href="tiki-received_articles.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;view={$channels[user].receivedArticleId}">{tr}view{/tr}</a>-->
   <!--<a class="link" href="tiki-received_articles.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;accept={$channels[user].receivedArticleId}">{tr}accept{/tr}</a>-->
</td>
</tr>
{else}
<tr>
<td class="even">{$channels[user].receivedArticleId}</td>
<td class="even">{$channels[user].title}</td>
<td class="even">{$channels[user].receivedDate|date_format:"%a %d of %b, %Y [%H:%M:%S]"}</td>
<td class="even">{$channels[user].receivedFromSite}</td>
<td class="even">{$channels[user].receivedFromUser}</td>
<td class="even">
   <a class="link" href="tiki-received_articles.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;remove={$channels[user].receivedArticleId}">{tr}remove{/tr}</a>
   <a class="link" href="tiki-received_articles.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;receivedArticleId={$channels[user].receivedArticleId}">{tr}edit{/tr}</a>
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
</div>
</div>

