{* $Header: /cvsroot/tikiwiki/tiki/templates/tiki-admin-include-search.tpl,v 1.21.2.5 2008-03-20 16:12:04 nyloth Exp $ *}

{if $prefs.feature_search_stats eq 'y'}
  <div class="rbox" name="tip">
    <div class="rbox-title" name="tip">{tr}Tip{/tr}</div>  
    <div class="rbox-data" name="tip">{tr}Search stats{/tr} {tr}can be seen on page{/tr} <a class='rbox-link' target='tikihelp' href='tiki-search_stats.php'>{tr}Search stats{/tr}</a> {tr}in Admin menu{/tr}</div>
  </div>
  <br /> 
{/if}

<div class="cbox">
  <div class="cbox-title">
    {tr}{$crumbs[$crumb]->description}{/tr}
    {help crumb=$crumbs[$crumb]}
  </div>
  <div class="cbox-data">
    <form action="tiki-admin.php?page=search" method="post">
      <table class="admin">
	<tr>
          <td class="form">
            {if $prefs.feature_help eq 'y'}
              <a href="{$prefs.helpurl}Referer+Search+Highlighting" target="tikihelp" class="tikihelp" title="{tr}Referer Search Highlighting{/tr}">
            {/if}
            {tr}Referer Search Highlighting{/tr}:
            {if $prefs.feature_help eq 'y'}</a>{/if}
          </td>
          <td>
            <input type="checkbox" name="feature_referer_highlight"
              {if $prefs.feature_referer_highlight eq 'y'}checked="checked"{/if}/>
          </td>
        </tr>
        <tr>
          <td class="form">
            {tr}Parsed the result (can be slow){/tr}:
          </td>
          <td>
            <input type="checkbox" name="search_parsed_snippet"
              {if $prefs.search_parsed_snippet eq 'y'}checked="checked"{/if}/>
          </td>
        </tr>
          
        <tr>
          <th class="heading" colspan="2" align="center">{tr}Database Search using MySQL 'Full-Text' Feature{/tr}</th>
        </tr>
        
        <tr>
	  <td class="form">
	    {if $prefs.feature_help eq 'y'}
              <a href="{$prefs.helpurl}Search+Admin" target="tikihelp" class="tikihelp" title="{tr}Activate MySQL Full-Text feature{/tr}">
            {/if}
	    {tr}Activate MySQL 'Full-Text' feature{/tr}:
	    {if $prefs.feature_help eq 'y'}</a>{/if}
	  </td>
          <td>
            <input type="checkbox" name="feature_search_fulltext" {if $prefs.feature_search_fulltext eq 'y'}checked="checked"{/if}/>
          </td>
        </tr>
	
        <tr>
          <td class="heading" colspan="2">{tr}Performance issues{/tr}</td>
        </tr>
        
        <tr>
	  <td class="form">
	    {if $prefs.feature_help eq 'y'}
              <a href="{$prefs.helpurl}Search+Admin" target="tikihelp" class="tikihelp" title="{tr}Search may show forbidden results. Much better performance though.{/tr}">
            {/if}
	    {tr}Ignore individual object permissions{/tr}:
	    {if $prefs.feature_help eq 'y'}</a>{/if}
	  </td>
          <td>
            <input type="checkbox" name="feature_search_show_forbidden_obj"
                {if $prefs.feature_search_show_forbidden_obj eq 'y'}checked="checked"{/if}/>
          </td>
        </tr>
        
        <tr>
	  <td class="form">
	    {if $prefs.feature_help eq 'y'}
              <a href="{$prefs.helpurl}Search+Admin" target="tikihelp" class="tikihelp" title="{tr}Search may show forbidden results. Much better performance though.{/tr}">
            {/if}
	    {tr}Ignore category viewing restrictions{/tr}:
            {if $prefs.feature_help eq 'y'}</a>{/if}
	  </td>
          <td>
            <input type="checkbox" name="feature_search_show_forbidden_cat" {if $prefs.feature_search_show_forbidden_cat eq 'y'}checked="checked"{/if}/>
          </td>
        </tr>
        
        <tr>
          <th class="heading" colspan="2" align="center">{tr}Database Independent Full Text Search or Tiki Search{/tr}</th>
        </tr>
	
        <tr>
          <td>{tr}This is activated by default if MySQL 'Full-Text' feature is not activated above{/tr}</td>
          <td>&nbsp;</td>
        </tr>
	
        {if $refresh_index_now neq 'y'}
	  <tr>
	    <td>
              <a href="tiki-admin.php?page=search&amp;refresh_index_now=y" class="link" title="{tr}Refresh wiki search index now{/tr}">
                {tr}Refresh wiki search index now{/tr}
              </a>
            </td>
            <td>&nbsp;</td>
	  </tr>
	{/if}
	
        {if $refresh_tracker_index_now neq 'y' and $prefs.trk_with_mirror_tables neq 'y'}
	  <tr>
            <td>
              <a href="tiki-admin.php?page=search&amp;refresh_tracker_index_now=y" class="link" title="{tr}Refresh trackers search index now{/tr}">{tr}Refresh tracker search index now{/tr}</a>
            </td>
            <td>&nbsp;</td>
          </tr>
	{/if}
      
        {if $refresh_files_index_now neq 'y' and $prefs.trk_with_mirror_tables neq 'y'}
	  <tr>
            <td>
              <a href="tiki-admin.php?page=search&amp;refresh_files_index_now=y" class="link" title="{tr}Refresh files search index now{/tr}">{tr}Refresh files search index now{/tr}</a>
            </td>
            <td>&nbsp;</td>
          </tr>
	{/if}
      
        <tr>
          <td class="heading" colspan="2">{tr}Search features{/tr}</td>
        </tr>
        
        <tr>
	  <td class="form">
            {if $prefs.feature_help eq 'y'}
              <a href="{$prefs.helpurl}Search+Stats" target="tikihelp" class="tikihelp" title="{tr}SearchStats{/tr}">
            {/if}
            {tr}Search stats{/tr}:
            {if $prefs.feature_help eq 'y'}</a>{/if}
          </td>
          <td>
            <input type="checkbox" name="feature_search_stats" {if $prefs.feature_search_stats eq 'y'}checked="checked"{/if}/>
	  </td>
	</tr>
      
        <tr>
          <td class="heading" colspan="2">{tr}Settings for searching content{/tr}</td>
        </tr>
        
        <tr>
          <td class="form">{tr}Search refresh mode{/tr}:</td>
	  <td>
	    <select name="search_refresh_index_mode">
	      <option value="normal" {if $prefs.search_refresh_index_mode eq 'normal'}selected="selected"{/if}>{tr}Normal{/tr}</option>
	      <option value="random" {if $prefs.search_refresh_index_mode eq 'random'}selected="selected"{/if}>{tr}random{/tr}</option>
	    </select>
	  </td>
        </tr>
        
        <tr>
          <td class="form"><label for="search-refresh">{tr}Search refresh rate{/tr}:</label></td>
	  <td>
            <input size="5" type="text" name="search_refresh_rate" id="search-refresh" value="{$prefs.search_refresh_rate|escape}" /> {tr}0 for no refresh{/tr}
          </td>
        </tr>
        
        <tr>
          <td class="form"><label for="search-min_length">{tr}Minimum length of search word{/tr}: </label></td>
          <td>
            <input size="5" type="text" name="search_min_wordlength" id="search-min_length" value="{$prefs.search_min_wordlength|escape}" />
          </td>
        </tr>
        
        <tr>
	  <td class="form"><label for="search-max_words">{tr}Max. number of words containing a syllable{/tr}: </label></td>
	  <td><input size="5" type="text" name="search_max_syllwords" id="search-max_words" value="{$prefs.search_max_syllwords|escape}" /></td>
	</tr>
        
        <tr>
          <td class="form"><label for="search-cache_age">{tr}Max. age in hours of syllable search cache{/tr}: </label></td>
          <td><input size="5" type="text" name="search_syll_age" id="search-cache_age" value="{$prefs.search_syll_age|escape}" /></td>
        </tr>
        
        <tr>
          <td class="form"><label for="search-purge">{tr}LRU list purging rate{/tr}: </label></td>
          <td><input size="5" type="text" name="search_lru_purge_rate" id="search-purge" value="{$prefs.search_lru_purge_rate|escape}" /></td>
        </tr>
        
        <tr>
          <td class="form"><label for="search-list_length">{tr}LRU list length{/tr}: </label></td>
          <td><input size="5" type="text" name="search_lru_length" id="search-list_length" value="{$prefs.search_lru_length|escape}" /></td>
        </tr>
        
        <tr>
          <td colspan="2" class="button"><input type="submit" name="searchprefs" value="{tr}Change settings{/tr}" /></td>
        </tr>
      </table>
    </form>
  </div>
</div>

