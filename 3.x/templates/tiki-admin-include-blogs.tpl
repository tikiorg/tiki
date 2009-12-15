{* $Id$ *}

{remarksbox type="tip" title="{tr}Tip{/tr}"}{tr}To add/remove blogs, go to "Create/Edit Blog" under "Blogs" on the application menu, or{/tr} <a class="rbox-link" href="tiki-edit_blog.php">{tr}Click Here{/tr}</a>.{/remarksbox}

<form action="tiki-admin.php?page=blogs" method="post">
<div class="cbox">
<table class="admin"><tr><td>
<div style="padding:1em;" align="center"><input type="submit" value="{tr}Change preferences{/tr}" /></div>

{if $prefs.feature_tabs eq 'y'}
			{tabs}{strip}
				{tr}General Settings{/tr}|
				{tr}Blogs Listing{/tr}
			{/strip}{/tabs}
{/if}

{cycle name=content values="1,2" print=false advance=false reset=true}

    <fieldset{if $prefs.feature_tabs eq 'y'} class="tabcontent" id="content{cycle name=content assign=focustab}{$focustab}"{/if}>
      {if $prefs.feature_tabs neq 'y'}
        <legend class="heading">
          <a href="#content{cycle name=content assign=focus}{$focus}" onclick="flip('content{$focus}'); return false;">
            <span>{tr}General Settings{/tr}</span>
          </a>
        </legend>
        <div id="content{$focus}" style="display:{if !isset($smarty.session.tiki_cookie_jar.show_content.$focus) and $smarty.session.tiki_cookie_jar.show_content.$focus neq 'y'}none{else}block{/if};">
      {/if}

<div class="adminoptionbox">
	<div class="adminoptionlabel"><label for="home_forum">{tr}Home Blog (main blog){/tr}</label>
	<select name="homeBlog" id="blogs-home"{if !$blogs} disabled="disabled"{/if}>
              {section name=ix loop=$blogs}
                <option value="{$blogs[ix].blogId|escape}" {if $blogs[ix].blogId eq $prefs.home_blog}selected="selected"{/if}>{$blogs[ix].title|truncate:$prefs.blog_list_title_len:"...":true}</option>
{sectionelse}
			<option value="" disabled="disabled" selected="selected">{tr}None{/tr}</option>
              {/section}
	</select>
{if $blogs}<input type="submit" name="blogset" value="{tr}Set{/tr}" />
{else}<a href="tiki-edit_blog.php" class="button" title="{tr}Create a blog{/tr}"> {tr}Create a blog{/tr} </a> {/if}
	</div>
</div>

<fieldset><legend>{tr}Features{/tr}{if $prefs.feature_help eq 'y'} {help url="Blog+Config"}{/if}</legend>
<input type="hidden" name="blogfeatures" />
<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" name="feature_blog_rankings" id="blogs-rankings"
              {if $prefs.feature_blog_rankings eq 'y'}checked="checked"{/if} /></div>
	<div class="adminoptionlabel"><label for="blogs-rankings">{tr}Rankings{/tr}</label></div>
</div>
<div class="adminoptionbox">
	<div class="adminoption">{if $prefs.lib_spellcheck eq 'y'}<input type="checkbox" name="blog_spellcheck" id="blogs-spell"
              {if $prefs.blog_spellcheck eq 'y'}checked="checked"{/if}/>{else}{tr}Not Installed{/tr}{/if}</div>
	<div class="adminoptionlabel"><label for="blogs-spell">{tr}Spell checking{/tr}</label>{if $prefs.feature_help eq 'y'} {help url="Spellcheck"}{/if}<br />
	<em>{tr}Requires a separate download{/tr}. </em></div>
</div>
<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" name="feature_blog_heading" id="blogs-heading"
              {if $prefs.feature_blog_heading eq 'y'}checked="checked"{/if} /></div>
	<div class="adminoptionlabel"><label for="blogs-heading">{tr}Custom blog headings{/tr}</label></div>
</div>
</fieldset>

<fieldset><legend>{tr}Comments{/tr}</legend>
<input type="hidden" name="blogcomprefs" />
<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" name="feature_blog_comments" id="blogs-blogcomments"
              {if $prefs.feature_blog_comments eq 'y'}checked="checked"{/if} /></div>
	<div class="adminoptionlabel"><label for="blogs-blogcomments">{tr}Blog-level{/tr}</label></div>
