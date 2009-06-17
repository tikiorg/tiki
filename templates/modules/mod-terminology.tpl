{tikimodule title="{tr}Terminology{/tr}" name="terminology flip=$module_params.flip decorations=$module_params.decorations}
  <div align="left">
    <b>WARNING: This module is still under development. DO NOT ACTIVATE IT!</b>
    <p>
    {tr}<b>Find term</b>{/tr}:<br>
	<div id="mod-search_wiki_pager1" style="display: block" class="clearfix box-data">
      <form class="forms" method="post" action="tiki-listpages.php">
        <input name="find" size="14" type="text" accesskey="s" value=""/>
        <input type="hidden" name="exact_match" value="On"/>
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
	{tr}If not found, you will be given a chance to create it.{/tr}
	<b>{tr}Do not forget to:{/tr}</b>
	<UL>
	<LI/>{tr}Choose the appropriate language.{/tr}
	<LI/>{tr}Use the Term template in that language.{/tr}
	</UL>
	<div class="box-footer">
	</div>
	
	<p>    
    
    <a href="tiki-index.php?page=Get Started with Multilingual Terminology">{tr}Terminology Help{/tr}</a>
    
     
  </div>
{/tikimodule}