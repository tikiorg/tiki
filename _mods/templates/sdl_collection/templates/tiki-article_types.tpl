{* $Header: /cvsroot/tikiwiki/_mods/templates/sdl_collection/templates/tiki-article_types.tpl,v 1.1 2004-05-09 23:09:15 damosoft Exp $ *}

<a  class="pagetitle" href="tiki-article_types.php">{tr}Admin Article Types{/tr}</a>

<!-- the help link info -->
{if $feature_help eq 'y'}
<a href="http://tikiwiki.org/tiki-index.php?page=ArticleDoc" target="tikihelp" class="tikihelp" title="{tr}Tikiwiki.org help{/tr}: {tr}Admin Article Types{/tr}">
<img border='0' src='img/icons/help.gif' alt='help' /></a>{/if}

<!-- link to tpl -->
{if $feature_view_tpl eq 'y'}
<a href="tiki-edit_templates.php?template=templates/tiki-article_types.tpl" target="tikihelp" class="tikihelp" title="{tr}View tpl{/tr}: {tr}Article Types tpl{/tr}">
<img border='0' src='img/icons/info.gif' alt='edit tpl' /></a>{/if}

<!-- beginning of next bit -->
<br />
<ol><li><b>{tr}Name{/tr}</b> - Shows up in the drop down list of article types<br /></li>
<li><b>{tr}Rate{/tr}</b> - Allow ratings by the author<br /></li>
<li><b>{tr}Show before publish date{/tr}</b> - non-admins can view before the publish date<br /></li>
<li><b>{tr}Show after expire date{/tr}</b> - non-admins can view after the expire date<br /></li>
<li><b>{tr}Heading only{/tr}</b> - No article body, heading only<br /></li>
<li><b>{tr}Comments{/tr}</b> - Allow comments for this type<br /></li>
<li><b>{tr}Comment Can Rate Article{/tr}</b> - Allow comments to include a rating value<br /></li>
<li><b>{tr}Show image{/tr}</b> - Show topic or own image<br /></li>
<li><b>{tr}Show avatar{/tr}</b> - Show author's avatar<br /></li>
<li><b>{tr}Show author{/tr}</b> - Show author name<br /></li>
<li><b>{tr}Show publish date{/tr}</b> - Show publish date<br /></li>
<li><b>{tr}Show expire date{/tr}</b> - Show expire date<br /></li>
<li><b>{tr}Show reads{/tr}</b> - Show the number of times the article was read<br /></li>
<li><b>{tr}Show size{/tr}</b> - Show the size of the article<br /></li>
<li><b>{tr}Creator can edit{/tr}</b> - The person who submits an article of this type can edit it<br /></li>
<li><b>{tr}Delete{/tr}</b> - Delete this type<br /></li></ol>
<h3>{tr}List of types{/tr}</h3>
<form enctype="multipart/form-data" action="tiki-article_types.php" method="post">
<table class="normal">
<tr class="heading">
<td>{tr}1.{/tr}</td>
<td>{tr}2.{/tr}</td>
<td>{tr}3.{/tr}</td>
<td>{tr}4.{/tr}</td>
<td>{tr}5.{/tr}</td>
<td>{tr}6.{/tr}</td>
<td>{tr}7.{/tr}</td>
<td>{tr}8.{/tr}</td>
<td>{tr}9.{/tr}</td>
<td>{tr}10.{/tr}</td>
<td>{tr}11.{/tr}</td>
<td>{tr}12.{/tr}</td>
<td>{tr}13.{/tr}</td>
<td>{tr}14.{/tr}</td>
<td>{tr}15.{/tr}</td>
<td>{tr}16.{/tr}</td>
</tr>
{cycle print=false values="even,odd"}
{section name=user loop=$types}
<input type="hidden" name="type_array[{$types[user].type}]" />
<tr>
<td class="{cycle advance=false}">
  <a class="link" href="tiki-view_articles.php?type={$types[user].type}">{tr}{$types[user].type}{/tr}</a>
</td>
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
<td class="{cycle advance=false}"><input type="checkbox" name="creator_edit[{$types[user].type}]" {if $types[user].creator_edit eq 'y'}checked="checked"{/if} /></td>
<td class="{cycle}">
{if $types[user].article_cnt eq 0}
<a class="link" href="tiki-article_types.php?remove_type={$types[user].type}" onclick="return confirmTheLink(this,'{tr}Are you sure you want to delete this article type?{/tr}')"><img src='img/icons2/delete.gif' alt='{tr}Remove{/tr}' title='{tr}Remove{/tr}' border='0' /></a>
{else}
{$types[user].article_cnt}
{/if}
</td>
</tr>
{/section}
</table>
<br />
<input type="submit" name="update_type" value="{tr}Save{/tr}" /><br />
<br />
<input type="text" name="new_type" /><input type="submit" name="add_type" value="{tr}Create a new type{/tr}" />

</form>