</div>
<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" name="feature_blogposts_comments" id="blogs-postcomments"
              {if $prefs.feature_blogposts_comments eq 'y'}checked="checked"{/if} /></div>
	<div class="adminoptionlabel"><label for="blogs-postcomments">{tr}Post-level{/tr}</label></div>
</div>
<div class="adminoptionbox">
	<div class="adminoptionlabel"><label for="blogs-commpage">{tr}Default number per page{/tr}: </label><input size="5" type="text" name="blog_comments_per_page" id="blogs-commpage"
               value="{$prefs.blog_comments_per_page|escape}" /></div>
</div>
<div class="adminoptionbox">
	<div class="adminoptionlabel"><label for="blogs-commorder">{tr}Default ordering{/tr}</label>: 
	<select name="blog_comments_default_ordering" id="blogs-commorder">
              <option value="commentDate_desc" {if $prefs.blog_comments_default_ordering eq 'commentDate_desc'}selected="selected"{/if}>{tr}Newest first{/tr}</option>
			  <option value="commentDate_asc" {if $prefs.blog_comments_default_ordering eq 'commentDate_asc'}selected="selected"{/if}>{tr}Oldest first{/tr}</option>
              <option value="points_desc" {if $prefs.blog_comments_default_ordering eq 'points_desc'}selected="selected"{/if}>{tr}Points{/tr}</option>
           </select>
	</div>
</div>
</fieldset>

<fieldset><legend>{tr}Trackback pings{/tr}{if $prefs.feature_help eq 'y'} {help url="Blog#About_Trackback"}{/if}</legend>
<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" name="feature_trackbackpings" id="feature_trackbackpings"
              {if $prefs.feature_trackbackpings eq 'y'}checked="checked" {/if}/></div>
	<div class="adminoptionlabel"><label for="feature_trackbackpings">{tr}Blog-level{/tr}</label></div>
</div>
<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" name="feature_blogposts_pings" id="blogs-postpings"
              {if $prefs.feature_blogposts_pings eq 'y'}checked="checked" {/if}/></div>
	<div class="adminoptionlabel"><label for="blogs-postpings">{tr}Post-level{/tr}</label></div>
</div>
</fieldset>
     {if $prefs.feature_tabs neq 'y'}</div>{/if}
    </fieldset>

    <fieldset{if $prefs.feature_tabs eq 'y'} class="tabcontent" id="content{cycle name=content assign=focustab}{$focustab}"{/if}>
      {if $prefs.feature_tabs neq 'y'}
        <legend class="heading">
          <a href="#content{cycle name=content assign=focus}{$focus}" onclick="flip('content{$focus}'); return false;">
            <span>{tr}Blogs Listings{/tr}</span>
          </a>
        </legend>
        <div id="content{$focus}" style="display:{if !isset($smarty.session.tiki_cookie_jar.show_content.$focus) and $smarty.session.tiki_cookie_jar.show_content.$focus neq 'y'}none{else}block{/if};">
      {/if}
<input type="hidden" name="bloglistconf" />	  
<div class="adminoptionbox">
	<div class="adminoptionlabel"><label for="blogs-order">{tr}Default ordering{/tr}:</label> 
	<select name="blog_list_order" id="blogs-order">
              <option value="created_desc" {if $prefs.blog_list_order eq 'created_desc'}selected="selected"{/if}>{tr}Creation date (desc){/tr}</option>
              <option value="lastModif_desc" {if $prefs.blog_list_order eq 'lastModif_desc'}selected="selected"{/if}>{tr}Last modification date (desc){/tr}</option>
              <option value="title_asc" {if $prefs.blog_list_order eq 'title_asc'}selected="selected"{/if}>{tr}Blog title (asc){/tr}</option>
              <option value="posts_desc" {if $prefs.blog_list_order eq 'posts_desc'}selected="selected"{/if}>{tr}Number of posts (desc){/tr}</option>
              <option value="hits_desc" {if $prefs.blog_list_order eq 'hits_desc'}selected="selected"{/if}>{tr}Visits (desc){/tr}</option>
              <option value="activity_desc" {if $prefs.blog_list_order eq 'activity_desc'}selected="selected"{/if}>{tr}Activity (desc){/tr}</option>
    </select>
	</div>
</div>

