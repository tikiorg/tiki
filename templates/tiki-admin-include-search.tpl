<div class="cbox">
  <div class="cbox-title">
    {tr}{$crumbs[$crumb]->description}{/tr}
    {help crumb=$crumbs[$crumb]}
  </div>
  <div class="cbox-data">
    <form action="tiki-admin.php?page=search" method="post">
      <table class="admin">
	<tr>
          <td class="heading" colspan="2">{tr}Search features{/tr}</td>
        </tr><tr>
	  <td class="form">
	{if $feature_help eq 'y'}<a href="{$helpurl}Full+Text+Search" target="tikihelp" class="tikihelp" title="{tr}Full Text Search{/tr}">{/if}
		{tr}Full Text Search{/tr}
		{if $feature_help eq 'y'}</a>{/if}
	        :</td>
          <td><input type="checkbox" name="feature_search_fulltext"
                {if $feature_search_fulltext eq 'y'}checked="checked"{/if}/></td>
        </tr><tr>
	<td class="form">
{if $feature_help eq 'y'}<a href="{$helpurl}Search+Stats" target="tikihelp" class="tikihelp" title="{tr}SearchStats{/tr}">{/if}
                        {tr}Search stats{/tr}
                        {if $feature_help eq 'y'}</a>{/if}
                        :</td>
        <td><input type="checkbox" name="feature_search_stats"
            {if $feature_search_stats eq 'y'}checked="checked"{/if}/>
	</td>
	</tr>
	<tr>
          <td class="form">
        {if $feature_help eq 'y'}<a href="{$helpurl}Referer+Search+Highlighting" target="tikihelp" class="tikihelp" title="{tr}Referer Search Highlighting{/tr}">{/if}
                {tr}Referer Search Highlighting{/tr}
                {if $feature_help eq 'y'}</a>{/if}
                :</td>
        <td><input type="checkbox" name="feature_referer_highlight"
                {if $feature_referer_highlight eq 'y'}checked="checked"{/if}/></td>
        </tr>
	{if $refresh_index_now neq 'y'}
	<tr>
	<td>
<a href="tiki-admin.php?page=search&refresh_index_now=y" class="link" title="{tr}Refresh wiki search index now{/tr}">
                        {tr}Refresh wiki search index now{/tr}</a></td>
    <td>&nbsp;</td>
	</tr>
	{/if}
	<tr>
          <td class="heading" colspan="2">{tr}Performance issues{/tr}</td>
        </tr><tr>
	<td class="form">
	{if $feature_help eq 'y'}<a href="{$helpurl}WYSIWYCA+Search" target="tikihelp" class="tikihelp" title="{tr}Search may show forbidden results. Much better performance though.{/tr}">{/if}
		{tr}Ignore individual object permissions{/tr}
		{if $feature_help eq 'y'}</a>{/if}
	        :</td>
          <td><input type="checkbox" name="feature_search_show_forbidden_obj"
                {if $feature_search_show_forbidden_obj eq 'y'}checked="checked"{/if}/></td>
        </tr><tr>
	<td class="form">
	{if $feature_help eq 'y'}<a href="{$helpurl}WYSIWYCA+Search" target="tikihelp" class="tikihelp" title="{tr}Search may show forbidden results. Much better performance though.{/tr}">{/if}
		{tr}Ignore category viewing restrictions{/tr}
		{if $feature_help eq 'y'}</a>{/if}
	        :</td>
          <td><input type="checkbox" name="feature_search_show_forbidden_cat"
                {if $feature_search_show_forbidden_cat eq 'y'}checked="checked"{/if}/></td>
        </tr><tr>
          <td class="heading" colspan="2">{tr}Settings for searching content{/tr}</td>
        </tr><tr>
          <td class="form"><label for="search-refresh">{tr}Search refresh rate{/tr}:</label></td>
	  <td><input size="5" type="text" name="search_refresh_rate" id="search-refresh"
	      value="{$search_refresh_rate|escape}" /></td>
        </tr><tr>
          <td class="form"><label for="search-min_length">{tr}Minimum length of search word{/tr}: </label></td>
          <td><input size="5" type="text" name="search_min_wordlength" id="search-min_length"
              value="{$search_min_wordlength|escape}" /></td>
        </tr><tr>
	  <td class="form"><label for="search-max_words">{tr}Max. number of words containing a syllable{/tr}: </label></td>
	  <td><input size="5" type="text" name="search_max_syllwords" id="search-max_words"
	  value="{$search_max_syllwords|escape}" /></td>
	</tr><tr>
          <td class="form"><label for="search-cache_age">{tr}Max. age in hours of syllable search cache{/tr}: </label></td>
          <td><input size="5" type="text" name="search_syll_age" id="search-cache_age"
          value="{$search_syll_age|escape}" /></td>
        </tr><tr>
          <td class="form"><label for="search-purge">{tr}LRU list purging rate{/tr}: </label></td>
          <td><input size="5" type="text" name="search_lru_purge_rate" id="search-purge"
          value="{$search_lru_purge_rate|escape}" /></td>
        </tr><tr>
          <td class="form"><label for="search-list_length">{tr}LRU list length{/tr}: </label></td>
          <td><input size="5" type="text" name="search_lru_length" id="search-list_length"
          value="{$search_lru_length|escape}" /></td>
        </tr><tr>
          <td colspan="2" class="button"><input type="submit" name="searchprefs"
              value="{tr}Change settings{/tr}" /></td>
        </tr>
      </table>
    </form>
  </div>
</div>

