{tikimodule title="{tr}Terminology{/tr}" name="terminology flip=$module_params.flip decorations=$module_params.decorations}
  <div align="left">
    {if $terminology_profile_was_installed == 'n'}
       <b>
       {tr}WARNING{/tr}:{tr}This module can only work if the following profile has been installed:{/tr}
       <br>
       <a href="http://profiles.tikiwiki.org/Collaborative_Multilingual_Terminology">Collaborative_Multilingual_Terminology profile.</a>
       <p>
       {tr}That profile has not been installed yet, so you need to install it first.{/tr} {tr}For help on how to install profiles, see:{/tr}
       <a href="http://profiles.tikiwiki.org/How+to+use+profiles">How to use profiles</a>.
       </b>
       <p>
    {/if}
    {tr}<b>Find term</b>{/tr}:<br>
	<div id="mod-search_wiki_pager1" style="display: block" class="clearfix box-data">
      <form class="forms" method="post" action="tiki-listpages.php">
        <input name="find" size="14" type="text" accesskey="s" value=""/>
        <input type="hidden" name="exact_match" value="On"/>
        <input type="hidden" name="hits_link_to_all_languages" value="On"/>
        <input type="hidden" name="create_new_pages_using_template_name" value="{$create_new_pages_using_template_name}"/>
        <label class="findlang">
          <select name="lang" class="in">
            {section name=ix loop=$user_languages}
			<option value="{$user_languages[ix].value}"{if $user_languages[ix].value eq $search_terms_in_lang} selected="selected"{/if}>{tr}{$user_languages[ix].name}{/tr}</option>
		    {/section} 	      
          </select>
        </label>
                       
        <input type="submit" class="wikiaction" name="search" value="Go"/> 

      </form>
      <!--[if IE]><br class="clear" style="height: 1px !important" /><![endif]-->
	</div>
	<small>{tr}If not found, you will be given a chance to create it.{/tr}</small>
	<div class="box-footer">
	</div>
	
	<p>
    
    <a href="tiki-index.php?page=Get Started with Multilingual Terminology">{tr}Help{/tr}</a>
    &nbsp; &nbsp; <a href="tiki-index.php?page=Collaborative Terminology admin page">{tr}Admin{/tr}</a>
    
    
     
  </div>
{/tikimodule}