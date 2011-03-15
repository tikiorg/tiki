{if $tiki_p_search eq 'y'}
{tikimodule error=$module_error title=$tpl_module_title name="search_box" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox notitle=$module_params.notitle}
{if $type neq 'none'}
    <form class="forms" id="search-module-form" method="get" action="tiki-search{if $type eq 'tiki'}index{else}results{/if}.php">
    <input id="fuser" name="highlight" size="14" type="text" accesskey="s" /> 
	{if $prefs.javascript_enabled eq 'y' and $prefs.feature_jquery_autocomplete eq 'y' and $prefs.search_autocomplete eq 'y'}
		{jq}
			$("#fuser").tiki("autocomplete", "pagename");
		{/jq}
	{/if}	
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

	{elseif !empty($prefs.search_default_where)}
		<input type="hidden" name="where" value="{$prefs.search_default_where|escape}" />
    {/if}
	{if $type eq 'fulltext'}
		<br/><label for="boolean">{tr}Advanced search:{/tr}<input type="checkbox" id="boolean" name="boolean"{if !isset($boolean) or $boolean ne 'n'} checked="checked"{/if} /></label>

		{capture name=advanced_search_help}
			{include file='advanced_search_help.tpl'}
		{/capture}
		{add_help show='y' title="{tr}Search Help{/tr}" id="advanced_search_help"}
			{$smarty.capture.advanced_search_help}
		{/add_help}<br/>
	{/if}
    <input type="submit" class="wikiaction" name="search" value="{tr}Go{/tr}"/> 
    </form>
{/if}
{/tikimodule}
{/if}
