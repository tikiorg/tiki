{* $Header: /cvsroot/tikiwiki/tiki/templates/tiki-article_types.tpl,v 1.16 2004-04-10 04:46:24 mose Exp $ *}

<a  class="pagetitle" href="tiki-article_types.php">{tr}Admin Article Types{/tr}</a>

<!-- the help link info -->
{if $feature_help eq 'y'}
<a href="http://tikiwiki.org/tiki-index.php?page=ArticleDoc" target="tikihelp" class="tikihelp" title="{tr}Tikiwiki.org help{/tr}: {tr}Admin Article Types{/tr}">
<img border='0' src='img/icons/help.gif' alt="{tr}help{/tr}" /></a>{/if}

<!-- link to tpl -->
{if $feature_view_tpl eq 'y'}
<a href="tiki-edit_templates.php?template=templates/tiki-article_types.tpl" target="tikihelp" class="tikihelp" title="{tr}View tpl{/tr}: {tr}Article Types tpl{/tr}">
<img border='0' src='img/icons/info.gif' alt="{tr}edit tpl{/tr}" /></a>{/if}

<!-- beginning of next bit -->
<br />
<b>{tr}Name{/tr}</b> - Shows up in the drop down list of article types<br />
<b>{tr}Rate{/tr}</b> - Allow ratings by the author<br />
<b>{tr}Show before publish date{/tr}</b> - non-admins can view before the publish date<br />
<b>{tr}Show after expire date{/tr}</b> - non-admins can view after the expire date<br />
<b>{tr}Heading only{/tr}</b> - No article body, heading only<br />
<b>{tr}Comments{/tr}</b> - Allow comments for this type<br />
<b>{tr}Comment Can Rate Article{/tr}</b> - Allow comments to include a rating value<br />
<b>{tr}Show image{/tr}</b> - Show topic or own image<br />
<b>{tr}Show avatar{/tr}</b> - Show author's avatar<br />
<b>{tr}Show author{/tr}</b> - Show author name<br />
<b>{tr}Show publish date{/tr}</b> - Show publish date<br />
<b>{tr}Show expire date{/tr}</b> - Show expire date<br />
<b>{tr}Show reads{/tr}</b> - Show the number of times the article was read<br />
<b>{tr}Show size{/tr}</b> - Show the size of the article<br />
<b>{tr}Show topline{/tr}</b> - Show a small title over the title<br />
<b>{tr}Show subtitle{/tr}</b> - Show the subtitle<br />
<b>{tr}Show linkto{/tr}</b> - Show a link field<br />
<b>{tr}Show Image Caption{/tr}</b> - Show a legend under the image<br />
<b>{tr}Show Language{/tr}</b> - Show the language<br />
<b>{tr}Creator can edit{/tr}</b> - The person who submits an article of this type can edit it<br />
<b>{tr}Delete{/tr}</b> - Delete this type<br />
<h3>{tr}List of types{/tr}</h3>
<form enctype="multipart/form-data" action="tiki-article_types.php" method="post">
<table class="normal">
<tr class="heading">
<td>{tr}Name{/tr}</td>
<td>{tr}Rate{/tr}</td>
<td>{tr}Show before publish date{/tr}</td>
<td>{tr}Show after expire date{/tr}</td>
<td>{tr}Heading only{/tr}</td>
<td>{tr}Comments{/tr}</td>
<td>{tr}Comment Can Rate Article{/tr}</td>
<td>{tr}Show image{/tr}</td>
<td>{tr}Show avatar{/tr}</td>
<td>{tr}Show author{/tr}</td>
<td>{tr}Show publish date{/tr}</td>
<td>{tr}Show expire date{/tr}</td>
<td>{tr}Show reads{/tr}</td>
<td>{tr}Show size{/tr}</td>
<td>{tr}Show topline{/tr}</td>
<td>{tr}Show subtitle{/tr}</td>
<td>{tr}Show linkto{/tr}</td>
<td>{tr}Show Image Caption{/tr}</td>
<td>{tr}Show lang{/tr}</td>
<td>{tr}Creator can edit{/tr}</td>
<td>{tr}Delete{/tr}</td>
</tr>
{cycle print=false values="even,odd"}
{section name=user loop=$types}
<input type="hidden" name="type_array[{$types[user].type}]" />
<tr>
<td class="{cycle advance=false}">
  <a class="link" href="tiki-view_articles.php?type={$types[user].type}">{tr}{$types[user].type}{/tr}</a>
