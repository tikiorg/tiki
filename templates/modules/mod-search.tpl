{* $Id: mod-search_box.tpl 24586 2010-01-21 02:16:26Z chealer $ *}
{*strip TODO*}
{if $tiki_p_search eq 'y'}
{tikimodule error=$module_error title=$tpl_module_title name="search" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox notitle=$module_params.notitle}
{if $module_params.tiki_search neq 'none'}
    <form id="search-module-form{$search_mod_usage_counter}" method="get" action="#"{if $module_params.use_autocomplete eq 'y'} onsubmit="return submitSearch{$search_mod_usage_counter}()"{/if}>
	    <input id="search_mod_input_{$search_mod_usage_counter}" name="find"{if $module_params.use_autocomplete eq 'y'} class="pagename"{/if} size="{$input_size}" type="text" accesskey="s" value="{$module_params.input_value}" /> 
		
	 	{if $module_params.show_object_filter eq 'y'}
			{tr}in:{/tr}
		    <select name="where" style="width:{$module_params.select_size}em;">
			    <option value="pages">{tr}Entire Site{/tr}</option>
			    {if $prefs.feature_wiki eq 'y'}<option value="wikis"{if $module_params.where eq "wikis"} selected="selected"{/if}>{tr}Wiki Pages{/tr}</option>{/if}
			    {if $prefs.feature_directory eq 'y'}<option value="directory"{if $module_params.where eq "directory"} selected="selected"{/if}>{tr}Directory{/tr}</option>{/if}
			    {if $prefs.feature_galleries eq 'y'}
			    	<option value="galleries"{if $module_params.where eq "galleries"} selected="selected"{/if}>{tr}Image Gals{/tr}</option>
			    	<option value="images"{if $module_params.where eq "images"} selected="selected"{/if}>{tr}Images{/tr}</option>
			    {/if}
			    {if $prefs.feature_file_galleries eq 'y'}<option value="files"{if $module_params.where eq "files"} selected="selected"{/if}>{tr}Files{/tr}</option>{/if}
			    {if $prefs.feature_articles eq 'y'}<option value="articles"{if $module_params.where eq "articles"} selected="selected"{/if}>{tr}Articles{/tr}</option>{/if}
			    {if $prefs.feature_forums eq 'y'}<option value="forums"{if $module_params.where eq "forums"} selected="selected"{/if}>{tr}Forums{/tr}</option>{/if}
			    {if $prefs.feature_blogs eq 'y'}
			    	<option value="blogs"{if $module_params.where eq "blogs"} selected="selected"{/if}>{tr}Blogs{/tr}</option>
			    	<option value="posts"{if $module_params.where eq "posts"} selected="selected"{/if}>{tr}Blog Posts{/tr}</option>
			    {/if}
			    {if $prefs.feature_faqs eq 'y'}<option value="faqs"{if $module_params.where eq "faqs"} selected="selected"{/if}>{tr}FAQs{/tr}</option>{/if}
			    {if $prefs.feature_trackers eq 'y'}<option value="trackers"{if $module_params.where eq "trackers"} selected="selected"{/if}>{tr}Trackers{/tr}</option>{/if}
		    </select>
		{elseif !empty($prefs.search_default_where)}
			<input type="hidden" name="where" value="{$prefs.search_default_where|escape}" />
	    {/if}
	    
		{if $module_params.tiki_search neq 'y'}
			{if $module_params.advanced_search_option eq 'y'}
				<label for="boolean">{tr}Advanced:{/tr}<input type="checkbox" name="boolean" id="boolean"{if $module_params.advanced_search eq "y"} checked="checked"{/if} /></label>
			{else}
				{if $module_params.advanced_search eq "y"}<input type="hidden" name="boolean" value="on" />{/if}
			{/if}
			<input type="hidden" name="boolean_last" value="{$module_params.advanced_search}" />
			{if $module_params.advanced_search_help eq 'y'}
				{capture name=advanced_search_help}
					{include file='advanced_search_help.tpl'}
				{/capture}
				{add_help show='y' title="{tr}Advanced Search Help{/tr}" id="advanced_search_help"}
					{$smarty.capture.advanced_search_help}
				{/add_help}
			{/if}
		{/if}
		{var_dump var="module_params"}
	    {if $module_params.show_search_button eq 'y'}
	    	<input type = "submit" class = "wikiaction{if $module_params.default_button eq 'search'} button_default{/if}"
	    			name = "search" value = "{tr}{$module_params.search_submit}{/tr}"
	    			onclick = "$jq('#search-module-form{$search_mod_usage_counter}').attr('action', '{$module_params.search_action}');" />
	    {/if}
	    {if $module_params.show_go_button eq 'y'}
	    	<input type = "submit" class = "wikiaction{if $module_params.default_button eq 'go'} button_default{/if}"
	    			name = "go" value = "{tr}{$module_params.go_submit}{/tr}"
	    			onclick = "$jq('#search-module-form{$search_mod_usage_counter}').attr('action', '{$module_params.go_action}');" />
	    {/if}
	    {if $module_params.show_edit_button eq 'y'}
	    	<input type = "submit" class = "wikiaction{if $module_params.default_button eq 'edit'} button_default{/if}"
	    			name = "edit" value = "{tr}{$module_params.edit_submit}{/tr}"
	    			onclick = "$jq('#search-module-form{$search_mod_usage_counter} input[name!=find]').attr('name', ''); $jq('#search-module-form{$search_mod_usage_counter} input[name=find]').attr('name', 'page'); $jq('#search-module-form{$search_mod_usage_counter}').attr('action', '{$module_params.edit_action}');" />
	    {/if}
	    
    </form>
    	{jq notonready=true}
function submitSearch{{$search_mod_usage_counter}}() {
	var $f = $jq('#search-module-form{{$search_mod_usage_counter}}');
	if ($f.attr('action') == "#") {
		$f.attr('action', "{{$module_params.default_action}}");
	}
	return true;
}
    	{/jq}
	{if $module_params.use_autocomplete eq 'y'}{jq}$jq(".pagename").tiki("autocomplete", "pagename");{/jq}{/if}
{/if}
{/tikimodule}
{/if}
{*/strip*}