{* $Header: /cvsroot/tikiwiki/tiki/templates/tiki-article_types.tpl,v 1.2 2003-10-28 16:49:11 dheltzel Exp $ *}

<a  class="pagetitle" href="tiki-article_types.php">{tr}Admin Article Types{/tr}</a>

<!-- the help link info --->
  
      {if $feature_help eq 'y'}
<a href="http://tikiwiki.org/tiki-index.php?page=Forums" target="tikihelp" class="tikihelp" title="{tr}Tikiwiki.org help{/tr}: {tr}Admin Forums{/tr}">
<img border='0' src='img/icons/help.gif' alt='help' />{/if}
                        {if $feature_help eq 'y'}</a>{/if}
<!-- link to tpl -->
      {if $feature_view_tpl eq 'y'}
<a href="tiki-edit_templates.php?template=templates/tiki-article_types.tpl" target="tikihelp" class="tikihelp" title="{tr}View tpl{/tr}: {tr}Article Types tpl{/tr}">
<img border='0' src='img/icons/info.gif' alt='edit tpl' /> {/if}
{if $feature_view_tpl eq 'y'}</a>{/if}
<!--- beginning of next bit --->
<br />
<h3>{tr}List of types{/tr}</h3>
<table class="normal">
<tr>
<td class="heading">{tr}Name{/tr}</td>
<td class="heading">{tr}Rate{/tr}</td>
<td class="heading">{tr}Show before publish date{/tr}</td>
<td class="heading">{tr}Show after expire date{/tr}</td>
<td class="heading">{tr}Heading only{/tr}</td>
<td class="heading">{tr}Comments{/tr}</td>
<td class="heading">{tr}Show image{/tr}</td>
<td class="heading">{tr}Show avatar{/tr}</td>
<td class="heading">{tr}Show author{/tr}</td>
<td class="heading">{tr}Show publish date{/tr}</td>
<td class="heading">{tr}Show expire date{/tr}</td>
<td class="heading">{tr}Show reads{/tr}</td>
<td class="heading" width=10%>{tr}Action{/tr}</td>
</tr>
{cycle print=false values="even,odd"}
{section name=user loop=$types}
<tr>
<td class="{cycle advance=false}">{$types[user].type}</td>
<td class="{cycle advance=false}">{if $types[user].use_ratings eq 'y'}y{/if}</td>
<td class="{cycle advance=false}">{if $types[user].show_pre_publ eq 'y'}y{/if}</td>
<td class="{cycle advance=false}">{if $types[user].show_post_expire eq 'y'}y{/if}</td>
<td class="{cycle advance=false}">{if $types[user].heading_only eq 'y'}y{/if}</td>
<td class="{cycle advance=false}">{if $types[user].allow_comments eq 'y'}y{/if}</td>
<td class="{cycle advance=false}">{if $types[user].show_image eq 'y'}y{/if}</td>
<td class="{cycle advance=false}">{if $types[user].show_avatar eq 'y'}y{/if}</td>
<td class="{cycle advance=false}">{if $types[user].show_author eq 'y'}y{/if}</td>
<td class="{cycle advance=false}">{if $types[user].show_pubdate eq 'y'}y{/if}</td>
<td class="{cycle advance=false}">{if $types[user].show_expdate eq 'y'}y{/if}</td>
<td class="{cycle advance=false}">{if $types[user].show_reads eq 'y'}y{/if}</td>
<td class="{cycle}">
<a class="link" href="tiki-article_types.php?remove={$types[user].type}">{tr}Remove{/tr}</a>
</td>
</tr>
{/section}
</table>
<h3>{tr}Create a new type{/tr}</h3>

<form enctype="multipart/form-data" action="tiki-article_types.php" method="post">
 <table class="normal">
<tr><td class="formcolor" width=20%>{tr}Type Name{/tr}</td><td class="formcolor"><input type="text" name="type" /></td></tr>
<tr><td class="formcolor">{tr}Use Ratings{/tr}</td><td class="formcolor"><input type="checkbox" name="use_ratings" {if $use_ratings eq 'y'}checked="checked"{/if} /></td></tr>
<tr><td class="formcolor">{tr}Show before publish date{/tr}</td><td class="formcolor"><input type="checkbox" name="show_pre_publ" {if $show_pre_publ eq 'y'}checked="checked"{/if} /></td></tr>
<tr><td class="formcolor">{tr}Show after expire date{/tr}</td><td class="formcolor"><input type="checkbox" name="show_post_expire" {if $show_post_expire eq 'y'}checked="checked"{/if} /></td></tr>
<tr><td class="formcolor">{tr}Show heading only{/tr}</td><td class="formcolor"><input type="checkbox" name="heading_only" {if $heading_only eq 'y'}checked="checked"{/if} /></td></tr>
<tr><td class="formcolor">{tr}Allow comments{/tr}</td><td class="formcolor"><input type="checkbox" name="allow_comments" {if $allow_comments eq 'y'}checked="checked"{/if} /></td></tr>
<tr><td class="formcolor">{tr}Show image{/tr}</td><td class="formcolor"><input type="checkbox" name="show_image" {if $show_image eq 'y'}checked="checked"{/if} /></td></tr>
<tr><td class="formcolor">{tr}Show avatar{/tr}</td><td class="formcolor"><input type="checkbox" name="show_avatar" {if $show_avatar eq 'y'}checked="checked"{/if} /></td></tr>
<tr><td class="formcolor">{tr}Show author{/tr}</td><td class="formcolor"><input type="checkbox" name="show_author" {if $show_author eq 'y'}checked="checked"{/if} /></td></tr>
<tr><td class="formcolor">{tr}Show publish date{/tr}</td><td class="formcolor"><input type="checkbox" name="show_pubdate" {if $show_pubdate eq 'y'}checked="checked"{/if} /></td></tr>
<tr><td class="formcolor">{tr}Show expire date{/tr}</td><td class="formcolor"><input type="checkbox" name="show_expdate" {if $show_expdate eq 'y'}checked="checked"{/if} /></td></tr>
<tr><td class="formcolor">{tr}Show reads{/tr}</td><td class="formcolor"><input type="checkbox" name="show_reads" {if $show_reads eq 'y'}checked="checked"{/if} /></td></tr>
<tr><td class="formcolor">&nbsp;</td><td class="formcolor"><input type="submit" name="addtype" value="{tr}save{/tr}" /></td></tr>
</table>
</form>
<br />
<b>Note:</b> To change the settings for an existing type, create a new type with the same name and the correct settings. Type names are case sensitive.
<br />
