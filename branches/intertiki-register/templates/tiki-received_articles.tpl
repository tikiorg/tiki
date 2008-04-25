{* $Id$ *}
<h1><a class="pagetitle" href="tiki-received_articles.php">{tr}Received articles{/tr}</a>

{if $prefs.feature_help eq 'y'}
<a href="{$prefs.helpurl}Communication+Center" target="tikihelp" class="tikihelp" title="{tr}Received Articles{/tr}">
{icon _id='help'}</a>
{/if}

{if $prefs.feature_view_tpl eq 'y'}
<a href="tiki-edit_templates.php?template=tiki-received_articles.tpl" target="tikihelp" class="tikihelp" title="{tr}View tpl{/tr}: {tr}received articles tpl{/tr}">
{icon _id='shape_square_edit' alt='{tr}Edit Template{/tr}'}</a>
{/if}

</h1>
{if $preview eq 'y'}
<h2>{tr}Preview{/tr}</h2>
<div class="articletitle">
<span class="titlea">{$title}</span><br />
<span class="titleb">{tr}By:{/tr} {$authorName} {tr}on:{/tr} {$publishDate|tiki_short_datetime} (0 {tr}Reads{/tr})</span>
</div>
<div class="articleheading">
<table  cellpadding="0" cellspacing="0">
<tr><td  valign="top">
{if $useImage eq 'y'}
  <img alt="{tr}Article image{/tr}" border="0" src="received_article_image.php?id={$receivedArticleId}" />
{else}
  <img alt="{tr}Topic image{/tr}" border="0" src="topic_image.php?id={$topic}" />
{/if}
</td><td  valign="top">
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
<select id='articletype' name='type' onchange='javascript:chgArtType();'>
{section name=t loop=$types}
<option value="{$types[t].type|escape}" {if $type eq $types[t].type}selected="selected"{/if}>{$types[t].type}</option>
{/section}
</select>
{if $tiki_p_admin_cms eq 'y'}<a href="tiki-article_types.php" class="link">{tr}Admin types{/tr}</a>{/if}
</td></tr>
<tr id='isreview' {if $type ne 'Review'}style="display:none;"{else}style="display:block;"{/if}><td class="formcolor">{tr}Rating{/tr}</td><td class="formcolor">
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
<option value="y" {if $useImage eq 'y'}selected="selected"{/if}>{tr}Yes{/tr}</option>
<option value="n" {if $useImage eq 'n'}selected="selected"{/if}>{tr}No{/tr}</option>
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
{html_select_date time=$publishDate end_year="+1" field_order=$prefs.display_field_order} at {html_select_time time=$publishDate display_seconds=false}
</td></tr>
<tr><td class="formcolor">{tr}Heading{/tr}:</td><td class="formcolor"><textarea rows="5" cols="40" name="heading">{$heading|escape}</textarea></td></tr>
<tr><td class="formcolor">{tr}Heading{/tr}:</td><td class="formcolor"><textarea rows="25" cols="40" name="body">{$body|escape}</textarea></td></tr>
<tr><td  class="formcolor">&nbsp;</td><td class="formcolor"><input type="submit" name="preview" value="{tr}Preview{/tr}" />&nbsp;<input type="submit" name="save" value="{tr}Save{/tr}" /></td></tr>
<tr><td class="formcolor">{tr}Accept Article{/tr}</td><td class="formcolor">
{tr}Topic{/tr}:<select name="topic">
{section name=t loop=$topics}
<option value="{$topics[t].topicId|escape}" {if $topic eq $topics[t].topicId}selected="selected"{/if}>{$topics[t].name}</option>
{/section}
</select><input type="submit" name="accept" value="{tr}Accept{/tr}" /></td></tr>
</table>
</form>
{/if}

<h2>{tr}Received Articles{/tr}</h2>
<div align="center">

{if $channels}
<table class="findtable">
<tr><td class="findtable">{tr}Find{/tr}</td>
   <td class="findtable">
   <form method="get" action="tiki-received_articles.php">
     <input type="text" name="find" />
     <input type="submit" name="search" value="{tr}Find{/tr}" />
     <input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
   </form>
   </td>
</tr>
</table>
{/if}

<table class="normal">
<tr>
<td class="heading"><a class="tableheading" href="tiki-received_articles.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'receivedArticleId_desc'}receivedArticleId_asc{else}receivedArticleId_desc{/if}">{tr}ID{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-received_articles.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'title_desc'}title_asc{else}title_desc{/if}">{tr}Title{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-received_articles.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'receivedDate_desc'}receivedDate_asc{else}receivedDate_desc{/if}">{tr}Date{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-received_articles.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'receivedFromSite_desc'}receivedFromSite_asc{else}receivedFromSite_desc{/if}">{tr}Site{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-received_articles.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'receivedFromUser_desc'}receivedFromUser_asc{else}receivedFromUser_desc{/if}">{tr}User{/tr}</a></td>
<td class="heading">{tr}Action{/tr}</td>
</tr>
{cycle values="even,odd" print=false}
{section name=user loop=$channels}
<tr>
<td class="odd">{$channels[user].receivedArticleId}</td>
<td class="odd">{$channels[user].title}
{if $channels[user].type eq 'Review'}(r){/if}
</td>
<td class="{cycle advance=false}">{$channels[user].receivedDate|tiki_short_datetime}</td>
<td class="{cycle advance=false}">{$channels[user].receivedFromSite}</td>
<td class="{cycle advance=false}">{$channels[user].receivedFromUser}</td>
<td class="{cycle advance=true}">
   <a class="link" href="tiki-received_articles.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;receivedArticleId={$channels[user].receivedArticleId}">{icon _id='page_edit'}</a> &nbsp;
   <a class="link" href="tiki-received_articles.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;remove={$channels[user].receivedArticleId}">{icon _id='cross' alt='{tr}Remove{/tr}'}</a>
   <!--<a class="link" href="tiki-received_articles.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;view={$channels[user].receivedArticleId}">{tr}View{/tr}</a>-->
   <!--<a class="link" href="tiki-received_articles.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;accept={$channels[user].receivedArticleId}">{tr}Accept{/tr}</a>-->
</td>
</tr>
{sectionelse}
<tr><td class="odd" colspan="6">{tr}No records.{/tr}</td></tr>
{/section}
</table>

{pagination_links cant=$cant step=$maxRecords offset=$offset}{/pagination_links}

</div>

