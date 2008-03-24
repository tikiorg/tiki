{* $Header: /cvsroot/tikiwiki/tiki/templates/tiki-admin-include-blogs.tpl,v 1.29.2.2 2007-11-22 17:53:03 nkoth Exp $ *}

<div class="rbox" name="tip">
<div class="rbox-title" name="tip">{tr}Tip{/tr}</div>  
<div class="rbox-data" name="tip">{tr}To add/remove blogs, go to "Create/Edit blog" under "Blogs" on the application menu, or{/tr} <a class="rbox-link" href="tiki-edit_blog.php">{tr}Click Here{/tr}</a>.</div>
</div>
<br />

<div class="cbox">
  <div class="cbox-title">{tr}Home Blog{/tr}</div>
  <div class="cbox-data">
      <form action="tiki-admin.php?page=blogs" method="post">
        <table class="admin"><tr>
          <td class="form"><label for="blogs-home">{tr}Home Blog (main blog){/tr}</label></td>
          <td><select name="homeBlog" id="blogs-home">
              {section name=ix loop=$blogs}
                <option value="{$blogs[ix].blogId|escape}" {if $blogs[ix].blogId eq $prefs.home_blog}selected="selected"{/if}>{$blogs[ix].title|truncate:$prefs.blog_list_title_len:"...":true}</option>
              {/section}
              </select></td>
          <td><input type="submit" name="blogset" value="{tr}OK{/tr}" /></td>
        </tr></table>
      </form>
  </div>
</div>

<div class="cbox">
  <div class="cbox-title">{tr}Blog features{/tr}</div>
  <div class="cbox-data">
      <form action="tiki-admin.php?page=blogs" method="post">
        <table class="admin"><tr>
          <td class="form"><label for="blogs-rankings">{tr}Rankings{/tr}:</label></td>
          <td><input type="checkbox" name="feature_blog_rankings" id="blogs-rankings"
              {if $prefs.feature_blog_rankings eq 'y'}checked="checked"{/if} /></td>
        </tr><tr>
          <td class="form"><label for="blogs-blogcomments">{tr}Blog level comments{/tr}:</label></td>
          <td><input type="checkbox" name="feature_blog_comments" id="blogs-blogcomments"
              {if $prefs.feature_blog_comments eq 'y'}checked="checked"{/if} /></td>
        </tr><tr>
          <td class="form"><label for="blogs-postcomments">{tr}Post level comments{/tr}:</label></td>
          <td><input type="checkbox" name="feature_blogposts_comments" id="blogs-postcomments"
              {if $prefs.feature_blogposts_comments eq 'y'}checked="checked"{/if} /></td>
        </tr><tr>
           <td class="form"><label for="feature_trackbackpings">{tr}Trackbacks Pings{/tr}:</label></td>
          <td><input type="checkbox" name="feature_trackbackpings" id="feature_trackbackpings"
              {if $prefs.feature_trackbackpings eq 'y'}checked="checked"{/if}/></td>       
        </tr><tr>    
          <td class="form"><label for="blogs-postpings">{tr}Post level trackback pings{/tr}:</label></td>
          <td><input type="checkbox" name="feature_blogposts_pings" id="blogs-postpings"
              {if $prefs.feature_blogposts_pings eq 'y'}checked="checked"{/if}/></td>
        </tr><tr>
         <td class="form"><label for="blogs-spell">{tr}Spellchecking{/tr}:</label></td>
          <td>{if $prefs.lib_spellcheck eq 'y'}<input type="checkbox" name="blog_spellcheck" id="blogs-spell"
              {if $prefs.blog_spellcheck eq 'y'}checked="checked"{/if}/>{else}{tr}Not Installed{/tr}{/if}</td>
        </tr><tr>
          <td class="form"><label for="blogs-heading">{tr}Blogs have a custom heading{/tr}:</label></td>
          <td><input type="checkbox" name="feature_blog_heading" id="blogs-heading"
              {if $prefs.feature_blog_heading eq 'y'}checked="checked"{/if} /></td>
        </tr><tr>
          <td class="form"><label for="blogs-order">{tr}Default ordering for blog listing{/tr}:</label></td>
          <td><select name="blog_list_order" id="blogs-order">
              <option value="created_desc" {if $prefs.blog_list_order eq 'created_desc'}selected="selected"{/if}>{tr}Creation date (desc){/tr}</option>
              <option value="lastModif_desc" {if $prefs.blog_list_order eq 'lastModif_desc'}selected="selected"{/if}>{tr}Last modification date (desc){/tr}</option>
              <option value="title_asc" {if $prefs.blog_list_order eq 'title_asc'}selected="selected"{/if}>{tr}Blog title (asc){/tr}</option>
              <option value="posts_desc" {if $prefs.blog_list_order eq 'posts_desc'}selected="selected"{/if}>{tr}Number of posts (desc){/tr}</option>
              <option value="hits_desc" {if $prefs.blog_list_order eq 'hits_desc'}selected="selected"{/if}>{tr}Visits (desc){/tr}</option>
              <option value="activity_desc" {if $prefs.blog_list_order eq 'activity_desc'}selected="selected"{/if}>{tr}Activity (desc){/tr}</option>
              </select></td>
        </tr>
       {if $prefs.feature_categories eq 'y'}
    	  <tr><td class="form">{tr}Force and limit categorization to within subtree of{/tr}:</td>
        <td class="form"><select name="feature_blog_mandatory_category">
        <option value="-1" {if $prefs.feature_blog_mandatory_category eq -1 or $prefs.feature_blog_mandatory_category eq ''}selected="selected"{/if}>{tr}None{/tr}</option>
        <option value="0" {if $prefs.feature_blog_mandatory_category eq 0}selected="selected"{/if}>{tr}All{/tr}</option>
        {section name=ix loop=$catree}
        <option value="{$catree[ix].categId|escape}" {if $catree[ix].categId eq $prefs.feature_blog_mandatory_category}selected="selected"{/if}>{$catree[ix].categpath}</option>
        {/section}
        </select>
        </td></tr>
        {/if}
        <tr>
          <td colspan="2" class="button"><input type="submit" name="blogfeatures"
              value="{tr}Change preferences{/tr}" /></td>
        </tr></table>
      </form>
  </div>
