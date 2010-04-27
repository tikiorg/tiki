{tikimodule title=$tpl_module_title name="terminology flip=$module_params.flip decorations=$module_params.decorations}
  <div align="left">
    <b>{tr}Find term:{/tr}</b><br>
	<div id="mod-search_wiki_pager1" style="display: block" class="clearfix box-data">
      <form class="forms" method="post" action="tiki-listpages.php">
      	{if $term_root_category_id != ''}
      		<input type="hidden" name="categId" value="{$term_root_category_id}"/>
      		<input type="hidden" name="create_page_with_search_category" value="y"/>
      	{/if}
        <input name="find" size="14" type="text" accesskey="s" value=""/>
        <input type="hidden" name="exact_match" value="On"/>
        <input type="hidden" name="hits_link_to_all_languages" value="On"/>
        <input type="hidden" name="create_new_pages_using_template_name" value="{$create_new_pages_using_template_name}"/>
        <input type="hidden" name="term_srch" value="y"/>
        <label class="findlang">
          <select name="lang" class="in">
            <option value=''{if $search_terms_in_lang eq ''} selected="selected"{/if}>{tr}any language{/tr}</option>
            {section name=ix loop=$user_languages}
			<option value="{$user_languages[ix].value}"{if $user_languages[ix].value eq $search_terms_in_lang} selected="selected"{/if}>{tr}{$user_languages[ix].name}{/tr}</option>
		    {/section}
          </select>
        </label>
 
        <input type="submit" class="wikiaction" name="search" value="{tr}Go{/tr}"/>

      </form>
      <!--[if IE]><br class="clear" style="height: 1px !important" /><![endif]-->
	</div>
	<small>{tr}If not found, you will be given a chance to create it.{/tr}</small>
	<div class="box-footer">
	</div>

    <a href="tiki-index.php?page=User Guide-- Collaborative Terminology profile">{tr}Help{/tr}</a>
    &nbsp; &nbsp; <a href="tiki-index.php?page=Admin Guide-- Collaborative Terminology Profile">{tr}Admin{/tr}</a>
  </div>
{/tikimodule}
