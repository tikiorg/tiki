{* $Id$ *}

{title help="Stats"}{tr}Stats{/tr}{/title}

<div class="navbar">
	{button _anchor="site_stats" _text="{tr}Site{/tr}"}
	{if $wiki_stats}{button _anchor="wiki_stats" _text="{tr}Wiki{/tr}"}{/if}
	{if $igal_stats}{button _anchor="igal_stats" _text="{tr}Image galleries{/tr}"}{/if}
	{if $fgal_stats}{button _anchor="fgal_stats" _text="{tr}File galleries{/tr}"}{/if}
	{if $cms_stats}{button _anchor="cms_stats" _text="{tr}CMS{/tr}"}{/if}
	{if $forum_stats}{button _anchor="forum_stats" _text="{tr}Forums{/tr}"}{/if}
	{if $blog_stats}{button _anchor="blog_stats" _text="{tr}Blogs{/tr}"}{/if}
	{if $poll_stats}{button _anchor="poll_stats" _text="{tr}Polls{/tr}"}{/if}
	{if $faq_stats}{button _anchor="faq_stats" _text="{tr}FAQs{/tr}"}{/if}
	{if $user_stats}{button _anchor="user_stats" _text="{tr}User{/tr}"}{/if}
	{if $quiz_stats}{button _anchor="quiz_stats" _text="{tr}Quizzes{/tr}"}{/if}
	{if $prefs.feature_referer_stats eq 'y' and $tiki_p_view_referer_stats eq 'y'}{button href="tiki-referer_stats.php" _text="{tr}Referer stats{/tr}"}{/if}
	{if $best_objects_stats}{button _anchor="best_objects_stats" _text="{tr}Most viewed objects{/tr}"}{/if}
	{if $best_objects_stats_lastweek}{button _anchor="best_objects_stats_lastweek" _text="{tr}Most viewed objects in the last 7 days{/tr}"}{/if}
</div>

<br class="clear" />

<h2 id="site_stats">{tr}Site Stats{/tr}</h2>
{cycle values="odd,even" print=false advance=false}
<table class="normal">
	<tr class="{cycle}">
		<td>{tr}Date of first pageview{/tr}</td>
		<td style="text-align:right;">{$site_stats.started|tiki_long_date}</td>
	</tr>
	<tr class="{cycle}">
		<td>{tr}Days since first pageview{/tr}</td>
		<td style="text-align:right;">{$site_stats.days}</td>
	</tr>
	<tr class="{cycle}">
		<td>{tr}Total pageviews{/tr}</td>
		<td style="text-align:right;">{$site_stats.pageviews}</td>
	</tr>
	<tr class="{cycle}">
		<td>{tr}Average pageviews per day{/tr} ({tr}pvs{/tr})</td>
		<td style="text-align:right;">{$site_stats.ppd}</td>
	</tr>
	<tr class="{cycle}">
		<td>{$site_stats.bestdesc}</td>
		<td style="text-align:right;">{$site_stats.bestday}</td>
	</tr>
	<tr class="{cycle}">
		<td>{$site_stats.worstdesc}</td><td style="text-align:right;">{$site_stats.worstday}</td>
	</tr>
</table>

{if $wiki_stats}
	<h2 id="wiki_stats">{tr}Wiki Stats{/tr}</h2>
	{cycle values="odd,even" print=false advance=false}
	<table class="normal">
		<tr class="{cycle}">
			<td>{tr}Wiki Pages{/tr}</td>
			<td style="text-align:right;">{$wiki_stats.pages}</td>
		</tr>
		<tr class="{cycle}">
			<td>{tr}Size of Wiki Pages{/tr}</td>
			<td style="text-align:right;">{$wiki_stats.size} {tr}Mb{/tr}</td>
		</tr>
		<tr class="{cycle}">
			<td>{tr}Average page length{/tr}</td>
			<td style="text-align:right;">{$wiki_stats.bpp|string_format:"%.2f"} {tr}bytes{/tr}</td>
		</tr>
		<tr class="{cycle}">
			<td>{tr}Versions{/tr}</td>
			<td style="text-align:right;">{$wiki_stats.versions}</td>
		</tr>
		<tr class="{cycle}">
			<td>{tr}Average versions per page{/tr}</td>
			<td style="text-align:right;">{$wiki_stats.vpp|string_format:"%.2f"}</td>
		</tr>
		<tr class="{cycle}">
			<td>{tr}Visits to wiki pages{/tr}</td>
			<td style="text-align:right;">{$wiki_stats.visits}</td>
		</tr>
		<tr class="{cycle}">
			<td>{tr}Orphan pages{/tr}</td>
			<td style="text-align:right;">{$wiki_stats.orphan}</td>
		</tr>
		<tr class="{cycle}">
			<td>{tr}Average links per page{/tr}</td>
			<td style="text-align:right;">{$wiki_stats.lpp|string_format:"%.2f"}</td>
		</tr>
	</table>
{/if}

