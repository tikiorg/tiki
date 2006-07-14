
<div class="rbox" name="tip">
<div class="rbox-title" name="tip">{tr}Tip{/tr}</div>  
<div class="rbox-data" name="tip">{tr}Look under "Articles" on the application menu for links to{/tr} "<a class="rbox-link" href="tiki-admin_topics.php">{tr}Admin topics{/tr}</a>" {tr}and{/tr} "<a class="rbox-link" href="tiki-article_types.php">{tr}Admin types{/tr}</a>".</div>
</div>
<br />

<div class="cbox">
  <div class="cbox-title">{tr}CMS features{/tr}</div>
  <div class="cbox-data">
      <form action="tiki-admin.php?page=cms" method="post">
        <table class="admin"><tr>
          <td class="form"><label for="articles-submission">
				{if $feature_help eq 'y'}<a href="{$helpurl}Articles" target="tikihelp" class="tikihelp" title="{tr}Articles{/tr}">{/if}
				{tr}Submissions{/tr}
				{if $feature_help eq 'y'}</a>{/if}
				:</td>
          <td><input type="checkbox" name="feature_submissions" id="articles-submission"
              {if $feature_submissions eq 'y'}checked="checked"{/if} /></td>

        </tr><tr>
          <td class="form"><label for="articles-rank">{tr}Rankings{/tr}:</label></td>
          <td><input type="checkbox" name="feature_cms_rankings" id="articles-rank"
              {if $feature_cms_rankings eq 'y'}checked="checked"{/if} /></td>
        </tr><tr>
          <td class="form"><label for="articles-comments">{tr}Comments{/tr}:</label></td>
          <td><input type="checkbox" name="feature_article_comments" id="articles-comments"
              {if $feature_article_comments eq 'y'}checked="checked"{/if} /></td>
        </tr><tr>
          <td class="form"><label for="articles-spell">{tr}Spellchecking{/tr}:</label></td>
          <td>{if $lib_spellcheck eq 'y'}<input type="checkbox" name="cms_spellcheck" id="articles-spell"
              {if $cms_spellcheck eq 'y'}checked="checked"{/if} />{else}{tr}Not Installed{/tr}{/if}</td>
        </tr><tr>
          <td class="form"><label for="articles-templates">{tr}Use templates{/tr}:</label></td>
          <td><input type="checkbox" name="feature_cms_templates" id="articles-templates"
              {if $feature_cms_templates eq 'y'}checked="checked"{/if} /></td>
        </tr><tr>
          <td class="form"><label for="articles-print">{tr}Print article{/tr}:</label></td>
          <td><input type="checkbox" name="feature_cms_print" id="articles-print"
              {if $feature_cms_print eq 'y'}checked="checked"{/if} /></td>
        </tr><tr>
          <td colspan="2" class="button"><input type="submit" name="cmsfeatures"
              value="{tr}Change preferences{/tr}" /></td>
        </tr></table>
      </form>
  </div>
</div>

<div class="cbox">
  <div class="cbox-title">{tr}Settings for page articles home{/tr}</div>
  <div class="cbox-data">
      <form method="post" action="tiki-admin.php?page=cms">
        <table class="admin"><tr>
          <td class="form"><label for="articles-maxhome">{tr}Maximum number of articles in home{/tr}: </label></td>
          <td><input size="5" type="text" name="maxArticles" id="articles-maxhome"
               value="{$maxArticles|escape}" /></td>
        </tr><tr>
          <td colspan="2" class="button"><input type="submit" name="cmsprefs"
              value="{tr}Change preferences{/tr}" /></td>
        </tr></table>
      </form>
  </div>
</div>

