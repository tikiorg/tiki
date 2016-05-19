{* $Id$ *}
{compact}
	{if $tiki_p_search eq 'y'}
		{tikimodule error=$module_error title=$smod_params.title name="search" flip=$smod_params.flip decorations=$smod_params.decorations nobox=$smod_params.nobox notitle=$smod_params.notitle}
			{if $smod_params.tiki_search neq 'none'}
				<form class="form-inline" id="search-module-form{$search_mod_usage_counter}" method="get" action="{$smod_params.search_action}"{if $smod_params.use_autocomplete eq 'y'} onsubmit="return submitSearch{$search_mod_usage_counter}()"{/if}>
<div style="position: relative;">
					<div class="{if $smod_params.compact eq "y"}btn-group{else}form-group{/if}" style="margin-left:-7px;margin-right:-7px;">
						<input style="float:left;{if $smod_params.compact eq "y"}width:72%;border-bottom-right-radius:0;border-top-right-radius: 0;{/if}" placeholder="{tr}Find{/tr}" class="form-control" id="search_mod_input_{$search_mod_usage_counter}" name="{if $smod_params.search_action eq 'tiki-searchindex.php'}filter~content{else}find{/if}" {if !empty($smod_params.input_size)}size="{$smod_params.input_size}" style="width: auto"{/if} type="text" accesskey="s" value="{$smod_params.input_value|escape}" />&nbsp;
					{*	</div> *}
						{if $smod_params.show_object_filter eq 'y'}
							<label class="control-label" for="filterType">
								{tr}in{/tr}&nbsp;
							</label>
							<div class="form-group">
									{if $smod_params.search_action eq 'tiki-searchindex.php'}
										<select id="filterType" name="filter~type" class="form-control" {*style="width:{$smod_params.select_size}em;"*}>
											<option value="">{tr}Entire Site{/tr}</option>
											{if $prefs.feature_wiki eq 'y'}<option value="wiki page"{if $smod_params.where eq "wiki page"} selected="selected"{/if}>{tr}Wiki Pages{/tr}</option>{/if}
											{if $prefs.feature_blogs eq 'y'}<option value="blog post"{if $smod_params.where eq "blog post"} selected="selected"{/if}>{tr}Blog Posts{/tr}</option>{/if}
											{if $prefs.feature_articles eq 'y'}<option value="article"{if $smod_params.where eq "article"} selected="selected"{/if}>{tr}Articles{/tr}</option>{/if}
											{if $prefs.feature_file_galleries eq 'y'}<option value="file"{if $smod_params.where eq "file"} selected="selected"{/if}>{tr}Files{/tr}</option>{/if}
											{if $prefs.feature_forums eq 'y'}<option value="forum post"{if $smod_params.where eq "forum post"} selected="selected"{/if}>{tr}Forums{/tr}</option>{/if}
											{if $prefs.feature_trackers eq 'y'}<option value="trackeritem"{if $smod_params.where eq "trackeritem"} selected="selected"{/if}>{tr}Trackers{/tr}</option>{/if}
											{if $prefs.feature_sheet eq 'y'}<option value="sheet"{if $smod_params.where eq "sheet"} selected="selected"{/if}>{tr}Spreadsheets{/tr}</option>{/if}
										</select>
									{else}
										<select class="form-control" name="where" {*style="width:{$smod_params.select_size}em;"*}>
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
									{/if}

							</div>
						{elseif !empty($prefs.search_default_where)}
							{if is_array($prefs.search_default_where)}
								{foreach from=$prefs.search_default_where item=t}
									<input type="hidden" name="{if $smod_params.search_action eq 'tiki-searchindex.php'}filter~type[]{else}where[]{/if}" value="{$t|escape}" />
								{/foreach}
							{else}
								<input type="hidden" name="{if $smod_params.search_action eq 'tiki-searchindex.php'}filter~type{else}where{/if}" value="{$prefs.search_default_where|escape}" />
							{/if}
						{/if}

							{if $smod_params.compact eq "y"}

							<button type="submit" class="btn btn-default search_mod_magnifier">
								{icon name="search"}
							</button>
					</div>

							<div class="btn-group search_mod_buttons box" style="display:none;position:absolute;left:2.5em;top:2.5em;z-index:2;white-space:nowrap;">
								{else}
								&nbsp;<div class="btn-group">
									{/if}
									{foreach $smod_params.additional_filters as $key => $filter}
										<input type="hidden" name="filter~{$key|escape}" value="{$filter|escape}"/>
									{/foreach}
									{if $smod_params.show_search_button eq 'y'}
										<input type = "submit" class="btn btn-default btn-sm tips{if $smod_params.default_button eq 'search'} button_default{/if}{if $smod_params.compact eq "y"} bottom{else} btn-xs{/if}"
											   name = "search" value = "{$smod_params.search_submit|escape}"
											   title="{tr}Search{/tr}|{tr}Search for text throughout the site.{/tr}"
											   {if $smod_params.compact eq "y"}data-placement="bottom"{/if}
											   onclick = "$('#search-module-form{$search_mod_usage_counter}').attr('action', '{$smod_params.search_action|escape:javascript}').attr('page_selected','');"
										/>
									{/if}
									{if $smod_params.show_go_button eq 'y'}
										<input type = "submit" class="btn btn-default btn-sm tips{if $smod_params.compact eq "y"} bottom{else} btn-xs{/if}{if $smod_params.default_button eq 'go'} button_default{/if}"
											   name = "go" value = "{$smod_params.go_submit|escape}"
											   title="{tr}Search{/tr}|{tr}Go directly to a page, or search in page titles if exact match is not found.{/tr}"
											   {if $smod_params.compact eq "y"}data-placement="bottom"{/if}
											   onclick = "$('#search-module-form{$search_mod_usage_counter}').attr('action', '{$smod_params.go_action|escape:javascript}').attr('page_selected','');
											   {if $smod_params.search_action eq 'tiki-searchindex.php'}
													   $('#search-module-form{$search_mod_usage_counter} input[name=\'filter~content\']').attr('name', 'find');
											   {/if}"
										/>
										<input type="hidden" name="exact_match" value="" />
									{/if}
									{if $smod_params.show_edit_button eq 'y' and $tiki_p_edit eq 'y'}
										<input type = "submit" class = "btn btn-default btn-sm tips{if $smod_params.compact eq "y"} bottom{else} btn-xs{/if}{if $smod_params.default_button eq 'edit'} button_default{/if}"
											   name = "edit" value = "{$smod_params.edit_submit|escape}"
											   title="{tr}Search{/tr}|{tr}Edit existing page or create a new one.{/tr}"
											   {if $smod_params.compact eq "y"}data-placement="bottom"{/if}
											   onclick = "$('#search-module-form{$search_mod_usage_counter} input[name!={if $smod_params.search_action eq 'tiki-searchindex.php'}\'filter~content\'{else}\'find\'{/if}]').attr('name', '');
													   $('#search-module-form{$search_mod_usage_counter} input[name={if $smod_params.search_action eq 'tiki-searchindex.php'}\'filter~content\'{else}\'find\'{/if}]').attr('name', 'page');
													   $('#search-module-form{$search_mod_usage_counter}').attr('action', '{$smod_params.edit_action|escape:javascript}').attr('page_selected','');"
										/>
									{/if}
								</div></div>


						{if $smod_params.tiki_search neq 'y'}
							{if $smod_params.advanced_search_option eq 'y'}
								<div class="checkbox" style="padding-left: 6px; padding-right: 6px;">
									<label for="boolean"><input type="checkbox" name="boolean" id="boolean"{if $smod_params.advanced_search eq "y"} checked="checked"{/if} /> {tr}Advanced{/tr}
										{if $smod_params.advanced_search_help eq 'y'}
											<a href="{bootstrap_modal controller=search action=help}">{icon name='help'} {tr}Search Help{/tr}</a>
										{/if}

									</label>
								</div>
							{else}
								{if $smod_params.advanced_search eq "y"}<input type="hidden" name="boolean" value="on" />{/if}
							{/if}
							<input type="hidden" name="boolean_last" value="{$smod_params.advanced_search}" />

						{/if}




						{if $smod_params.compact eq "y"}
							{jq}
