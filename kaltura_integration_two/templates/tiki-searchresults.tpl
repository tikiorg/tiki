{* $Id$ *}
{popup_init src="lib/overlib.js"}

{if !( $searchStyle eq "menu" )}
	{title admpage="search" help="Search+User"}{tr}Search{/tr}{/title}
{/if}

{capture name=advanced_search_help}
	<ul>
		<li>+ : {tr}A leading plus sign indicates that this word must be present in every object returned.{/tr}</li>
		<li>- : {tr}A leading minus sign indicates that this word must not be present in any row returned.{/tr}</li>
		<li>{tr}By default (when neither plus nor minus is specified) the word is optional, but the object that contain it will be rated higher.{/tr}</li>
		<li>&lt; &gt; : {tr}These two operators are used to change a word's contribution to the relevance value that is assigned to a row.{/tr}</li>
		<li>( ) : {tr}Parentheses are used to group words into subexpressions.{/tr}</li>
		<li>~ : {tr}A leading tilde acts as a negation operator, causing the word's contribution to the object relevance to be negative. It's useful for marking noise words. An object that contains such a word will be rated lower than others, but will not be excluded altogether, as it would be with the - operator.{/tr}</li>
		<li>* : {tr}An asterisk is the truncation operator. Unlike the other operators, it should be appended to the word, not prepended.{/tr}</li>
		<li>&quot; : {tr}The phrase, that is enclosed in double quotes &quot;, matches only objects that contain this phrase literally, as it was typed.{/tr}</li>
	</ul>
{/capture}

<div class="nohighlight">
	{if $searchStyle neq "menu" && $prefs.feature_search_show_object_filter eq 'y'}
		<div class="navbar">
			{tr}Search in{/tr}:
			{foreach item=name key=k from=$where_list}
				{button _auto_args='where,highlight' href="tiki-searchresults.php?where=$k"  _selected="'$where'=='$k'" _selected_class="highlight" _text="$name"}
			{/foreach}
		</div>
	{/if}

	{if $prefs.feature_search_show_search_box eq 'y'}
		<form action="tiki-searchresults.php" method="get" id="search-form" class="findtable">
			<label class="findtitle">
				{tr}Find{/tr} <input name="highlight" size="14" type="text" accesskey="s" value="{$words}" />
			</label>
			{if !( $searchStyle eq "menu" )}
				<label class="searchboolean" for="boolean">
					{tr}Advanced search:{/tr}<input type="checkbox" name="boolean" id="boolean" {if $boolean eq 'y'} checked="checked"{/if} />
				</label>
				{add_help show='y' title="{tr}Help{/tr}" id="advanced_search_help"}
					{$smarty.capture.advanced_search_help}
				{/add_help}
				<label class="searchdate" for="date">
					{tr}Date Search:{/tr}
					<select id="date" name="date" onchange="javascript:submit()">
						{section name=date start=0 loop=12 step=1}	
							<option value="{$smarty.section.date.index}" {if $smarty.section.date.index eq $date}selected="selected"{/if}>
								{if $smarty.section.date.index eq 0}
									{tr}All dates{/tr}
								{else}
									{$smarty.section.date.index} {tr}Month{/tr}
								{/if}
							</option>
						{/section}
					</select>
				</label>
			{/if}

			{if $prefs.feature_search_show_object_filter eq 'y'}
				{if $searchStyle eq "menu" }
					<span class='searchMenu'>
						{tr}in{/tr}
						<select name="where">
							{if empty($where_list)} {* Required when file included outside tiki-searchindex.php. eg. error.rpl *}
								<option value="pages">{tr}Entire Site{/tr}</option>
								{if $prefs.feature_wiki eq 'y'}
									<option value="wikis">{tr}Wiki Pages{/tr}</option>
								{/if}
								{if $prefs.feature_calendar eq 'y'}
									<option value="calendars">{tr}Calendar Items{/tr}</option>
								{/if}
								{if $prefs.feature_galleries eq 'y'}
									<option value="galleries">{tr}Galleries{/tr}</option>
									<option value="images">{tr}Images{/tr}</option>
								{/if}
								{if $prefs.feature_file_galleries eq 'y'}
									<option value="files">{tr}Files{/tr}</option>
								{/if}
								{if $prefs.feature_forums eq 'y'}
									<option value="forums">{tr}Forums{/tr}</option>
								{/if}
								{if $prefs.feature_faqs eq 'y'}
									<option value="faqs">{tr}Faqs{/tr}</option>
								{/if}
								{if $prefs.feature_blogs eq 'y'}
									<option value="blogs">{tr}Blogs{/tr}</option>
									<option value="posts">{tr}Blog Posts{/tr}</option>
								{/if}
								{if $prefs.feature_directory eq 'y'}
									<option value="directory">{tr}Directory{/tr}</option>
								{/if}
								{if $prefs.feature_articles eq 'y'}
									<option value="articles">{tr}Articles{/tr}</option>
								{/if}
								{if $prefs.feature_trackers eq 'y'}
									<option value="trackers">{tr}Trackers{/tr}</option>
								{/if}
							{else}
								{foreach item=name key=k from=$where_list}
									<option value="{$k}">{$name}</option>
								{/foreach}
							{/if}
						</select>
					</span>
				{else}
					<input type="hidden" name="where" value="{$where|escape}" />
					{if $forumId}<input type="hidden" name="forumId" value="{$forumId}" />{/if}
				{/if}
			{/if}
			<label class="findsubmit">
				<input type="submit" name="search" value="{tr}Go{/tr}"/>
			</label>
			{if !$searchNoResults}
				{button _auto_args='highlight' href="tiki-searchindex.php?highlight=" _text="{tr}Clear Filter{/tr}"}
			{/if}
		</form>
	{/if}
