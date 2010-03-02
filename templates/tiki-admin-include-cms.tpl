{* $Id$ *}
{popup_init src="lib/overlib.js"}
{remarksbox type="tip" title="{tr}Tip{/tr}"}{tr}Look under "Articles" on the application menu for links to{/tr} "<a class="rbox-link" href="tiki-admin_topics.php">{tr}Admin topics{/tr}</a>" {tr}and{/tr} "<a class="rbox-link" href="tiki-article_types.php">{tr}Admin types{/tr}</a>".{/remarksbox}

{if !empty($msgs)}
	<div class="simplebox highlight">
	{foreach from=$msgs item=msg}
	{$msg}			 
	{/foreach}
	</div>
{/if}

<form method="post" action="tiki-admin.php?page=cms">
<div class="cbox">
<table class="admin"><tr><td>
<div style="padding:1em" align="center"><input type="submit" value="{tr}Change preferences{/tr}" /></div>

{if $prefs.feature_tabs eq 'y'}
			{tabs}{strip}
				{tr}General Settings{/tr}|
				{tr}Articles Listing{/tr}
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

<input type="hidden" name="cmsprefs" />

<div class="adminoptionbox">
	<div class="adminoptionlabel"><label for="art_home_title">{tr}Title of articles home page{/tr}:</label>
	<select name='art_home_title' id='art_home_title'>
				<option value=''></option>
				<option value="topic"{if $prefs.art_home_title eq 'topic'} selected="selected"{/if}>{tr}Topic{/tr}</option>
				<option value="type"{if $prefs.art_home_title eq 'type'} selected="selected"{/if}>{tr}Type{/tr}</option>
				<option value="articles"{if $prefs.art_home_title eq 'articles'} selected="selected"{/if}>'{tr}Articles{/tr}'</option>
	</select>
	</div>
</div>

<div class="adminoptionbox">
	<div class="adminoptionlabel"><label for="articles-maxhome">{tr}Maximum number of articles on articles home page{/tr}:</label> <input size="5" type="text" name="maxArticles" id="articles-maxhome"
               value="{$prefs.maxArticles|escape}" />
	
	</div>
</div>

<fieldset><legend>{tr}Features{/tr}{if $prefs.feature_help eq 'y'}  {help url="Articles+Config"}{/if}</legend>

<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" name="feature_submissions" id="articles-submission"
              {if $prefs.feature_submissions eq 'y'}checked="checked"{/if} /></div>
	<div class="adminoptionlabel"><label for="articles-submission">{tr}Submissions{/tr}</label>{if $prefs.feature_help eq 'y'} {help url="Articles"}{/if}</div>
</div>

<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" name="feature_cms_rankings" id="articles-rank"
              {if $prefs.feature_cms_rankings eq 'y'}checked="checked"{/if} /></div>
	<div class="adminoptionlabel"><label for="articles-rank">{tr}Rankings{/tr}</label></div>
</div>

<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" name="feature_article_comments" id="articles-comments"
              {if $prefs.feature_article_comments eq 'y'}checked="checked"{/if} onclick="flip('cmscomments');" /></div>
	<div class="adminoptionlabel"><label for="articles-comments">{tr}Comments{/tr}</label></div>
<input type="hidden" name="articlecomprefs" />
<div class="adminoptionboxchild" id="cmscomments" style="display:{if $prefs.feature_article_comments eq 'y'}block{else}none{/if};">
<div class="adminoptionbox">
	<div class="adminoptionlabel"><label for="articles-commentsnumber">{tr}Default number per page{/tr}:</label> 
	<input size="5" type="text" name="article_comments_per_page" id="articles-commentsnumber"
               value="{$prefs.article_comments_per_page|escape}" />
	</div>
</div>

<div class="adminoptionbox">
	<div class="adminoptionlabel"><label for="articles-commentsorder">{tr}Default ordering{/tr}:</label> 
	<select name="article_comments_default_ordering" id="articles-commentsorder">
              <option value="commentDate_desc" {if $prefs.article_comments_default_ordering eq 'commentDate_desc'}selected="selected"{/if}>{tr}Newest first{/tr}</option>
			  <option value="commentDate_asc" {if $prefs.article_comments_default_ordering eq 'commentDate_asc'}selected="selected"{/if}>{tr}Oldest first{/tr}</option>
              <option value="points_desc" {if $prefs.article_comments_default_ordering eq 'points_desc'}selected="selected"{/if}>{tr}Points{/tr}</option>
	</select>
	</div>