{if $igal_stats}
	<h2 id="igal_stats">{tr}Image galleries Stats{/tr}</h2>
	{cycle values="odd,even" print=false advance=false}
	<table class="normal">
		<tr class="{cycle}">
			<td>{tr}Galleries{/tr}</td>
			<td style="text-align:right;">{$igal_stats.galleries}</td>
		</tr>
		<tr class="{cycle}">
			<td>{tr}Images{/tr}</td>
			<td style="text-align:right;">{$igal_stats.images}</td>
		</tr>
		<tr class="{cycle}">
			<td>{tr}Average images per gallery{/tr}</td>
			<td style="text-align:right;">{$igal_stats.ipg|string_format:"%.2f"}</td>
		</tr>
		<tr class="{cycle}">
			<td>{tr}Total size of images{/tr}</td>
			<td style="text-align:right;">{$igal_stats.size} {tr}Mb{/tr}</td>
		</tr>
		<tr class="{cycle}">
			<td>{tr}Average image size{/tr}</td>
			<td style="text-align:right;">{$igal_stats.bpi|string_format:"%.2f"} {tr}bytes{/tr}</td>
		</tr>
		<tr class="{cycle}">
			<td>{tr}Visits to image galleries{/tr}</td>
			<td style="text-align:right;">{$igal_stats.visits|@default:'0'}</td>
		</tr>
	</table>
{/if}

{if $fgal_stats}
	<h2 id="fgal_stats">{tr}File galleries Stats{/tr}</h2>
	{cycle values="odd,even" print=false advance=false}
	<table class="normal">
		<tr class="{cycle}">
			<td>{tr}Galleries{/tr}</td>
			<td style="text-align:right;">{$fgal_stats.galleries}</td>
		</tr>
		<tr class="{cycle}">
			<td>{tr}Files{/tr}</td>
			<td style="text-align:right;">{$fgal_stats.files}</td>
		</tr>
		<tr class="{cycle}">
			<td>{tr}Average files per gallery{/tr}</td>
			<td style="text-align:right;">{$fgal_stats.fpg|string_format:"%.2f"}</td>
		</tr>
		<tr class="{cycle}">
			<td>{tr}Total size of files{/tr}</td>
			<td style="text-align:right;">{$fgal_stats.size} {tr}Mb{/tr}</td>
		</tr>
		<tr class="{cycle}">
			<td>{tr}Average file size{/tr}</td>
			<td style="text-align:right;">{$fgal_stats.bpf|string_format:"%.2f"} {tr}Mb{/tr}</td>
		</tr>
		<tr class="{cycle}">
			<td>{tr}Visits to file galleries{/tr}</td>
			<td style="text-align:right;">{$fgal_stats.visits|@default:'0'}</td>
		</tr>
		<tr class="{cycle}">
			<td>{tr}Downloads{/tr}</td>
			<td style="text-align:right;">{$fgal_stats.hits|@default:'0'}</td>
		</tr>
	</table>
{/if}

{if $cms_stats}
	<h2 id="cms_stats">{tr}CMS Stats{/tr}</h2>
	{cycle values="odd,even" print=false advance=false}
	<table class="normal">
		<tr class="{cycle}">
			<td>{tr}Articles{/tr}</td>
			<td style="text-align:right;">{$cms_stats.articles}</td>
		</tr>
		<tr class="{cycle}">
			<td>{tr}Total reads{/tr}</td>
			<td style="text-align:right;">{$cms_stats.reads|@default:'0'}</td>
		</tr>
		<tr class="{cycle}">
			<td>{tr}Average reads per article{/tr}</td>
			<td style="text-align:right;">{$cms_stats.rpa|string_format:"%.2f"}</td>
		</tr>
		<tr class="{cycle}">
			<td>{tr}Total articles size{/tr}</td>
			<td style="text-align:right;">{$cms_stats.size} {tr}bytes{/tr}</td>
		</tr>
		<tr class="{cycle}">
			<td>{tr}Average article size{/tr}</td>
			<td style="text-align:right;">{$cms_stats.bpa|string_format:"%.2f"} {tr}bytes{/tr}</td>
		</tr>
		<tr class="{cycle}">
			<td>{tr}Topics{/tr}</td>
			<td style="text-align:right;">{$cms_stats.topics}</td>
		</tr>
	</table>
{/if}

