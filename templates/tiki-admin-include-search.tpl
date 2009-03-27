{* $Id$ *}

{if $prefs.feature_search_stats eq 'y'}
  {remarksbox type="tip" title="{tr}Tip{/tr}"}{tr}Search stats{/tr} {tr}can be seen on page{/tr} <a class='rbox-link' target='tikihelp' href='tiki-search_stats.php'>{tr}Search stats{/tr}</a> {tr}in Admin menu{/tr}{/remarksbox}
{/if}


<form action="tiki-admin.php?page=search" method="post">
<input type="hidden" name="searchprefs" />
<div class="cbox">
<table class="admin"><tr><td>
<div style="padding:1em;" align="center"><input type="submit" value="{tr}Change preferences{/tr}" /></div>

{if $prefs.feature_tabs eq 'y'}
			{tabs}{strip}
				{tr}General Settings{/tr}|
				{tr}Search Results{/tr}
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
	  
<fieldset><legend>{tr}Search type{/tr}{if $prefs.feature_help eq 'y'} {help url="Search+Admin"}{/if}</legend>
<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" id="feature_search_fulltext" name="feature_search_fulltext" {if $prefs.feature_search_fulltext eq 'y'}checked="checked" {/if}onclick="flip('searchrefresh');flip('autosearchrefresh');" /></div>
	<div class="adminoptionlabel"><label for="feature_search_fulltext">{tr}Database search{/tr}</label>{if $prefs.feature_help eq 'y'} {help url="Search"}{/if}
	<br /><em>{tr}This search uses the MySQL Full-Text feature{/tr}.</em></div>
</div>
<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" checked="checked" disabled="disabled" /></div>
	<div class="adminoptionlabel"><label for="">{tr}Tiki search{/tr}</label>
	<br /><em>{tr}This database-independent search is always enabled{/tr}.</em></div>

<div class="adminoptionboxchild" id="autosearchrefresh" style="display:{if $prefs.feature_search_fulltext eq 'y'}none{else}block{/if};">
<div class="adminoptionbox">
	<div>{tr}Specify the Tiki search settings{/tr}:</div>
</div>
<div class="adminoptionbox">
	<div class="adminoptionlabel"><label for="search_refresh_index_mode">{tr}Refresh mode{/tr}: </label>
	<select name="search_refresh_index_mode" id="search_refresh_index_mode">
	      <option value="normal" {if $prefs.search_refresh_index_mode eq 'normal'}selected="selected"{/if}>{tr}Normal{/tr}</option>
	      <option value="random" {if $prefs.search_refresh_index_mode eq 'random'}selected="selected"{/if}>{tr}random{/tr}</option>
	</select>
	</div>
</div>

<div class="adminoptionbox">
	<div class="adminoptionlabel"><label for="search-refresh">{tr}Refresh rate{/tr}:</label> <input size="5" type="text" name="search_refresh_rate" id="search-refresh" value="{$prefs.search_refresh_rate|escape}" /><br /><em>{tr}Use <strong>0</strong> for no refresh{/tr}.</em></div>
</div>

<div class="adminoptionbox">
	<div class="adminoptionlabel"><label for="search-min_length">{tr}Minimum length of search word{/tr}: <input size="5" type="text" name="search_min_wordlength" id="search-min_length" value="{$prefs.search_min_wordlength|escape}" /></label></div>
</div>

<div class="adminoptionbox">
	<div class="adminoptionlabel"><label for="search-max_words">{tr}Max. number of words containing a syllable{/tr}: </label><input size="5" type="text" name="search_max_syllwords" id="search-max_words" value="{$prefs.search_max_syllwords|escape}" /></div>
</div>

<div class="adminoptionbox">
	<div class="adminoptionlabel"><label for="search-cache_age">{tr}Max. age in hours of syllable search cache{/tr}: <input size="5" type="text" name="search_syll_age" id="search-cache_age" value="{$prefs.search_syll_age|escape}" /></label></div>
</div>

<div class="adminoptionbox">
	<div class="adminoptionlabel"><label for="search-purge">{tr}LRU list purging rate{/tr}: </label><input size="5" type="text" name="search_lru_purge_rate" id="search-purge" value="{$prefs.search_lru_purge_rate|escape}" /></div>
</div>
<div class="adminoptionbox">
	<div class="adminoptionlabel"><label for="search-list_length">{tr}LRU list length{/tr}: </label><input size="5" type="text" name="search_lru_length" id="search-list_length" value="{$prefs.search_lru_length|escape}" /></div>
</div>
</div>	
	
<div class="adminoptionboxchild" id="searchrefresh" style="display:{if $prefs.feature_search_fulltext eq 'y'}block{else}none{/if};">
<div class="adminoptionbox">
	<div>{tr}When the Database search is enabled, you must manually refresh the Tiki search indexes{/tr}:</div>
	<div>
{if $refresh_index_all_now neq 'y'}
	<br /><a href="tiki-admin.php?page=search&amp;refresh_index_all_now=y" class="button" title="{tr}Refresh all search index now{/tr}">{tr}Refresh all search index now{/tr}</a>
{/if}
{if $refresh_index_now neq 'y'}
	<br /><a href="tiki-admin.php?page=search&amp;refresh_index_now=y" class="button" title="{tr}Refresh wiki search index now{/tr}">{tr}Refresh wiki search index now{/tr}</a>
{/if}
{if $refresh_tracker_index_now neq 'y' and $prefs.trk_with_mirror_tables neq 'y'}
	<br /><a href="tiki-admin.php?page=search&amp;refresh_tracker_index_now=y" class="button" title="{tr}Refresh trackers search index now{/tr}">{tr}Refresh tracker search index now{/tr}</a>
{/if}
{if $refresh_files_index_now neq 'y' and $prefs.trk_with_mirror_tables neq 'y'}
	<br /><a href="tiki-admin.php?page=search&amp;refresh_files_index_now=y" class="button" title="{tr}Refresh files search index now{/tr}">{tr}Refresh files search index now{/tr}</a>
{/if}
	</div>
