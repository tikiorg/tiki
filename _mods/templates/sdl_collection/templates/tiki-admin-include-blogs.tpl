<div class="cbox">
  <div class="cbox-title">{tr}Blog Settings{/tr}</div>
  <div class="cbox-data">
    <div class="simplebox">
      <form action="tiki-admin.php?page=blogs" method="post">
        <table class="admin"><tr>
          <td class="form"><label for="blogs-home">{tr}Home Blog (main blog){/tr}</label></td>
          <td><select name="homeBlog" id="blogs-home">
              {section name=ix loop=$blogs}
                <option value="{$blogs[ix].blogId|escape}" {if $blogs[ix].blogId eq $home_blog}selected="selected"{/if}>{$blogs[ix].title|truncate:20:"...":true}</option>
              {/section}
              </select></td>
          <td><input type="submit" name="blogset" value="{tr}Set{/tr}" /></td>
        </tr></table>
      </form>
    </div>

    <div class="simplebox">
      {tr}Blog features{/tr}<br />
      <form action="tiki-admin.php?page=blogs" method="post">
        <table class="admin"><tr>
          <td class="form"><label for="blogs-rankings">{tr}Rankings{/tr}:</label></td>
          <td><input type="checkbox" name="feature_blog_rankings" id="blogs-rankings"
              {if $feature_blog_rankings eq 'y'}checked="checked"{/if}/></td>
        </tr><tr>
          <td class="form"><label for="blogs-blogcomments">{tr}Blog level comments{/tr}:</label></td>
          <td><input type="checkbox" name="feature_blog_comments" id="blogs-blogcomments"
              {if $feature_blog_comments eq 'y'}checked="checked"{/if}/></td>
        </tr><tr>
          <td class="form"><label for="blogs-postcomments">{tr}Post level comments{/tr}:</label></td>
          <td><input type="checkbox" name="feature_blogposts_comments" id="blogs-postcomments"
              {if $feature_blogposts_comments eq 'y'}checked="checked"{/if}/></td>
        </tr><tr>
          <td class="form"><label for="blogs-spell">{tr}Spellchecking{/tr}:</label></td>
          <td><input type="checkbox" name="blog_spellcheck" id="blogs-spell"
              {if $blog_spellcheck eq 'y'}checked="checked"{/if}/></td>
        </tr><tr>
          <td class="form"><label for="blogs-order">{tr}Default ordering for blog listing{/tr}:</label></td>
          <td><select name="blog_list_order" id="blogs-order">
              <option value="created_desc" {if $blog_list_order eq 'created_desc'}selected="selected"{/if}>{tr}Creation date (desc){/tr}</option>
              <option value="lastModif_desc" {if $blog_list_order eq 'lastModif_desc'}selected="selected"{/if}>{tr}Last modification date (desc){/tr}</option>
              <option value="title_asc" {if $blog_list_order eq 'title_asc'}selected="selected"{/if}>{tr}Blog title (asc){/tr}</option>
              <option value="posts_desc" {if $blog_list_order eq 'posts_desc'}selected="selected"{/if}>{tr}Number of posts (desc){/tr}</option>
              <option value="hits_desc" {if $blog_list_order eq 'hits_desc'}selected="selected"{/if}>{tr}Visits (desc){/tr}</option>
              <option value="activity_desc" {if $blog_list_order eq 'activity_desc'}selected="selected"{/if}>{tr}Activity (desc){/tr}</option>
              </select></td>
        </tr><tr>
          <td class="form"><label for="blogs-listinguser">{tr}In blog listing show user as{/tr}:</label></td>
          <td><select name="blog_list_user" id="blogs-listinguser">
              <option value="text" {if $blog_list_user eq 'text'}selected="selected"{/if}>{tr}Plain text{/tr}</option>
              <option value="link" {if $blog_list_user eq 'link'}selected="selected"{/if}>{tr}Link to user information{/tr}</option>
              <option value="avatar" {if $blog_list_user eq 'avatar'}selected="selected"{/if}>{tr}User avatar{/tr}</option>
              </select></td>
        </tr><tr>
          <td colspan="2" class="button"><input type="submit" name="blogfeatures"
              value="{tr}Change Preferences{/tr}" /></td>
        </tr></table>
      </form>
    </div>

    <div class="simplebox">
      {tr}Blog listing configuration (when listing available blogs){/tr}
      <form method="post" action="tiki-admin.php?page=blogs">
        <table class="admin"><tr>
          <td class="form"><label for="blogs-title">{tr}Title{/tr}</label></td>
          <td class="form"><input type="checkbox" name="blog_list_title" id="blogs-title"
              {if $blog_list_title eq 'y'}checked="checked"{/if}></td>
        </tr><tr>
          <td class="form"><label for="blogs-desc">{tr}Description{/tr}</label></td>
          <td class="form"><input type="checkbox" name="blog_list_description" id="blogs-desc"
              {if $blog_list_description eq 'y'}checked="checked"{/if}></td>
        </tr><tr>
          <td class="form"><label for="blogs-creation">{tr}Creation date{/tr}</label></td>
          <td class="form"><input type="checkbox" name="blog_list_created" id="blogs-creation"
              {if $blog_list_created eq 'y'}checked="checked"{/if}></td>
        </tr><tr>
          <td class="form"><label for="blogs-lastmod">{tr}Last modification time{/tr}</label></td>
          <td class="form"><input type="checkbox" name="blog_list_lastmodif" id="blogs-lastmod"
              {if $blog_list_lastmodif eq 'y'}checked="checked"{/if}></td>
        </tr><tr>
          <td class="form"><label for="blogs-user">{tr}User{/tr}</label></td>
          <td class="form"><input type="checkbox" name="blog_list_user" id="blogs-user"
              {if $blog_list_user eq 'y'}checked="checked"{/if}></td>
        </tr><tr>
          <td class="form"><label for="blogs-posts">{tr}Posts{/tr}</label></td>
          <td class="form"><input type="checkbox" name="blog_list_posts" id="blogs-posts"
              {if $blog_list_posts eq 'y'}checked="checked"{/if}></td>
        </tr><tr>
          <td class="form"><label for="blogs-visits">{tr}Visits{/tr}</label></td>
          <td class="form"><input type="checkbox" name="blog_list_visits" id="blogs-visits"
              {if $blog_list_visits eq 'y'}checked="checked"{/if}></td>
        </tr><tr>
          <td class="form"><label for="blogs-activity">{tr}Activity{/tr}</label></td>
          <td class="form"><input type="checkbox" name="blog_list_activity" id="blogs-activity"
              {if $blog_list_activity eq 'y'}checked="checked"{/if}></td>
        </tr><tr>
          <td colspan="2" class="button"><input type="submit" name="bloglistconf"
              value="{tr}Change Preferences{/tr}" /></td>
        </tr></table>
      </form>
    </div>

    <div class="simplebox">
      {tr}Blog comments settings{/tr}
      <form method="post" action="tiki-admin.php?page=blogs">
        <table class="admin"><tr>
          <td class="form"><label for="blogs-commpage">{tr}Default number of comments per page{/tr}: </label></td>
          <td><input size="5" type="text" name="blog_comments_per_page" id="blogs-commpage"
               value="{$blog_comments_per_page|escape}" /></td>
        </tr><tr>
          <td class="form"><label for="blogs-commorder">{tr}Comments default ordering{/tr}</label></td>
          <td><select name="blog_comments_default_ordering" id="blogs-commorder">
              <option value="commentDate_desc" {if $blog_comments_default_ordering eq 'commentDate_desc'}selected="selected"{/if}>{tr}Newest first{/tr}</option>
			  <option value="commentDate_asc" {if $blog_comments_default_ordering eq 'commentDate_asc'}selected="selected"{/if}>{tr}Oldest first{/tr}</option>
              <option value="points_desc" {if $blog_comments_default_ordering eq 'points_desc'}selected="selected"{/if}>{tr}Points{/tr}</option>
           </select></td>
        </tr><tr>
          <td colspan="2" class="button"><input type="submit" name="blogcomprefs"
              value="{tr}Change Preferences{/tr}" /></td>
        </tr></table>
      </form>
    </div>

  </div>
</div>