</div><!--nohighlight-->
	{* do not change the comment above, since smarty 'highlight' outputfilter is hardcoded to find exactly this... instead you may experience white pages as results *}

{if $searchStyle ne 'menu' and ! $searchNoResults }
	<div class="nohighlight simplebox">
		 {tr}Found{/tr} "{$words}" {tr}in{/tr} 
			{if $where_forum}
				{tr}{$where}{/tr}: {$where_forum}
			{else}
				{$cant} {tr}{$where}{/tr}
			{/if}
	</div><!--nohighlight-->
{/if}

{if ! $searchNoResults }
	<ul class="searchresults">
		{section name=search loop=$results}
		<li>
			{if $prefs.feature_search_show_object_type eq 'y' &&  $results[search].type > ''}
				<span class="objecttype">{tr}{$results[search].type}{/tr}</span>
			{/if}
			{if !empty($results[search].parentName)}
					<a href="{$results[search].parentHref}" class="parentname">{$results[search].parentName|escape}</a>
				{/if}
			<a href="{$results[search].href}&amp;highlight={$words}" class="objectname">{$results[search].pageName|strip_tags}</a>
			{if $prefs.feature_search_show_visit_count eq 'y'}
				<span class="itemhits">({tr}Hits{/tr}: {$results[search].hits})</span>
			{/if}

			{if $prefs.feature_search_show_pertinence eq 'y' && $prefs.feature_search_fulltext eq 'y'}
				<span class="itemrelevance">
					{if $results[search].relevance <= 0}
						({tr}Simple search{/tr})
					{else}
						({tr}Relevance{/tr}: {$results[search].relevance})
					{/if}
				</span>
			{/if}

			<div class="searchdesc">{$results[search].data|strip_tags|truncate:250:'...'}</div>
			{if $prefs.feature_search_show_last_modification eq 'y'}
				<div class="searchdate">{tr}Last modification{/tr}: {$results[search].lastModif|tiki_long_datetime}</div>
			{/if}
		</li>
		{sectionelse}
			{tr}No pages matched the search criteria{/tr}
		{/section}
	</ul>
	{pagination_links cant=$cant step=$maxRecords offset=$offset}{/pagination_links}
{/if}