</div>	
</div>
</fieldset>


<fieldset><legend>{tr}Features{/tr}</legend>
<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" id="feature_referer_highlight" name="feature_referer_highlight"
              {if $prefs.feature_referer_highlight eq 'y'}checked="checked" {/if}/></div>
	<div class="adminoptionlabel"><label for="feature_referer_highlight">{tr}Referer Search Highlighting{/tr}</label>{if $prefs.feature_help eq 'y'} {help url="Referer+Search+Highlighting"}{/if}</div>
</div>
<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" id="search_parsed_snippet" name="search_parsed_snippet"
              {if $prefs.search_parsed_snippet eq 'y'}checked="checked"{/if}/></div>
	<div class="adminoptionlabel"><label for="search_parsed_snippet">{tr}Parse the results{/tr}</label>
	<br /><em>{tr}May impact performance{/tr}.</em></div>
</div>
<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" id="feature_search_stats" name="feature_search_stats" {if $prefs.feature_search_stats eq 'y'}checked="checked" {/if}/></div>
	<div class="adminoptionlabel"><label for="feature_search_stats">{tr}Search stats{/tr}</label>{if $prefs.feature_help eq 'y'} {help url="Search+Stats"}{/if}</div>
</div>
</fieldset>	 

<fieldset><legend>{tr}Permissions{/tr}</legend>
<div class="adminoptionbox">
	<div>{icon _id=information} {tr}Enabling these options will improve performance, but may show forbidden results{/tr}.</div>
</div>
<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" id="feature_search_show_forbidden_obj" name="feature_search_show_forbidden_obj"
                {if $prefs.feature_search_show_forbidden_obj eq 'y'}checked="checked"{/if}/></div>
	<div class="adminoptionlabel"><label for="feature_search_show_forbidden_obj">{tr}Ignore individual object permissions{/tr}.</label></div>
</div>
<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" id="feature_search_show_forbidden_cat" name="feature_search_show_forbidden_cat" {if $prefs.feature_search_show_forbidden_cat eq 'y'}checked="checked"{/if}/></div>
	<div class="adminoptionlabel"><label for="feature_search_show_forbidden_cat">{tr}Ignore category viewing restrictions{/tr}.</label></div>
</div>
</fieldset> 
	  
     {if $prefs.feature_tabs neq 'y'}</div>{/if}
    </fieldset>

    <fieldset{if $prefs.feature_tabs eq 'y'} class="tabcontent" id="content{cycle name=content assign=focustab}{$focustab}"{/if}>
      {if $prefs.feature_tabs neq 'y'}
        <legend class="heading">
          <a href="#content{cycle name=content assign=focus}{$focus}" onclick="flip('content{$focus}'); return false;">
            <span>{tr}Search Results{/tr}</span>
          </a>
        </legend>
        <div id="content{$focus}" style="display:{if !isset($smarty.session.tiki_cookie_jar.show_content.$focus) and $smarty.session.tiki_cookie_jar.show_content.$focus neq 'y'}none{else}block{/if};">
      {/if}
	  
<div class="adminoptionbox">
	<div>{tr}Select the items to display on the search results page{/tr}: </div>
</div>
<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" id="feature_search_show_object_filter" name="feature_search_show_object_filter"
              {if $prefs.feature_search_show_object_filter eq 'y'}checked="checked"{/if}/></div>
	<div class="adminoptionlabel"><label for="feature_search_show_object_filter">{tr}Object filter{/tr}</label></div>
</div>
<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" id="feature_search_show_search_box"name="feature_search_show_search_box"
              {if $prefs.feature_search_show_search_box eq 'y'}checked="checked"{/if}/></div>
	<div class="adminoptionlabel"><label for="feature_search_show_search_box">{tr}Search box{/tr}</label></div>
</div>
<div class="adminoptionbox">
	<div>{tr}Select the information to display for each result{/tr}: </div>
</div>
<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" id="feature_search_show_visit_count" name="feature_search_show_visit_count"
              {if $prefs.feature_search_show_visit_count eq 'y'}checked="checked"{/if}/></div>
	<div class="adminoptionlabel"><label for="feature_search_show_visit_count">{tr}Visits{/tr} ({tr}hits{/tr})</label></div>
</div>	  
<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" id="feature_search_show_pertinence" name="feature_search_show_pertinence"
              {if $prefs.feature_search_show_pertinence eq 'y'}checked="checked"{/if}/></div>
	<div class="adminoptionlabel"><label for="feature_search_show_pertinence">{tr}Pertinence{/tr}</label></div>
</div>	  
<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" id="feature_search_show_object_type" name="feature_search_show_object_type"
              {if $prefs.feature_search_show_object_type eq 'y'}checked="checked"{/if}/></div>
	<div class="adminoptionlabel"><label for="feature_search_show_object_type">{tr}Object type{/tr}</label></div>
</div>	  
<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" id="feature_search_show_last_modification" name="feature_search_show_last_modification"
              {if $prefs.feature_search_show_last_modification eq 'y'}checked="checked"{/if}/></div>
	<div class="adminoptionlabel"><label for="feature_search_show_last_modification">{tr}Last modified date{/tr}</label></div>
</div>	  
  
     {if $prefs.feature_tabs neq 'y'}</div>{/if}
    </fieldset>

	
<div style="padding:1em;" align="center"><input type="submit" value="{tr}Change preferences{/tr}" /></div>
</td></tr></table>
</div>
</form>