</div>

</div>	
</div>

<div class="adminoptionbox">
	<div class="adminoption">{if $prefs.lib_spellcheck eq 'y'}<input type="checkbox" name="cms_spellcheck" id="articles-spell"
              {if $prefs.cms_spellcheck eq 'y'}checked="checked"{/if} />{else}{tr}Not Installed{/tr}{/if}</div>
	<div class="adminoptionlabel"><label for="articles-spell">{tr}Spell checking{/tr}</label>{if $prefs.feature_help eq 'y'}  {help url="Spellcheck"}{/if}<br /><em>{tr}Requires a separate download{/tr}.</em></div>
</div>
			</fieldset>
			
			<fieldset>
				<legend>
					{tr}Article properties{/tr}
				</legend>
				{remarksbox type="tip" title="{tr}Tip{/tr}"}
					{tr}Give only one value (width or height) to keep the image proportions{/tr}
				{/remarksbox}

<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" name="feature_cms_templates" id="articles-templates"
              {if $prefs.feature_cms_templates eq 'y'}checked="checked"{/if} /></div>
	<div class="adminoptionlabel"><label for="articles-templates">Content templates</label>{if $prefs.feature_help eq 'y'}  {help url="Content+Template"}{/if}</div>
</div>  
  
<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" name="feature_cms_print" id="articles-print"
              {if $prefs.feature_cms_print eq 'y'}checked="checked"{/if} /></div>
	<div class="adminoptionlabel"><label for="articles-print">{tr}Print{/tr}</label></div>
</div>
  
<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" name="feature_cms_emails" id="articles-emails"
              {if $prefs.feature_cms_emails eq 'y'}checked="checked"{/if} /></div>
	<div class="adminoptionlabel"><label for="articles-emails">{tr}Specify notification emails when creating articles{/tr}.</label></div>
</div>
<input type="hidden" name="cmsfeatures" />
			</fieldset>

			<fieldset>
				<legend>
					{tr}Article properties{/tr}
				</legend>
				{remarksbox type="tip" title="{tr}Tip{/tr}"}
					{tr}Give only one value (width or height) to keep the image proportions{/tr}
				{/remarksbox}
				<div class="adminoptionbox">
					<div class="adminoptionlabel">
						<label for="article_image_sizex">{tr}Default article image width{/tr}</label>
						<input size="3" type="text" name="article_image_size_x" id="article_image_sizex" value="{$prefs.article_image_size_x|escape}" />
					</div>
				</div>

				<div class="adminoptionbox">
					<div class="adminoptionlabel">
						<label for="article_image_sizey">{tr}Default article image height{/tr}</label>
						<input size="3" type="text" name="article_image_size_y" id="article_image_sizey" value="{$prefs.article_image_size_y|escape}" />
					</div>
				</div>
				<input type="hidden" name="artprops" />
			</fieldset>

<fieldset><legend>{tr}Import CSV file{/tr}</legend>
<div class="adminoptionbox">
	<div class="adminoptionlabel"><label for="csvlist">{tr}Batch upload (CSV file){/tr}:</label> <input type="file" name="csvlist" id="csvlist" /> 
	<br /><em>{tr}File format: title,authorName,heading,body,lang,user{/tr}....</em>
	<div align="center"><input type="submit" name="import" value="{tr}Import{/tr}" /></div>
	</div>
</div>
</fieldset>



     {if $prefs.feature_tabs neq 'y'}</div>{/if}
    </fieldset>

    <fieldset{if $prefs.feature_tabs eq 'y'} class="tabcontent" id="content{cycle name=content assign=focustab}{$focustab}"{/if}>
      {if $prefs.feature_tabs neq 'y'}
        <legend class="heading">
          <a href="#content{cycle name=content assign=focus}{$focus}" onclick="flip('content{$focus}'); return false;">
            <span>{tr}Articles Listing{/tr}</span>
          </a>
        </legend>
        <div id="content{$focus}" style="display:{if !isset($smarty.session.tiki_cookie_jar.show_content.$focus) and $smarty.session.tiki_cookie_jar.show_content.$focus neq 'y'}none{else}block{/if};">
      {/if}
