<a name="blogs"></a>
{include file="tiki-admin-include-anchors-empty.tpl"}
<div class="cbox">
<div class="cbox-title">{tr}Blog settings{/tr}</div>
<div class="cbox-data">
<div class="simplebox">
<form action="tiki-admin.php" method="post">
<table width="100%">
<tr><td class="form">{tr}Home Blog (main blog){/tr}</td><td>
<select name="homeBlog">
{section name=ix loop=$blogs}
<option value="{$blogs[ix].blogId}" {if $blogs[ix].blogId eq $home_blog}selected="selected"{/if}>{$blogs[ix].title|truncate:20:"(...)":true}</option>
{/section}
</select>
</td></tr>
<tr><td align="center" colspan="2"><input type="submit" name="blogset" value="{tr}Set prefs{/tr}" /></td></tr>    
</table>
</form>
</div>




<div class="simplebox">
{tr}Blog features{/tr}<br/>
<form action="tiki-admin.php" method="post">
    <table width="100%">
    <tr><td class="form">{tr}Rankings{/tr}:</td><td><input type="checkbox" name="feature_blog_rankings" {if $feature_blog_rankings eq 'y'}checked="checked"{/if}/></td></tr>
    <tr><td class="form">{tr}Blog level comments{/tr}:</td><td><input type="checkbox" name="feature_blog_comments" {if $feature_blog_comments eq 'y'}checked="checked"{/if}/></td></tr>
    <tr><td class="form">{tr}Post level comments{/tr}:</td><td><input type="checkbox" name="feature_blogposts_comments" {if $feature_blogposts_comments eq 'y'}checked="checked"{/if}/></td></tr>
    <tr><td class="form">{tr}Spellchecking{/tr}:</td><td><input type="checkbox" name="blog_spellcheck" {if $blog_spellcheck eq 'y'}checked="checked"{/if}/></td></tr>
    <tr><td class="form">{tr}Default ordering for blog listing{/tr}:</td>
    <td>
    <select name="blog_list_order">
    <option value="created_desc" {if $blog_list_order eq 'created_desc'}selected="selected"{/if}>{tr}Creation date (desc){/tr}</option>
    <option value="lastModif_desc" {if $blog_list_order eq 'lastModif_desc'}selected="selected"{/if}>{tr}Last modification date (desc){/tr}</option>
    <option value="title_asc" {if $blog_list_order eq 'title_asc'}selected="selected"{/if}>{tr}Blog title (asc){/tr}</option>
    <option value="posts_desc" {if $blog_list_order eq 'posts_desc'}selected="selected"{/if}>{tr}Number of posts (desc){/tr}</option>
    <option value="hits_desc" {if $blog_list_order eq 'hits_desc'}selected="selected"{/if}>{tr}Visits (desc){/tr}</option>
    <option value="activity_desc" {if $blog_list_order eq 'activity_desc'}selected="selected"{/if}>{tr}Activity (desc){/tr}</option>
    </select>
    </td></tr>
    <tr><td align="center" colspan="2"><input type="submit" name="blogfeatures" value="{tr}Set features{/tr}" /></td></tr>    
    </table>
</form>
</div>

    <div class="simplebox">
    {tr}Blog comments settings{/tr}
    <form method="post" action="tiki-admin.php">
    <table>
    <tr><td class="form">{tr}Default number of comments per page{/tr}: </td><td><input size="5" type="text" name="blog_comments_per_page" value="{$blog_comments_per_page}" /></td></tr>
    <tr><td class="form">{tr}Comments default ordering{/tr}
    </td><td>
    <select name="blog_comments_default_ordering">
    <option value="commentDate_desc" {if $blog_comments_default_ordering eq 'commentDate_dec'}selected="selected"{/if}>{tr}Date{/tr}</option>
    <option value="points_desc" {if $blog_comments_default_ordering eq 'points_desc'}selected="selected"{/if}>{tr}Points{/tr}</option>
    </select>
    </td></tr>
    <tr><td align="center" colspan="2"><input type="submit" name="blogcomprefs" value="{tr}Change preferences{/tr}" /></td></tr>
    </table>
    </form>
    </div>

</div>
</div>