{if $forum_stats}
	{cycle values="odd,even" print=false advance=false}
	<h2 id="forum_stats">{tr}Forum Stats{/tr}</h2>
	<table class="normal">
		<tr class="{cycle}">
			<td>{tr}Forums{/tr}</td>
			<td style="text-align:right;">{$forum_stats.forums}</td>
		</tr>
		<tr class="{cycle}">
			<td>{tr}Total topics{/tr}</td>
			<td style="text-align:right;">{$forum_stats.topics}</td>
		</tr>
		<tr class="{cycle}">
			<td>{tr}Average topics per forums{/tr}</td>
			<td style="text-align:right;">{$forum_stats.tpf|string_format:"%.2f"}</td>
		</tr>
		<tr class="{cycle}">
			<td>{tr}Total replies{/tr}</td>
			<td style="text-align:right;">{$forum_stats.threads}</td>
		</tr>
		<tr class="{cycle}">
			<td>{tr}Average number of replies per topic{/tr}</td>
			<td style="text-align:right;">{$forum_stats.tpt|string_format:"%.2f"}</td>
		</tr>
		<tr class="{cycle}">
			<td>{tr}Visits to forums{/tr}</td>
			<td style="text-align:right;">{$forum_stats.visits|@default:'0'}</td>
		</tr>
	</table>
{/if}

{if $blog_stats}
	<h2 id="blog_stats">{tr}Blog Stats{/tr}</h2>
	{cycle values="odd,even" print=false advance=false}
	<table class="normal">
		<tr class="{cycle}">
			<td>{tr}Weblogs{/tr}</td>
			<td style="text-align:right;">{$blog_stats.blogs}</td>
		</tr>
		<tr class="{cycle}">
			<td>{tr}Total posts{/tr}</td>
			<td style="text-align:right;">{$blog_stats.posts}</td>
		</tr>
		<tr class="{cycle}">
			<td>{tr}Average posts per weblog{/tr}</td>
			<td style="text-align:right;">{$blog_stats.ppb|string_format:"%.2f"}</td>
		</tr>
		<tr class="{cycle}">
			<td>{tr}Total size of blog posts{/tr}</td>
			<td style="text-align:right;">{$blog_stats.size|@default:'0'}</td>
		</tr>
		<tr class="{cycle}">
			<td>{tr}Average posts size{/tr}</td>
			<td style="text-align:right;">{$blog_stats.bpp|string_format:"%.2f"}</td>
		</tr>
		<tr class="{cycle}">
			<td>{tr}Visits to weblogs{/tr}</td>
			<td style="text-align:right;">{$blog_stats.visits|@default:'0'}</td>
		</tr>
	</table>
{/if}

{if $poll_stats}
	<h2 id="poll_stats">{tr}Poll Stats{/tr}</h2>
	{cycle values="odd,even" print=false advance=false}
	<table class="normal">
		<tr class="{cycle}">
			<td>{tr}Polls{/tr}</td>
			<td style="text-align:right;">{$poll_stats.polls}</td>
		</tr>
		<tr class="{cycle}">
			<td>{tr}Total votes{/tr}</td>
			<td style="text-align:right;">{$poll_stats.votes|@default:'0'}</td>
		</tr>
		<tr class="{cycle}">
			<td>{tr}Average votes per poll{/tr}</td>
			<td style="text-align:right;">{$poll_stats.vpp|string_format:"%.2f"}</td>
		</tr>
	</table>
{/if}

{if $faq_stats}
	<h2 id="faq_stats">{tr}FAQ Stats{/tr}</h2>
	{cycle values="odd,even" print=false advance=false}
	<table class="normal">
		<tr class="{cycle}">
			<td>{tr}FAQs{/tr}</td>
			<td style="text-align:right;">{$faq_stats.faqs}</td>
		</tr>
		<tr class="{cycle}">
			<td>{tr}Total questions{/tr}</td>
			<td style="text-align:right;">{$faq_stats.questions}</td>
		</tr>
		<tr class="{cycle}">
			<td>{tr}Average questions per FAQ{/tr}</td>
			<td style="text-align:right;">{$faq_stats.qpf|string_format:"%.2f"}</td>
		</tr>
	</table>
{/if}