<div class="adminoptionbox">
{tr}Select which items to display when listing articles{/tr}: 	  
</div>
<input type="hidden" name="artlist" />
<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" name="art_list_title" id="articles-title"
              {if $prefs.art_list_title eq 'y'}checked="checked"{/if} onclick="flip('titlelength');" /></div>
	<div class="adminoptionlabel"><label for="articles-title">{tr}Title{/tr}</label></div>
<div class="adminoptionboxchild" id="titlelength" style="display:{if $prefs.art_list_title eq 'y'}block{else}none{/if};">
	<div class="adminoption"><label for="articles-titlelen">{tr}Title length{/tr}: </label> <input size="5" type="text" name="art_list_title_len" id="articles-titlelen"
               value="{$prefs.art_list_title_len|escape}" /></div>
</div>
</div>

<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" name="art_list_type" id="articles-type"
              {if $prefs.art_list_type eq 'y'}checked="checked"{/if} /></div>
	<div class="adminoptionlabel"><label for="articles-type">{tr}Type{/tr}</label></div>
</div>

<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" name="art_list_topic" id="articles-topic"
              {if $prefs.art_list_topic eq 'y'}checked="checked"{/if} /></div>
	<div class="adminoptionlabel"><label for="articles-topic">{tr}Topic{/tr}</label></div>
</div>

<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" name="art_list_date" id="articles-date"
              {if $prefs.art_list_date eq 'y'}checked="checked"{/if} /></div>
	<div class="adminoptionlabel"><label for="articles-date">{tr}Publication date{/tr}</label></div>
</div>

<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" name="art_list_expire" id="articles-expire"
              {if $prefs.art_list_expire eq 'y'}checked="checked"{/if} /></div>
	<div class="adminoptionlabel"><label for="articles-expire">{tr}Expiration date{/tr}</label></div>
</div>

<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" name="art_list_visible" id="articles-visible"
              {if $prefs.art_list_visible eq 'y'}checked="checked"{/if} /></div>
	<div class="adminoptionlabel"><label for="articles-visible">{tr}Visible{/tr}</label></div>
</div>

<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" name="art_list_lang" id="articles-lang"
              {if $prefs.art_list_lang eq 'y'}checked="checked"{/if} /></div>
	<div class="adminoptionlabel"><label for="articles-lang">{tr}Language{/tr}</label></div>
</div>

<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" name="art_list_author" id="articles-author"
              {if $prefs.art_list_author eq 'y'}checked="checked"{/if} /></div>
	<div class="adminoptionlabel"><label for="articles-author">{tr}Author{/tr}</label></div>
</div>

<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" name="art_list_reads" id="articles-reads"
              {if $prefs.art_list_reads eq 'y'}checked="checked"{/if} /></div>
	<div class="adminoptionlabel"><label for="articles-reads">{tr}Reads{/tr}</label></div>
</div>

<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" name="art_list_size" id="articles-size"
              {if $prefs.art_list_size eq 'y'}checked="checked"{/if} /></div>
	<div class="adminoptionlabel"><label for="articles-size">{tr}Size{/tr}</label></div>
</div>

<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" name="art_list_img" id="articles-img"
              {if $prefs.art_list_img eq 'y'}checked="checked"{/if} /></div>
	<div class="adminoptionlabel"><label for="articles-img">{tr}Image{/tr}</label>s</div>
</div>

<div class="adminoptionbox">
	<div class="adminoption"></div>
	<div class="adminoptionlabel"></div>
</div>
	  
     {if $prefs.feature_tabs neq 'y'}</div>{/if}
    </fieldset>

<div style="padding:1em" align="center"><input type="submit" value="{tr}Change preferences{/tr}" /></div>
	
</td></tr></table>
</div>
</form>

