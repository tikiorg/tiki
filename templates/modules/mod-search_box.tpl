{* $Id$ *}

{if $prefs.feature_search eq 'y' && $tiki_p_search eq 'y'}
{if !isset($tpl_module_title)}{assign var=tpl_module_title value="{tr}Search{/tr}"}{/if}
{tikimodule error=$module_params.error title=$tpl_module_title name="search_box" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox notitle=$module_params.notitle}

{capture name=advanced_search_help}
		<ul><li>+ : {tr}A leading plus sign indicates that this word must be present in every object returned.{/tr}</li>
		<li>- : {tr}A leading minus sign indicates that this word must not be present in any row returned.{/tr}</li>
    	<li>{tr}By default (when neither plus nor minus is specified) the word is optional, but the object that contain it will be rated higher.{/tr}</li>
		<li>&lt; &gt; : {tr}These two operators are used to change a word's contribution to the relevance value that is assigned to a row.{/tr}</li>
		<li>( ) : {tr}Parentheses are used to group words into subexpressions.{/tr}</li>
		<li>~ : {tr}A leading tilde acts as a negation operator, causing the word's contribution to the object relevance to be negative. It's useful for marking noise words. An object that contains such a word will be rated lower than others, but will not be excluded altogether, as it would be with the - operator.{/tr}</li>
		<li>* : {tr}An asterisk is the truncation operator. Unlike the other operators, it should be appended to the word, not prepended.{/tr}</li>
		<li>&quot; : {tr}The phrase, that is enclosed in double quotes &quot;, matches only objects that contain this phrase literally, as it was typed.{/tr}</li></ul>
{/capture}

    <form class="forms" method="get" action="tiki-searchresults.php">
    <input id="fuser" name="highlight" size="14" type="text" accesskey="s" /> 
	
 	{if $prefs.feature_search_show_object_filter eq 'y'}

	{tr}in:{/tr}<br />
    <select name="where">
    <option value="pages">{tr}Entire Site{/tr}</option>
    {if $prefs.feature_wiki eq 'y'}
    <option value="wikis">{tr}Wiki Pages{/tr}</option>
    {/if}
    {if $prefs.feature_directory eq 'y'}
    <option value="directory">{tr}Directory{/tr}</option>
    {/if}
    {if $prefs.feature_galleries eq 'y'}
    <option value="galleries">{tr}Image Gals{/tr}</option>
    <option value="images">{tr}Images{/tr}</option>
    {/if}
    {if $prefs.feature_file_galleries eq 'y'}
    <option value="files">{tr}Files{/tr}</option>
    {/if}
    {if $prefs.feature_articles eq 'y'}
    <option value="articles">{tr}Articles{/tr}</option>
    {/if}
    {if $prefs.feature_forums eq 'y'}
    <option value="forums">{tr}Forums{/tr}</option>
    {/if}
    {if $prefs.feature_blogs eq 'y'}
    <option value="blogs">{tr}Blogs{/tr}</option>
    <option value="posts">{tr}Blog Posts{/tr}</option>
    {/if}
    {if $prefs.feature_faqs eq 'y'}
    <option value="faqs">{tr}FAQs{/tr}</option>
    {/if}
    {if $prefs.feature_trackers eq 'y'}
    <option value="trackers">{tr}Trackers{/tr}</option>
    {/if}
    </select>
	
    {/if}
		<br/><label for="boolean">{tr}Advanced search:{/tr}<input type="checkbox" name="boolean"{if $boolean ne 'n'} checked="checked"{/if} /></label>
		{add_help show='y' title="{tr}Advanced Search Help{/tr}" id="advanced_search_help"}
			{$smarty.capture.advanced_search_help}
		{/add_help}<br/>		
    <input type="submit" class="wikiaction" name="search" value="{tr}Go{/tr}"/> 
    </form>
{/tikimodule}
{/if}
