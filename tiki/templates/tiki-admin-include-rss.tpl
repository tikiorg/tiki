
<div class="rbox" name="tip">
  <div class="rbox-title" name="tip">{tr}Tip{/tr}</div>  
  <div class="rbox-data" name="tip">{tr}This page is to configure settings of RSS feeds generated/exported by Tiki. To read/import RSS feeds, look for "RSS modules" on the application menu, or{/tr} <a class="rbox-link" href="tiki-admin_rssmodules.php">{tr}Click Here{/tr}</a>.</div>
</div>

<br />

<div class="cbox">
  <div class="cbox-title">
    {tr}{$crumbs[$crumb]->title}{/tr}
    {help crumb=$crumbs[$crumb]}
  </div>

  <div class="cbox-data">
    <form action="tiki-admin.php?page=rss" method="post">
      <table class="admin">
        <tr>
          <td class="form"><b>{tr}Feed{/tr}</b></td>
          <td class="form"><b>{tr}Enable{/tr}/{tr}Disable{/tr}</b></td>
          <td class="form"><b>{tr}Max number of items{/tr}</b></td>
	  <td class='form'><b>{tr}Title{/tr}</b></td>
	  <td class='form'><b>{tr}Description{/tr}</b></td>
        </tr>
        
        <tr>
          <td class="form"><a href="tiki-articles_rss.php">{tr}Feed for Articles{/tr}</a>:</td>
          <td><input type="checkbox" name="rss_articles" {if $prefs.rss_articles eq 'y'}checked="checked"{/if}/></td>
          <td class="form"><input type="text" name="max_rss_articles" size="5" value="{$prefs.max_rss_articles|escape}" /></td>
          <td><input type="text" name="title_rss_articles" size="20" maxlength="255" value='{$prefs.title_rss_articles|escape}' /></td>
          <td><textarea name="desc_rss_articles" cols="20" rows="2">{$prefs.desc_rss_articles|escape}</textarea></td>
        </tr>
        
        <tr>
          <td class="form"><a href="tiki-blogs_rss.php">{tr}Feed for Weblogs{/tr}</a>:</td>
          <td><input type="checkbox" name="rss_blogs" {if $prefs.rss_blogs eq 'y'}checked="checked"{/if}/></td>
          <td><input type="text" name="max_rss_blogs" size="5" value="{$prefs.max_rss_blogs|escape}" /></td>
          <td><input type='text' name='title_rss_blogs' size='20' maxlength='255' value='{$prefs.title_rss_blogs|escape}' /></td>
          <td><textarea name='desc_rss_blogs' cols='20' rows="2">{$prefs.desc_rss_blogs|escape}</textarea></td>
        </tr>
        
        <tr>
          <td class="form"><a href="tiki-image_galleries_rss.php">{tr}Feed for Image Galleries{/tr}</a>:</td>
          <td><input type="checkbox" name="rss_image_galleries" {if $prefs.rss_image_galleries eq 'y'}checked="checked"{/if}/></td>
          <td><input type="text" name="max_rss_image_galleries" size="5" value="{$prefs.max_rss_image_galleries|escape}" /></td>
          <td><input type='text' name='title_rss_image_galleries' size='20' maxlength='255' value='{$prefs.title_rss_image_galleries|escape}' /></td>
          <td><textarea name='desc_rss_image_galleries' cols='20' rows="2">{$prefs.desc_rss_image_galleries|escape}</textarea></td>
        </tr>
        
        <tr>
          <td class="form"><a href="tiki-file_galleries_rss.php">{tr}Feed for File Galleries{/tr}</a>:</td>
          <td><input type="checkbox" name="rss_file_galleries" {if $prefs.rss_file_galleries eq 'y'}checked="checked"{/if}/></td>
          <td><input type="text" name="max_rss_file_galleries" size="5" value="{$prefs.max_rss_file_galleries|escape}" /></td>
          <td><input type='text' name='title_rss_file_galleries' size='20' maxlength='255' value='{$prefs.title_rss_file_galleries|escape}' /></td>
          <td><textarea name='desc_rss_file_galleries' cols='20' rows="2">{$prefs.desc_rss_file_galleries|escape}</textarea></td>
        </tr>
        
        <tr>
          <td class="form"><a href="tiki-wiki_rss.php">{tr}Feed for the Wiki{/tr}</a>:</td>
          <td><input type="checkbox" name="rss_wiki" {if $prefs.rss_wiki eq 'y'}checked="checked"{/if}/></td>
          <td><input type="text" name="max_rss_wiki" size="5" value="{$prefs.max_rss_wiki|escape}" /></td>
          <td><input type='text' name='title_rss_wiki' size='20' maxlength='255' value='{$prefs.title_rss_wiki|escape}' /></td>
          <td><textarea name='desc_rss_wiki' cols='20' rows="2">{$prefs.desc_rss_wiki|escape}</textarea></td>
        </tr>
        
        <tr>
          <td class="form">{tr}Feed for individual Image Galleries{/tr}:</td>
          <td><input type="checkbox" name="rss_image_gallery" {if $prefs.rss_image_gallery eq 'y'}checked="checked"{/if}/></td>
          <td><input type="text" name="max_rss_image_gallery" size="5" value="{$prefs.max_rss_image_gallery|escape}" /></td>
          <td><input type='text' name='title_rss_image_gallery' size='20' maxlength='255' value='{$prefs.title_rss_image_gallery|escape}' /></td>
          <td><textarea name='desc_rss_image_gallery' cols='20' rows="2">{$prefs.desc_rss_image_gallery|escape}</textarea></td>
        </tr>
        
        <tr>
          <td class="form">{tr}Feed for individual File Galleries{/tr}:</td>
          <td><input type="checkbox" name="rss_file_gallery" {if $prefs.rss_file_gallery eq 'y'}checked="checked"{/if}/></td>
          <td><input type="text" name="max_rss_file_gallery" size="5" value="{$prefs.max_rss_file_gallery|escape}" /></td>
          <td><input type='text' name='title_rss_file_gallery' size='20' maxlength='255' value='{$prefs.title_rss_file_gallery|escape}' /></td>
          <td><textarea name='desc_rss_file_gallery' cols='20' rows="2">{$prefs.desc_rss_file_gallery|escape}</textarea></td>
        </tr>
        
        <tr>
          <td class="form">{tr}Feed for individual weblogs{/tr}:</td>
          <td><input type="checkbox" name="rss_blog" {if $prefs.rss_blog eq 'y'}checked="checked"{/if}/></td>
          <td><input type="text" name="max_rss_blog" size="5" value="{$prefs.max_rss_blog|escape}" /></td>
          <td><input type='text' name='title_rss_blog' size='20' maxlength='255' value='{$prefs.title_rss_blog|escape}' /></td>
          <td><textarea name='desc_rss_blog' cols='20' rows="2">{$prefs.desc_rss_blog|escape}</textarea></td>
        </tr>
        
        <tr>
          <td class="form"><a href="tiki-forums_rss.php">{tr}Feed for forums{/tr}</a>:</td>
          <td><input type="checkbox" name="rss_forums" {if $prefs.rss_forums eq 'y'}checked="checked"{/if}/></td>
          <td><input type="text" name="max_rss_forums" size="5" value="{$prefs.max_rss_forums|escape}" /></td>
          <td><input type='text' name='title_rss_forums' size='20' maxlength='255' value='{$prefs.title_rss_forums|escape}' /></td>
          <td><textarea name='desc_rss_forums' cols='20' rows="2">{$prefs.desc_rss_forums|escape}</textarea></td>
        </tr>
        
        <tr>
          <td class="form">{tr}Feed for individual forums{/tr}:</td>
          <td><input type="checkbox" name="rss_forum" {if $prefs.rss_forum eq 'y'}checked="checked"{/if}/></td>
          <td><input type="text" name="max_rss_forum" size="5" value="{$prefs.max_rss_forum|escape}" /></td>
          <td><input type='text' name='title_rss_forum' size='20' maxlength='255' value='{$prefs.title_rss_forum|escape}' /></td>
          <td><textarea name='desc_rss_forum' cols='20' rows="2">{$prefs.desc_rss_forum|escape}</textarea></td>
        </tr>
        
        <tr>
          <td class="form"><a href="tiki-map_rss.php">{tr}Feed for mapfiles{/tr}</a>:</td>
          <td><input type="checkbox" name="rss_mapfiles" {if $prefs.rss_mapfiles eq 'y'}checked="checked"{/if}/></td>
          <td><input type="text" name="max_rss_mapfiles" size="5" value="{$prefs.max_rss_mapfiles|escape}" /></td>
          <td><input type='text' name='title_rss_mapfiles' size='20' maxlength='255' value='{$prefs.title_rss_mapfiles|escape}' /></td>
          <td><textarea name='desc_rss_mapfiles' cols='20' rows="2">{$prefs.desc_rss_mapfiles|escape}</textarea></td>
        </tr>
        
        <tr>
          <td class="form"><a href="tiki-directories_rss.php">{tr}Feed for directories{/tr}</a>:</td>
          <td><input type="checkbox" name="rss_directories" {if $prefs.rss_directories eq 'y'}checked="checked"{/if}/></td>
          <td><input type="text" name="max_rss_directories" size="5" value="{$prefs.max_rss_directories|escape}" /></td>
          <td><input type='text' name='title_rss_directories' size='20' maxlength='255' value='{$prefs.title_rss_directories|escape}' /></td>
          <td><textarea name='desc_rss_directories' cols='20' rows="2">{$prefs.desc_rss_directories|escape}</textarea></td>
        </tr>
        
        <tr>
          <td class="form">{tr}Feed for individual tracker items{/tr}:</td>
          <td><input type="checkbox" name="rss_tracker" {if $prefs.rss_tracker eq 'y'}checked="checked"{/if}/></td>
          <td><input type="text" name="max_rss_tracker" size="5" value="{$prefs.max_rss_tracker|escape}" /></td>
          <td><input type='text' name='title_rss_tracker' size='20' maxlength='255' value='{$prefs.title_rss_tracker|escape}' /></td>
          <td><textarea name='desc_rss_tracker' cols='20' rows="2">{$prefs.desc_rss_tracker|escape}</textarea></td>
        </tr>
        
        <tr>
          <td class="form">{tr}Feed for tracker items{/tr}:</td>
          <td><input type="checkbox" name="rss_trackers" {if $prefs.rss_trackers eq 'y'}checked="checked"{/if}/></td>
          <td><input type="text" name="max_rss_trackers" size="5" value="{$prefs.max_rss_trackers|escape}" /></td>
          <td><input type='text' name='title_rss_trackers' size='20' maxlength='255' value='{$prefs.title_rss_trackers|escape}' /></td>
          <td><textarea name='desc_rss_trackers' cols='20' rows="2">{$prefs.desc_rss_trackers|escape}</textarea></td>
        </tr>
        
        <tr>
          <td class="form"><a href="tiki-calendars_rss.php">{tr}Feed for upcoming calendar events{/tr}</a>:</td>
          <td><input type="checkbox" name="rss_calendar" {if $prefs.rss_calendar eq 'y'}checked="checked"{/if}/></td>
          <td><input type="text" name="max_rss_calendar" size="5" value="{$prefs.max_rss_calendar|escape}" /></td>
          <td><input type='text' name='title_rss_calendar' size='20' maxlength='255' value='{$prefs.title_rss_calendar|escape}' /></td>
          <td><textarea name="desc_rss_calendar" cols="20" rows="2">{$prefs.desc_rss_calendar|escape}</textarea></td>
        </tr>

        <tr>
          <td class="form" colspan="3">&nbsp;</td>
        </tr>
        
        <tr>
          <td class="form">{tr}Default RDF version{/tr}:</td>
          <td class="form">
            <select name="rssfeed_default_version" id="rssfeed_default_version">
              <option value="9" {if $prefs.rssfeed_default_version eq "9"}selected="selected"{/if}> RSS 0.91 </option>
              <option value="1" {if $prefs.rssfeed_default_version eq "1"}selected="selected"{/if}> RSS 1.0 </option>
              <option value="2" {if $prefs.rssfeed_default_version eq "2"}selected="selected"{/if}> RSS 2.0 </option>
              <option value="3" {if $prefs.rssfeed_default_version eq "3"}selected="selected"{/if}> PIE0.1 </option>
              <option value="4" {if $prefs.rssfeed_default_version eq "4"}selected="selected"{/if}> MBOX </option>
              <option value="5" {if $prefs.rssfeed_default_version eq "5"}selected="selected"{/if}> ATOM 0.3 </option>
              <option value="6" {if $prefs.rssfeed_default_version eq "6"}selected="selected"{/if}> OPML </option>
              <option value="7" {if $prefs.rssfeed_default_version eq "7"}selected="selected"{/if}> HTML </option>
              <option value="8" {if $prefs.rssfeed_default_version eq "8"}selected="selected"{/if}> JS </option>
            </select>
          </td>
          <td class="form" colspan="3">
            {tr}Specification{/tr} 
              <a href="http://www.w3.org/TR/rdf-schema/" target="tikihelp" class="tikihelp" title="{tr}Specification{/tr}: RDF 1.0">RDF 1.0</a>,
              <a href="http://blogs.law.harvard.edu/tech/rss" target="tikihelp" class="tikihelp" title="{tr}Specification{/tr}: RDF 2.0">RDF 2.0</a>,
              <a href="http://bitworking.org/rfc/draft-gregorio-07.html" target="tikihelp" class="tikihelp" title="{tr}Specification{/tr}: Atom 0.3">Atom 0.3</a>
          </td>
        </tr>
        
        <tr>
          <td class="form">
            <a href="http://blogs.law.harvard.edu/tech/rss#optionalChannelElements" target="tikihelp" class="tikihelp" title="{tr}Documentation{/tr}: RDF">{tr}Language{/tr}</a>:
          </td>
          <td class="form" colspan="4">
            <input type="text" name="rssfeed_language" size="10" value="{$prefs.rssfeed_language|escape}" />
          </td>
        </tr>
        
        <tr>
          <td class="form" colspan="5">&nbsp;</td>
        </tr>
        
        <tr>
          <td class="form">
            <a href="http://blogs.law.harvard.edu/tech/rss#optionalChannelElements" target="tikihelp" class="tikihelp" title="{tr}Documentation{/tr}: RDF">{tr}Editor{/tr}</a>:
          </td>
          <td colspan="4">
            <input type="text" name="rssfeed_editor" size="50" value="{$prefs.rssfeed_editor|escape}" />
          </td>
        </tr>
        
        <tr>
          <td class="form">
            <a href="http://blogs.law.harvard.edu/tech/rss#optionalChannelElements" target="tikihelp" class="tikihelp" title="{tr}Documentation{/tr}: RDF">{tr}Webmaster{/tr}</a>:
          </td>
          <td colspan="4">
            <input type="text" name="rssfeed_webmaster" size="50" value="{$prefs.rssfeed_webmaster|escape}" />
          </td>
        </tr>
        
        <tr>
          <td class="form">{tr}Caching time :{/tr}</td>
          <td colspan="4">
            <input type="text" name="rss_cache_time" size="4" value="{$prefs.rss_cache_time}" /> {tr}seconds (0 = cache inactive){/tr}
          </td>
        </tr>

        <tr>
          <td class="form">&nbsp;</td><td class="form" colspan="4">
	    <div class="rbox" name="tip">
	      <div class="rbox-title" name="tip">{tr}Tip{/tr}</div>  
	      <div class="rbox-data" name="tip">{tr}Only enable caching if all syndicated content is public (visible to everyone) or private documents might leak out. Cache ignores existing permissions.{/tr}</div>
	    </div>
	  </td>
        </tr>
		
        <tr>
          <td colspan="5" class="button"><input type="submit" name="rss" value="{tr}Change preferences{/tr}" /></td>
        </tr>
      </table>
    </form>
  </div>
</div>
