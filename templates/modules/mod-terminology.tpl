{tikimodule title=$tpl_module_title name="terminology flip=$module_params.flip decorations=$module_params.decorations}
  <div align="left">
    {if $terminology_profile_was_installed == 'n'}
       <b>
       {tr}WARNING{/tr}:{tr}The Terminology module can only work if the following profile has been installed:{/tr}
       <b>Collaborative_Multilingual_Terminology</b>.
       <p>
       {tr}That profile has not been installed yet, so you need to install it first.{/tr} {tr}To install this profile, click on the following link:{/tr}
       <a href="tiki-admin.php?profile=Collaborative_Multilingual_Terminology&category=&repository=&page=profiles&list=List#profile-results">Install Collaborative_Multilingual_Terminology</a>. 
       {tr}Then click on the name of that profile, wait a few seconds, and click on Install.{/tr}
       {tr}Note that you may need admin privileges.{/tr}       
       </b>
       <p>
    {/if}
    <b>{tr}Find term{/tr}:</b><br>
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
 
        <input type="submit" class="wikiaction" name="search" value="{tr}Go{/tr}"/>

      </form>
      <!--[if IE]><br class="clear" style="height: 1px !important" /><![endif]-->
	</div>
	<small>{tr}If not found, you will be given a chance to create it.{/tr}</small>
	<div class="box-footer">
	</div>
	
	<p>
    
    <a href="tiki-index.php?page=Guide de l'utilisateur-- profil de terminologie collaborative">{tr}Help{/tr}</a>
    &nbsp; &nbsp; <a href="tiki-index.php?page=Admin Guide-- Collaborative Terminology Profile">{tr}Admin{/tr}</a>
    
    
     
  </div>
{/tikimodule}