<div class="cbox">
  <div class="cbox-title">{tr}Article comments settings{/tr}</div>  
  <div class="cbox-data">
      <form method="post" action="tiki-admin.php?page=cms">
        <table class="admin"><tr>
          <td class="form"><label for="articles-commentsnumber">{tr}Default number of comments per page{/tr}: </label></td>
          <td><input size="5" type="text" name="article_comments_per_page" id="articles-commentsnumber"
               value="{$article_comments_per_page|escape}" /></td>
        </tr><tr>
          <td class="form"><label for="articles-commentsorder">{tr}Comments default ordering{/tr}</label></td>
          <td><select name="article_comments_default_ordering" id="articles-commentsorder">
              <option value="commentDate_desc" {if $article_comments_default_ordering eq 'commentDate_desc'}selected="selected"{/if}>{tr}Newest first{/tr}</option>
			  <option value="commentDate_asc" {if $article_comments_default_ordering eq 'commentDate_asc'}selected="selected"{/if}>{tr}Oldest first{/tr}</option>
              <option value="points_desc" {if $article_comments_default_ordering eq 'points_desc'}selected="selected"{/if}>{tr}Points{/tr}</option>
              </select></td>
        </tr><tr>
          <td colspan="2" class="button"><input type="submit"
              name="articlecomprefs" value="{tr}Change preferences{/tr}" /></td>
        </tr></table>
      </form>
  </div>
</div>
   
<div class="cbox">
  <div class="cbox-title">{tr}Fields to display on page{/tr} <a href="tiki-list_articles.php" class="link">{tr}List articles{/tr}</a></div>
  <div class="cbox-data">
      <form method="post" action="tiki-admin.php?page=cms">
        <table class="admin"><tr>
          <td class="form"><label for="articles-title">{tr}Title{/tr}</label></td>
          <td class="form"><input type="checkbox" name="art_list_title" id="articles-title"
              {if $art_list_title eq 'y'}checked="checked"{/if} /></td>
        </tr><tr>
          <td class="form"><label for="articles-type">{tr}Type{/tr}</label></td>
          <td class="form"><input type="checkbox" name="art_list_type" id="articles-type"
              {if $art_list_type eq 'y'}checked="checked"{/if} /></td>
        </tr><tr>
          <td class="form"><label for="articles-topic">{tr}Topic{/tr}</label></td>
          <td class="form"><input type="checkbox" name="art_list_topic" id="articles-topic"
              {if $art_list_topic eq 'y'}checked="checked"{/if} /></td>
        </tr><tr>
          <td class="form"><label for="articles-date">{tr}Publish Date{/tr}</label></td>
          <td class="form"><input type="checkbox" name="art_list_date" id="articles-date"
              {if $art_list_date eq 'y'}checked="checked"{/if} /></td>
        </tr><tr>
          <td class="form"><label for="articles-expire">{tr}Expire Date{/tr}</label></td>
          <td class="form"><input type="checkbox" name="art_list_expire" id="articles-expire"
              {if $art_list_expire eq 'y'}checked="checked"{/if} /></td>
        </tr><tr>
          <td class="form"><label for="articles-visible">{tr}Visible{/tr}</label></td>
          <td class="form"><input type="checkbox" name="art_list_visible" id="articles-visible"
              {if $art_list_visible eq 'y'}checked="checked"{/if} /></td>
        </tr><tr>
          <td class="form"><label for="articles-author">{tr}Author{/tr}</label></td>
          <td class="form"><input type="checkbox" name="art_list_author" id="articles-author"
              {if $art_list_author eq 'y'}checked="checked"{/if} /></td>
        </tr><tr>
          <td class="form"><label for="articles-reads">{tr}Reads{/tr}</label></td>
          <td class="form"><input type="checkbox" name="art_list_reads" id="articles-reads"
              {if $art_list_reads eq 'y'}checked="checked"{/if} /></td>
        </tr><tr>
          <td class="form"><label for="articles-size">{tr}Size{/tr}</label></td>
          <td class="form"><input type="checkbox" name="art_list_size" id="articles-size"
              {if $art_list_size eq 'y'}checked="checked"{/if} /></td>
        </tr><tr>
          <td class="form"><label for="articles-img">{tr}Img{/tr}</label></td>
          <td class="form"><input type="checkbox" name="art_list_img" id="articles-img"
              {if $art_list_img eq 'y'}checked="checked"{/if} /></td>
        </tr><tr>
          <td colspan="2" class="button"><input type="submit" name="artlist" 
              value="{tr}Change preferences{/tr}" /></td>
        </tr></table>
      </form>    
    </div>
  </div>
</div>
