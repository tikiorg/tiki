<div class="cbox">
  <div class="cbox-title">{tr}Search settings{/tr}</div>
  <div class="cbox-data">
    <div class="simplebox">
        <table class="admin">
      <form action="tiki-admin.php?page=search" method="post">
	<tr>
          <td class="heading" colspan="2">{tr}Search features{/tr}</td>
        </tr><tr>
	  <td class="form">
	{if $feature_help eq 'y'}<a href="{$helpurl}FullTextSearch" target="tikihelp" class="tikihelp" title="{tr}Full Text Search{/tr}">{/if}
		{tr}Full Text Search{/tr}
		{if $feature_help eq 'y'}</a>{/if}
	        :</td>
          <td><input type="checkbox" name="feature_search_fulltext"
                {if $feature_search_fulltext eq 'y'}checked="checked"{/if}/></td>
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
              value="{tr}Change preferences{/tr}" /></td>
        </tr>
      </form>
        </table>
    </div>
  </div>
</div>