{if $prefs.feature_categories eq 'y'}
	<div class="adminoptionbox">
		<div class="adminoptionlabel">
			<label for="blog_mandatory_category"> {tr}Force and limit categorization to within subtree of{/tr}:</label>
			<select name="feature_blog_mandatory_category" id="blog_mandatory_category">
				<option value="-1" {if $prefs.feature_blog_mandatory_category eq -1 or $prefs.feature_blog_mandatory_category eq ''}selected="selected"{/if}>{tr}None{/tr}</option>
				<option value="0" {if $prefs.feature_blog_mandatory_category eq 0}selected="selected"{/if}>{tr}All{/tr}</option>
				{section name=ix loop=$catree}
					<option value="{$catree[ix].categId|escape}" {if $catree[ix].categId eq $prefs.feature_blog_mandatory_category}selected="selected"{/if}>{$catree[ix].categpath}</option>
				{/section}
		  </select>
		</div>
	</div>
{/if}

<div class="adminoptionbox">
{tr}Select which items to display when listing blogs{/tr}:
</div>
<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" name="blog_list_title" id="blogs-title"
              {if $prefs.blog_list_title eq 'y'}checked="checked"{/if} onclick="flip('titlelength');" /></div>
	<div class="adminoptionlabel"><label for="blogs-title">{tr}Title{/tr}</label>
<div class="adminoptionboxchild" id="titlelength" style="display:{if $prefs.blog_list_title eq 'y'}block{else}none{/if};">
	<div class="adminoptionlabel"><label for="blogs-titlelen">{tr}Title length{/tr}: </label><input size="3" type="text" name="blog_list_title_len" id="blogs-titlelen"
               value="{$prefs.blog_list_title_len|escape}" /></div>
</div>
</div>
</div>

<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" name="blog_list_description" id="blogs-desc"
              {if $prefs.blog_list_description eq 'y'}checked="checked"{/if} /></div>
	<div class="adminoptionlabel"><label for="blogs-desc">{tr}Description{/tr}</label></div>
</div>
<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" name="blog_list_created" id="blogs-creation"
              {if $prefs.blog_list_created eq 'y'}checked="checked"{/if} /></div>
	<div class="adminoptionlabel"><label for="blogs-creation">{tr}Creation date{/tr}</label></div>
</div>
<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" name="blog_list_lastmodif" id="blogs-lastmod"
              {if $prefs.blog_list_lastmodif eq 'y'}checked="checked"{/if} /></div>
	<div class="adminoptionlabel"><label for="blogs-lastmod">{tr}Last modified{/tr}</label></div>
</div>
<div class="adminoptionbox">
	<div class="adminoptionlabel"><label for="blogs-listinguser">{tr}User{/tr}: </label>
	<select name="blog_list_user" id="blogs-listinguser">
              <option value="disabled" {if $prefs.blog_list_user eq 'disabled'}selected="selected"{/if}>{tr}Disabled{/tr}</option>
	      <option value="text" {if $prefs.blog_list_user eq 'text'}selected="selected"{/if}>{tr}Plain text{/tr}</option>
              <option value="link" {if $prefs.blog_list_user eq 'link'}selected="selected"{/if}>{tr}Link to user information{/tr}</option>
              <option value="avatar" {if $prefs.blog_list_user eq 'avatar'}selected="selected"{/if}>{tr}User avatar{/tr}</option>
	</select>
	</div>
</div>
<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" name="blog_list_posts" id="blogs-posts"
              {if $prefs.blog_list_posts eq 'y'}checked="checked"{/if} /></div>
	<div class="adminoptionlabel"><label for="blogs-posts">{tr}Posts{/tr}</label></div>
</div>
<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" name="blog_list_visits" id="blogs-visits"
              {if $prefs.blog_list_visits eq 'y'}checked="checked"{/if} /></div>
	<div class="adminoptionlabel"><label for="blogs-visits">{tr}Visits{/tr}</label></div>
</div>
<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" name="blog_list_activity" id="blogs-activity"
              {if $prefs.blog_list_activity eq 'y'}checked="checked"{/if} /></div>
	<div class="adminoptionlabel"><label for="blogs-activity">{tr}Activity{/tr}</label></div>
</div>
     {if $prefs.feature_tabs neq 'y'}</div>{/if}
    </fieldset>

<div style="padding:1em;" align="center"><input type="submit" value="{tr}Change preferences{/tr}" /></div>
</td></tr></table>
</div>
</form>
