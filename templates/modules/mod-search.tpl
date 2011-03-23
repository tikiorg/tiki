{compact}
{if $tiki_p_search eq 'y'}
{tikimodule error=$module_error title=$tpl_module_title name="search" flip=$smod_params.flip decorations=$smod_params.decorations nobox=$smod_params.nobox notitle=$smod_params.notitle}
{if $smod_params.tiki_search neq 'none'}
    <form id="search-module-form{$search_mod_usage_counter}" method="get" action="#"{if $smod_params.use_autocomplete eq 'y'} onsubmit="return submitSearch{$search_mod_usage_counter}()"{/if}>
    	<div>
		    <input id="search_mod_input_{$search_mod_usage_counter}" name="find"{if !empty($smod_params.input_size)} style="width:{$smod_params.input_size}em"{/if} type="text" accesskey="s" value="{$smod_params.input_value}" /> 
			
		 	{if $smod_params.show_object_filter eq 'y'}
				{tr}in:{/tr}
			    <select name="where" style="width:{$smod_params.select_size}em;">
				    <option value="pages">{tr}Entire Site{/tr}</option>
				    {if $prefs.feature_wiki eq 'y'}<option value="wikis"{if $smod_params.where eq "wikis"} selected="selected"{/if}>{tr}Wiki Pages{/tr}</option>{/if}
				    {if $prefs.feature_directory eq 'y'}<option value="directory"{if $smod_params.where eq "directory"} selected="selected"{/if}>{tr}Directory{/tr}</option>{/if}
				    {if $prefs.feature_galleries eq 'y'}
				    	<option value="galleries"{if $smod_params.where eq "galleries"} selected="selected"{/if}>{tr}Image Gals{/tr}</option>
				    	<option value="images"{if $smod_params.where eq "images"} selected="selected"{/if}>{tr}Images{/tr}</option>
				    {/if}
				    {if $prefs.feature_file_galleries eq 'y'}<option value="files"{if $smod_params.where eq "files"} selected="selected"{/if}>{tr}Files{/tr}</option>{/if}
				    {if $prefs.feature_articles eq 'y'}<option value="articles"{if $smod_params.where eq "articles"} selected="selected"{/if}>{tr}Articles{/tr}</option>{/if}
				    {if $prefs.feature_forums eq 'y'}<option value="forums"{if $smod_params.where eq "forums"} selected="selected"{/if}>{tr}Forums{/tr}</option>{/if}
				    {if $prefs.feature_blogs eq 'y'}
				    	<option value="blogs"{if $smod_params.where eq "blogs"} selected="selected"{/if}>{tr}Blogs{/tr}</option>
				    	<option value="posts"{if $smod_params.where eq "posts"} selected="selected"{/if}>{tr}Blog Posts{/tr}</option>
				    {/if}
				    {if $prefs.feature_faqs eq 'y'}<option value="faqs"{if $smod_params.where eq "faqs"} selected="selected"{/if}>{tr}FAQs{/tr}</option>{/if}
				    {if $prefs.feature_trackers eq 'y'}<option value="trackers"{if $smod_params.where eq "trackers"} selected="selected"{/if}>{tr}Trackers{/tr}</option>{/if}
			    </select>
			{elseif !empty($prefs.search_default_where)}
				<input type="hidden" name="where" value="{$prefs.search_default_where|escape}" />
		    {/if}
		    
			{if $smod_params.tiki_search neq 'y'}
				{if $smod_params.advanced_search_option eq 'y'}
					<label for="boolean">{tr}Advanced:{/tr}<input type="checkbox" name="boolean" id="boolean"{if $smod_params.advanced_search eq "y"} checked="checked"{/if} /></label>
				{else}
					{if $smod_params.advanced_search eq "y"}<input type="hidden" name="boolean" value="on" />{/if}
				{/if}
				<input type="hidden" name="boolean_last" value="{$smod_params.advanced_search}" />
				{if $smod_params.advanced_search_help eq 'y'}
					{capture name=advanced_search_help}
						{include file='advanced_search_help.tpl'}
					{/capture}
					{add_help show='y' title="{tr}Search Help{/tr}" id="advanced_search_help"}
						{$smarty.capture.advanced_search_help}
					{/add_help}
				{/if}
			{/if}
		    {if $smod_params.show_search_button eq 'y'}
		    	<input type = "submit" class = "wikiaction tips{if $smod_params.default_button eq 'search'} button_default{/if}"
		    			name = "search" value = "{$smod_params.search_submit}"
		    			title="{tr}Search{/tr}|{tr}Search for text throughout the site.{/tr}"
		    			onclick = "$('#search-module-form{$search_mod_usage_counter}').attr('action', '{$smod_params.search_action}').attr('page_selected','');" />
		    {/if}
		    {if $smod_params.show_go_button eq 'y'}
		    	<input type="hidden" name="exact_match" />
		    	<input type = "submit" class = "wikiaction tips{if $smod_params.default_button eq 'go'} button_default{/if}"
		    			name = "go" value = "{$smod_params.go_submit}"
		    			title="{tr}Search{/tr}|{tr}Go directly to a page, or search in page titles if exact match is not found.{/tr}"
		    			onclick = "$('#search-module-form{$search_mod_usage_counter}').attr('action', '{$smod_params.go_action}').attr('page_selected','');" />
		    {/if}
		    {if $smod_params.show_edit_button eq 'y' and $tiki_p_edit eq 'y'}
		    	<input type = "submit" class = "wikiaction tips{if $smod_params.default_button eq 'edit'} button_default{/if}"
		    			name = "edit" value = "{$smod_params.edit_submit}"
		    			title="{tr}Search{/tr}|{tr}Edit existing page or create a new one.{/tr}"
		    			onclick = "$('#search-module-form{$search_mod_usage_counter} input[name!=find]').attr('name', ''); $('#search-module-form{$search_mod_usage_counter} input[name=find]').attr('name', 'page'); $('#search-module-form{$search_mod_usage_counter}').attr('action', '{$smod_params.edit_action}').attr('page_selected','');" />
		    {/if}
	    </div>
    </form>
    {jq notonready=true}
function submitSearch{{$search_mod_usage_counter}}() {
	var $f = $('#search-module-form{{$search_mod_usage_counter}}');
	if ($f.attr('page_selected') === $("#search_mod_input_{{$search_mod_usage_counter}}").val()) {
		$f.attr('action', '{{$smod_params.go_action}}');
	} else if ($f.attr('action') == "#") {
		$f.attr('action', '{{$smod_params.search_action}}');
	}
	return true;
}
    {/jq}
	{if $smod_params.use_autocomplete eq 'y'}
		{capture name="selectFn"}select: function(event, item) {ldelim}
	$('#search-module-form{$search_mod_usage_counter}').attr('page_selected', item.item.value);
{rdelim}{/capture}
		{autocomplete element="#search_mod_input_"|cat:$search_mod_usage_counter type="pagename" options=$smarty.capture.selectFn}
	{/if}
{/if}
{/tikimodule}
{/if}
{/compact}