</td>
{*get_strings {tr}Articl{/tr}{tr}Review{/tr}{tr}Event{/tr}{tr}Classified{/tr} *}
<td class="{cycle advance=false}"><input type="checkbox" name="use_ratings[{$types[user].type}]" {if $types[user].use_ratings eq 'y'}checked="checked"{/if} /></td>
<td class="{cycle advance=false}"><input type="checkbox" name="show_pre_publ[{$types[user].type}]" {if $types[user].show_pre_publ eq 'y'}checked="checked"{/if} /></td>
<td class="{cycle advance=false}"><input type="checkbox" name="show_post_expire[{$types[user].type}]" {if $types[user].show_post_expire eq 'y'}checked="checked"{/if} /></td>
<td class="{cycle advance=false}"><input type="checkbox" name="heading_only[{$types[user].type}]" {if $types[user].heading_only eq 'y'}checked="checked"{/if} /></td>
<td class="{cycle advance=false}"><input type="checkbox" name="allow_comments[{$types[user].type}]" {if $types[user].allow_comments eq 'y'}checked="checked"{/if} /></td>
<td class="{cycle advance=false}"><input type="checkbox" name="comment_can_rate_article[{$types[user].type}]" {if $types[user].comment_can_rate_article eq 'y'}checked="checked"{/if} /></td>
<td class="{cycle advance=false}"><input type="checkbox" name="show_image[{$types[user].type}]" {if $types[user].show_image eq 'y'}checked="checked"{/if} /></td>
<td class="{cycle advance=false}"><input type="checkbox" name="show_avatar[{$types[user].type}]" {if $types[user].show_avatar eq 'y'}checked="checked"{/if} /></td>
<td class="{cycle advance=false}"><input type="checkbox" name="show_author[{$types[user].type}]" {if $types[user].show_author eq 'y'}checked="checked"{/if} /></td>
<td class="{cycle advance=false}"><input type="checkbox" name="show_pubdate[{$types[user].type}]" {if $types[user].show_pubdate eq 'y'}checked="checked"{/if} /></td>
<td class="{cycle advance=false}"><input type="checkbox" name="show_expdate[{$types[user].type}]" {if $types[user].show_expdate eq 'y'}checked="checked"{/if} /></td>
<td class="{cycle advance=false}"><input type="checkbox" name="show_reads[{$types[user].type}]" {if $types[user].show_reads eq 'y'}checked="checked"{/if} /></td>
<td class="{cycle advance=false}"><input type="checkbox" name="show_size[{$types[user].type}]" {if $types[user].show_size eq 'y'}checked="checked"{/if} /></td>
<td class="{cycle advance=false}"><input type="checkbox" name="show_topline[{$types[user].type}]" {if $types[user].show_topline eq 'y'}checked="checked"{/if} /></td>
<td class="{cycle advance=false}"><input type="checkbox" name="show_subtitle[{$types[user].type}]" {if $types[user].show_subtitle eq 'y'}checked="checked"{/if} /></td>
<td class="{cycle advance=false}"><input type="checkbox" name="show_linkto[{$types[user].type}]" {if $types[user].show_linkto eq 'y'}checked="checked"{/if} /></td>
<td class="{cycle advance=false}"><input type="checkbox" name="show_image_caption[{$types[user].type}]" {if $types[user].show_image_caption eq 'y'}checked="checked"{/if} /></td>
<td class="{cycle advance=false}"><input type="checkbox" name="show_lang[{$types[user].type}]" {if $types[user].show_lang eq 'y'}checked="checked"{/if} /></td>
<td class="{cycle advance=false}"><input type="checkbox" name="creator_edit[{$types[user].type}]" {if $types[user].creator_edit eq 'y'}checked="checked"{/if} /></td>
<td class="{cycle}">
{if $types[user].article_cnt eq 0}
<a class="link" href="tiki-article_types.php?remove_type={$types[user].type}"><img src='img/icons2/delete.gif' alt='{tr}Remove{/tr}' title='{tr}Remove{/tr}' border='0' /></a>
{else}
{$types[user].article_cnt}
{/if}
</td>
</tr>
{/section}
</table>
<br />
<input type="submit" name="update_type" value="{tr}save{/tr}" /><br />
<br />
<input type="text" name="new_type" /><input type="submit" name="add_type" value="{tr}Create a new type{/tr}" />

</form>