$(".search_mod_magnifier").mouseover( function () {
	$(".search_mod_buttons", $(this).parent().parent())
	.show('fast')
	.mouseleave( function () {
		$(this).hide('fast');
	});
}).click( function () {
	$(this).parents("form").submit();
});
$("#search_mod_input_{{$search_mod_usage_counter}}")
.keydown( function () {
	$(".search_mod_magnifier", $(this).parent()).mouseover();}
);
							{/jq}
						{else}
					</div>
						{/if}
				</form>
				{jq notonready=true}
					function submitSearch{{$search_mod_usage_counter}}() {
						var $f = $('#search-module-form{{$search_mod_usage_counter}}');
						if ($f.attr('action') !== "tiki-editpage.php" && $f.data('page_selected') === $("#search_mod_input_{{$search_mod_usage_counter}}").val()) {
							if ($f.find('input[name="find"]').length) {
								$f.find('input[name="find"]').val($f.data('page_selected'));
							} else {
								$f.append($('<input name="find">').val($f.data('page_selected')));
							}
							$f.attr('action', '{{$smod_params.go_action|escape:javascript}}');
						} else if ($f.attr('action') == "#") {
							$f.attr('action', '{{$smod_params.search_action|escape:javascript}}');
						}
						$exact = $f.find("input[name=exact_match]");
						if ($exact.val() != "y") {
							$exact.remove(); // seems exact_match is true even if empty
						}
						return true;
					}
				{/jq}
				{if $smod_params.use_autocomplete eq 'y'}
					{capture name="selectFn"}select: function(event, item) {ldelim}
						$('#search-module-form{$search_mod_usage_counter}').data('page_selected', item.item.value).find("input[name=exact_match]").val("y");
						{rdelim}, open: function(event, item) {ldelim}
						$(".search_mod_buttons", "#search-module-form{$search_mod_usage_counter}").hide();
						{rdelim}, close: function(event, item) {ldelim}
						$(".search_mod_buttons", "#search-module-form{$search_mod_usage_counter}").show();
						{rdelim}{/capture}
					{autocomplete element="#search_mod_input_"|cat:$search_mod_usage_counter type="pagename" options=$smarty.capture.selectFn}
				{/if}
			{/if}
		{/tikimodule}
	{/if}
{/compact}