{if $user_stats}
	<h2 id="user_stats">{tr}User Stats{/tr}</h2>
	{cycle values="odd,even" print=false advance=false}
	<table class="normal">
		<tr class="{cycle}">
			<td>{tr}Users{/tr}</td>
			<td style="text-align:right;">{$user_stats.users}</td>
		</tr>
		<tr class="{cycle}">
			<td>{tr}User bookmarks{/tr}</td>
			<td style="text-align:right;">{$user_stats.bookmarks}</td>
		</tr>
		<tr class="{cycle}">
			<td>{tr}Average bookmarks per user{/tr}</td>
			<td style="text-align:right;">{$user_stats.bpu|string_format:"%.2f"}</td>
		</tr>
	</table>
{/if}

{if $quiz_stats}
	<h2 id="quiz_stats">{tr}Quiz Stats{/tr}</h2>
	{cycle values="odd,even" print=false advance=false}
	<table class="normal">
		<tr class="{cycle}">
			<td>{tr}Quizzes{/tr}</td>
			<td style="text-align:right;">{$quiz_stats.quizzes}</td>
		</tr>
		<tr class="{cycle}">
			<td>{tr}Questions{/tr}</td>
			<td style="text-align:right;">{$quiz_stats.questions}</td>
		</tr>
		<tr class="{cycle}">
			<td>{tr}Average questions per quiz{/tr}</td>
			<td style="text-align:right;">{$quiz_stats.qpq|string_format:"%.2f"}</td>
		</tr>
		<tr class="{cycle}">
			<td>{tr}Quizzes taken{/tr}</td>
			<td style="text-align:right;">{$quiz_stats.visits|@default:'0'}</td>
		</tr>
		<tr class="{cycle}">
			<td>{tr}Average quiz score{/tr}</td>
			<td style="text-align:right;">{$quiz_stats.avg|string_format:"%.2f"}</td>
		</tr>
		<tr class="{cycle}">
			<td>{tr}Average time per quiz{/tr}</td>
			<td style="text-align:right;">{$quiz_stats.avgtime|string_format:"%.2f"} {tr}secs{/tr}</td>
		</tr>
	</table>
{/if}

{if $best_objects_stats_between}
<h2 id="best_objects_stats_between">{tr}Most viewed objects in period{/tr}</h2>
	<form method="post" action="tiki-stats.php">
		{html_select_date time=$startDate prefix="startDate_" start_year=$start_year end_year=$end_year day_value_format="%02d" field_order=$prefs.display_field_order}
	 	&rarr; {html_select_date time=$endDate prefix="endDate_" start_year=$start_year end_year=$end_year day_value_format="%02d" field_order=$prefs.display_field_order}
		<input type="submit" name="modify" value="{tr}Filter{/tr}"/>
	</form>
	<table class="normal">
		<tr>
			<th>{tr}Object{/tr}</th>
			<th>{tr}Section{/tr}</th>
			<th>{tr}Hits{/tr}</th>
		</tr>
		{cycle values="odd,even" print=false advance=false}
		{section name=i loop=$best_objects_stats_between}
			<tr class="{cycle}">
				<td>{$best_objects_stats_between[i]->object|escape}</td>
				<td>{tr}{$best_objects_stats_between[i]->type}{/tr}</td>
				<td>{$best_objects_stats_between[i]->hits}</td>
			</tr>
		{/section}
	</table>
{/if}

{if $best_objects_stats_lastweek}
	<h2 id="best_objects_stats_lastweek">{tr}Most viewed objects in the last 7 days{/tr}</h2>
	<table class="normal">
		<tr>
			<th>{tr}Object{/tr}</th>
			<th>{tr}Section{/tr}</th>
			<th>{tr}Hits{/tr}</th>
		</tr>
		{cycle values="odd,even" print=false advance=false}
		{section name=i loop=$best_objects_stats_lastweek}
			<tr class="{cycle}">
				<td>{$best_objects_stats_lastweek[i]->object|escape}</td>
				<td>{tr}{$best_objects_stats_lastweek[i]->type}{/tr}</td>
				<td>{$best_objects_stats_lastweek[i]->hits}</td>
			</tr>
		{/section}
	</table>
{/if}

<a name="charts" href="tiki-stats.php?chart=usage#charts" class="link">{tr}Usage chart{/tr}</a>

{if $usage_chart eq 'y'}
	<div align="center">
		<img src="tiki-usage_chart.php" alt="{tr}Usage chart image{/tr}"/>
	</div>
	<br />
	<div align="center">
		<img src="tiki-usage_chart.php?type=daily" alt="{tr}Daily Usage{/tr}"/>
	</div>
{/if}