</div>

<div class="cbox">
  <div class="cbox-title">{tr}Blog listing configuration (when listing available blogs){/tr}</div>
  <div class="cbox-data">
      <form method="post" action="tiki-admin.php?page=blogs">
        <table class="admin"><tr>
          <td class="form"><label for="blogs-title">{tr}Title{/tr}</label></td>
          <td class="form"><input type="checkbox" name="blog_list_title" id="blogs-title"
              {if $prefs.blog_list_title eq 'y'}checked="checked"{/if} /></td>
        </tr><tr>
          <td class="form"><label for="blogs-titlelen">{tr}Title length{/tr}: </label></td>
          <td class="form"><input size="3" type="text" name="blog_list_title_len" id="blogs-titlelen"
               value="{$prefs.blog_list_title_len|escape}" /></td>
        </tr><tr>
          <td class="form"><label for="blogs-desc">{tr}Description{/tr}</label></td>
          <td class="form"><input type="checkbox" name="blog_list_description" id="blogs-desc"
              {if $prefs.blog_list_description eq 'y'}checked="checked"{/if} /></td>
        </tr><tr>
          <td class="form"><label for="blogs-creation">{tr}Creation date{/tr}</label></td>
          <td class="form"><input type="checkbox" name="blog_list_created" id="blogs-creation"
              {if $prefs.blog_list_created eq 'y'}checked="checked"{/if} /></td>
        </tr><tr>
          <td class="form"><label for="blogs-lastmod">{tr}Last modification time{/tr}</label></td>
          <td class="form"><input type="checkbox" name="blog_list_lastmodif" id="blogs-lastmod"
              {if $prefs.blog_list_lastmodif eq 'y'}checked="checked"{/if} /></td>
        </tr><tr>
          <td class="form"><label for="blogs-listinguser">{tr}User{/tr}</label></td>
          <td class="form"><select name="blog_list_user" id="blogs-listinguser">
              <option value="disabled" {if $prefs.blog_list_user eq 'disabled'}selected="selected"{/if}>{tr}Disabled{/tr}</option>
	      <option value="text" {if $prefs.blog_list_user eq 'text'}selected="selected"{/if}>{tr}Plain text{/tr}</option>
              <option value="link" {if $prefs.blog_list_user eq 'link'}selected="selected"{/if}>{tr}Link to user information{/tr}</option>
              <option value="avatar" {if $prefs.blog_list_user eq 'avatar'}selected="selected"{/if}>{tr}User avatar{/tr}</option>
              </select></td>
        </tr><tr>
          <td class="form"><label for="blogs-posts">{tr}Posts{/tr}</label></td>
          <td class="form"><input type="checkbox" name="blog_list_posts" id="blogs-posts"
              {if $prefs.blog_list_posts eq 'y'}checked="checked"{/if} /></td>
        </tr><tr>
          <td class="form"><label for="blogs-visits">{tr}Visits{/tr}</label></td>
          <td class="form"><input type="checkbox" name="blog_list_visits" id="blogs-visits"
              {if $prefs.blog_list_visits eq 'y'}checked="checked"{/if} /></td>
        </tr><tr>
          <td class="form"><label for="blogs-activity">{tr}Activity{/tr}</label></td>
          <td class="form"><input type="checkbox" name="blog_list_activity" id="blogs-activity"
              {if $prefs.blog_list_activity eq 'y'}checked="checked"{/if} /></td>
        </tr><tr>
          <td colspan="2" class="button"><input type="submit" name="bloglistconf"
              value="{tr}Change preferences{/tr}" /></td>
        </tr></table>
      </form>
  </div>
</div>

<div class="cbox">
  <div class="cbox-title">{tr}Blog comments settings{/tr}</div>
  <div class="cbox-data">
      <form method="post" action="tiki-admin.php?page=blogs">
        <table class="admin"><tr>
          <td class="form"><label for="blogs-commpage">{tr}Default number of comments per page{/tr}: </label></td>
          <td><input size="5" type="text" name="blog_comments_per_page" id="blogs-commpage"
               value="{$prefs.blog_comments_per_page|escape}" /></td>
        </tr><tr>
          <td class="form"><label for="blogs-commorder">{tr}Comments default ordering{/tr}</label></td>
          <td><select name="blog_comments_default_ordering" id="blogs-commorder">
              <option value="commentDate_desc" {if $prefs.blog_comments_default_ordering eq 'commentDate_desc'}selected="selected"{/if}>{tr}Newest first{/tr}</option>
			  <option value="commentDate_asc" {if $prefs.blog_comments_default_ordering eq 'commentDate_asc'}selected="selected"{/if}>{tr}Oldest first{/tr}</option>
              <option value="points_desc" {if $prefs.blog_comments_default_ordering eq 'points_desc'}selected="selected"{/if}>{tr}Points{/tr}</option>
           </select></td>
        </tr><tr>
          <td colspan="2" class="button"><input type="submit" name="blogcomprefs" value="{tr}Change preferences{/tr}" /></td>
        </tr></table>
      </form>
  </div>
</div>
