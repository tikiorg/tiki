<div class="cbox">
  <div class="cbox-title">{tr}Blog settings{/tr}</div>
  <div class="cbox-data">
    <div class="simplebox">
      <form action="tiki-admin.php?page=blogs" method="post">
        <table ><tr>
          <td class="form">{tr}Home Blog (main blog){/tr}</td>
          <td><select name="homeBlog">
              {section name=ix loop=$blogs}
                <option value="{$blogs[ix].blogId|escape}" {if $blogs[ix].blogId eq $home_blog}selected="selected"{/if}>{$blogs[ix].title|truncate:20:"...":true}</option>
              {/section}
              </select></td>
        </tr><tr>
          <td align="center" colspan="2"><input type="submit" name="blogset"
              value="{tr}Set prefs{/tr}" /></td>
        </tr></table>
      </form>
    </div>

    <div class="simplebox">
      {tr}Blog features{/tr}<br />
      <form action="tiki-admin.php?page=blogs" method="post">
        <table ><tr>
          <td class="form">{tr}Rankings{/tr}:</td>
          <td><input type="checkbox" name="feature_blog_rankings"
              {if $feature_blog_rankings eq 'y'}checked="checked"{/if}/></td>
        </tr><tr>
          <td class="form">{tr}Blog level comments{/tr}:</td>
          <td><input type="checkbox" name="feature_blog_comments"
              {if $feature_blog_comments eq 'y'}checked="checked"{/if}/></td>
        </tr><tr>
          <td class="form">{tr}Post level comments{/tr}:</td>
          <td><input type="checkbox" name="feature_blogposts_comments"
              {if $feature_blogposts_comments eq 'y'}checked="checked"{/if}/></td>
        </tr><tr>
          <td class="form">{tr}Spellchecking{/tr}:</td>
          <td><input type="checkbox" name="blog_spellcheck"
              {if $blog_spellcheck eq 'y'}checked="checked"{/if}/></td>
        </tr><tr>
          <td class="form">{tr}Default ordering for blog listing{/tr}:</td>
          <td><select name="blog_list_order">
              <option value="created_desc" {if $blog_list_order eq 'created_desc'}selected="selected"{/if}>{tr}Creation date (desc){/tr}</option>
              <option value="lastModif_desc" {if $blog_list_order eq 'lastModif_desc'}selected="selected"{/if}>{tr}Last modification date (desc){/tr}</option>
              <option value="title_asc" {if $blog_list_order eq 'title_asc'}selected="selected"{/if}>{tr}Blog title (asc){/tr}</option>
              <option value="posts_desc" {if $blog_list_order eq 'posts_desc'}selected="selected"{/if}>{tr}Number of posts (desc){/tr}</option>
              <option value="hits_desc" {if $blog_list_order eq 'hits_desc'}selected="selected"{/if}>{tr}Visits (desc){/tr}</option>
              <option value="activity_desc" {if $blog_list_order eq 'activity_desc'}selected="selected"{/if}>{tr}Activity (desc){/tr}</option>
              </select></td>
        </tr><tr>
          <td class="form">{tr}In blog listing show user as{/tr}:</td>
          <td><select name="blog_list_user">
              <option value="text" {if $blog_list_user eq 'text'}selected="selected"{/if}>{tr}Plain text{/tr}</option>
              <option value="link" {if $blog_list_user eq 'link'}selected="selected"{/if}>{tr}Link to user information{/tr}</option>
              <option value="avatar" {if $blog_list_user eq 'avatar'}selected="selected"{/if}>{tr}User avatar{/tr}</option>
              </select></td>
        </tr><tr>
          <td align="center" colspan="2"><input type="submit" name="blogfeatures"
              value="{tr}Set features{/tr}" /></td>
        </tr></table>
      </form>
    </div>

    <div class="simplebox">
      {tr}Blog listing configuration (when listing available blogs){/tr}
      <form method="post" action="tiki-admin.php?page=blogs">
        <table><tr>
          <td class="form">{tr}title{/tr}</td>
          <td class="form"><input type="checkbox" name="blog_list_title"
              {if $blog_list_title eq 'y'}checked="checked"{/if}></td>
        </tr><tr>
          <td class="form">{tr}description{/tr}</td>
          <td class="form"><input type="checkbox" name="blog_list_description"
              {if $blog_list_description eq 'y'}checked="checked"{/if}></td>
        </tr><tr>
          <td class="form">{tr}creation date{/tr}</td>
          <td class="form"><input type="checkbox" name="blog_list_created"
              {if $blog_list_created eq 'y'}checked="checked"{/if}></td>
        </tr><tr>
          <td class="form">{tr}last modification time{/tr}</td>
          <td class="form"><input type="checkbox" name="blog_list_lastmodif"
              {if $blog_list_lastmodif eq 'y'}checked="checked"{/if}></td>
        </tr><tr>
          <td class="form">{tr}user{/tr}</td>
          <td class="form"><input type="checkbox" name="blog_list_user"
              {if $blog_list_user eq 'y'}checked="checked"{/if}></td>
        </tr><tr>
          <td class="form">{tr}posts{/tr}</td>
          <td class="form"><input type="checkbox" name="blog_list_posts"
              {if $blog_list_posts eq 'y'}checked="checked"{/if}></td>
        </tr><tr>
          <td class="form">{tr}visits{/tr}</td>
          <td class="form"><input type="checkbox" name="blog_list_visits"
              {if $blog_list_visits eq 'y'}checked="checked"{/if}></td>
        </tr><tr>
          <td class="form">{tr}activity{/tr}</td>
          <td class="form"><input type="checkbox" name="blog_list_activity"
              {if $blog_list_activity eq 'y'}checked="checked"{/if}></td>
        </tr><tr>
          <td align="center" colspan="2"><input type="submit" name="bloglistconf"
              value="{tr}Change preferences{/tr}" /></td>
        </tr></table>
      </form>
    </div>

    <div class="simplebox">
      {tr}Blog comments settings{/tr}
      <form method="post" action="tiki-admin.php?page=blogs">
        <table><tr>
          <td class="form">{tr}Default number of comments per page{/tr}: </td>
          <td><input size="5" type="text" name="blog_comments_per_page"
               value="{$blog_comments_per_page|escape}" /></td>
        </tr><tr>
          <td class="form">{tr}Comments default ordering{/tr}</td>
          <td><select name="blog_comments_default_ordering">
              <option value="commentDate_desc" {if $blog_comments_default_ordering eq 'commentDate_desc'}selected="selected"{/if}>{tr}Newest first{/tr}</option>
							<option value="commentDate_asc" {if $blog_comments_default_ordering eq 'commentDate_asc'}selected="selected"{/if}>{tr}Oldest first{/tr}</option>
              <option value="points_desc" {if $blog_comments_default_ordering eq 'points_desc'}selected="selected"{/if}>{tr}Points{/tr}</option>
              </select></td>
        </tr><tr>
          <td align="center" colspan="2"><input type="submit" name="blogcomprefs"
              value="{tr}Change preferences{/tr}" /></td>
        </tr></table>
      </form>
    </div>

  </div>
</div>

