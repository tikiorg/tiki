{* $Id$ *}

<h1><a href="tiki-stats.php" class="pagetitle">{tr}Stats{/tr}</a></h1>

<div id="page-bar">

<span class="button2"> <a class="linkbut" href="#site_stats">{tr}Site{/tr}</a></span>
{if $wiki_stats}<span class="button2"> <a class="linkbut" href="#wiki_stats">{tr}Wiki{/tr}</a></span>{/if}
{if $igal_stats}<span class="button2"> <a class="linkbut" href="#igal_stats">{tr}Image galleries{/tr}</a></span>{/if}
{if $fgal_stats}<span class="button2"> <a class="linkbut" href="#fgal_stats">{tr}File galleries{/tr}</a></span>{/if}
{if $cms_stats}<span class="button2"> <a class="linkbut" href="#cms_stats">{tr}CMS{/tr}</a></span>{/if}
{if $forum_stats}<span class="button2"> <a class="linkbut" href="#forum_stats">{tr}Forums{/tr}</a></span>{/if}
{if $blog_stats}<span class="button2"> <a class="linkbut" href="#blog_stats">{tr}Blogs{/tr}</a></span>{/if}
{if $poll_stats}<span class="button2"> <a class="linkbut" href="#poll_stats">{tr}Polls{/tr}</a></span>{/if}
{if $faq_stats}<span class="button2"> <a class="linkbut" href="#faq_stats">{tr}FAQs{/tr}</a></span>{/if}
{if $user_stats}<span class="button2"> <a class="linkbut" href="#user_stats">{tr}User{/tr}</a></span>{/if}
{if $quiz_stats}<span class="button2"> <a class="linkbut" href="#quiz_stats">{tr}Quizzes{/tr}</a></span>{/if}
{if $prefs.feature_referer_stats eq 'y' and $tiki_p_view_referer_stats eq 'y'}<span class="button2"> <a class="linkbut" href="tiki-referer_stats.php">{tr}Referer stats{/tr}</a></span>{/if}
{if $best_objects_stats}<span class="button2"> <a class="linkbut" href="#best_objects_stats">{tr}Most viewed objects{/tr}</a></span>{/if}
{if $best_objects_stats_lastweek}<span class="button2"> <a class="linkbut" href="#best_objects_stats_lastweek">{tr}Most viewed objects in the last 7 days{/tr}</a></span>{/if}


</div>
<br class="clear" />
{* Site stats *}
<h2 id="site_stats">{tr}Site Stats{/tr}</h2>
{cycle values="odd,even" print=false advance=false}
<table class="normal">
	<tr class="{cycle}"><td>{tr}Started{/tr}</td><td style="text-align:right;">{$site_stats.started|tiki_long_date}</td></tr>
	<tr class="{cycle}"><td>{tr}Days online{/tr}</td><td style="text-align:right;">{$site_stats.days}</td></tr>
	<tr class="{cycle}"><td>{tr}Total pageviews{/tr}</td><td style="text-align:right;">{$site_stats.pageviews}</td></tr>
	<tr class="{cycle}"><td>{tr}Average pageviews per day{/tr} ({tr}pvs{/tr})</td><td style="text-align:right;">{$site_stats.ppd|string_format:"%.2f"}</td></tr>
	<tr class="{cycle}"><td>{tr}Best day{/tr}</td><td style="text-align:right;">{$site_stats.bestday|tiki_long_date} ({$site_stats.bestpvs} {tr}pvs{/tr})</td></tr>
	<tr class="{cycle}"><td>{tr}Worst day{/tr}</td><td style="text-align:right;">{$site_stats.worstday|tiki_long_date} ({$site_stats.worstpvs} {tr}pvs{/tr})</td></tr>
{* Site stats *}
</table>

{* Wiki Stats *}
{if $wiki_stats}
<br /><h2 id="wiki_stats">{tr}Wiki Stats{/tr}</h2>
{cycle values="odd,even" print=false advance=false}
<table class="normal">
	<tr class="{cycle}"><td>{tr}Wiki Pages{/tr}</td><td style="text-align:right;">{$wiki_stats.pages}</td></tr>
	<tr class="{cycle}"><td>{tr}Size of Wiki Pages{/tr}</td><td style="text-align:right;">{$wiki_stats.size} {tr}Mb{/tr}</td></tr>
	<tr class="{cycle}"><td>{tr}Average page length{/tr}</td><td style="text-align:right;">{$wiki_stats.bpp|string_format:"%.2f"} {tr}bytes{/tr}</td></tr>
	<tr class="{cycle}"><td>{tr}Versions{/tr}</td><td style="text-align:right;">{$wiki_stats.versions}</td></tr>
	<tr class="{cycle}"><td>{tr}Average versions per page{/tr}</td><td style="text-align:right;">{$wiki_stats.vpp|string_format:"%.2f"}</td></tr>
	<tr class="{cycle}"><td>{tr}Visits to wiki pages{/tr}</td><td style="text-align:right;">{$wiki_stats.visits}</td></tr>
	<tr class="{cycle}"><td>{tr}Orphan pages{/tr}</td><td style="text-align:right;">{$wiki_stats.orphan}</td></tr>
	<tr class="{cycle}"><td>{tr}Average links per page{/tr}</td><td style="text-align:right;">{$wiki_stats.lpp|string_format:"%.2f"}</td></tr>
</table>
{/if}
{* Wiki Stats *}

{* Image gallleries stats *}
{if $igal_stats}
<br /><h2 id="igal_stats">{tr}Image galleries Stats{/tr}</h2>
{cycle values="odd,even" print=false advance=false}
<table class="normal">
	<tr class="{cycle}"><td>{tr}Galleries{/tr}</td><td style="text-align:right;">{$igal_stats.galleries}</td></tr>
	<tr class="{cycle}"><td>{tr}Images{/tr}</td><td style="text-align:right;">{$igal_stats.images}</td></tr>
	<tr class="{cycle}"><td>{tr}Average images per gallery{/tr}</td><td style="text-align:right;">{$igal_stats.ipg|string_format:"%.2f"}</td></tr>
	<tr class="{cycle}"><td>{tr}Total size of images{/tr}</td><td style="text-align:right;">{$igal_stats.size} {tr}Mb{/tr}</td></tr>
	<tr class="{cycle}"><td>{tr}Average image size{/tr}</td><td style="text-align:right;">{$igal_stats.bpi|string_format:"%.2f"} {tr}bytes{/tr}</td></tr>
	<tr class="{cycle}"><td>{tr}Visits to image galleries{/tr}</td><td style="text-align:right;">{$igal_stats.visits|@default:'0'}</td></tr>
</table>
{/if}
{* Image gallleries stats *}

{* File gallleries stats *}
{if $fgal_stats}
<br /><h2 id="fgal_stats">{tr}File galleries Stats{/tr}</h2>
{cycle values="odd,even" print=false advance=false}
<table class="normal">
	<tr class="{cycle}"><td>{tr}Galleries{/tr}</td><td style="text-align:right;">{$fgal_stats.galleries}</td></tr>
	<tr class="{cycle}"><td>{tr}Files{/tr}</td><td style="text-align:right;">{$fgal_stats.files}</td></tr>
	<tr class="{cycle}"><td>{tr}Average files per gallery{/tr}</td><td style="text-align:right;">{$fgal_stats.fpg|string_format:"%.2f"}</td></tr>
	<tr class="{cycle}"><td>{tr}Total size of files{/tr}</td><td style="text-align:right;">{$fgal_stats.size} {tr}Mb{/tr}</td></tr>
	<tr class="{cycle}"><td>{tr}Average file size{/tr}</td><td style="text-align:right;">{$fgal_stats.bpf|string_format:"%.2f"} {tr}Mb{/tr}</td></tr>
	<tr class="{cycle}"><td>{tr}Visits to file galleries{/tr}</td><td style="text-align:right;">{$fgal_stats.visits|@default:'0'}</td></tr>
	<tr class="{cycle}"><td>{tr}Downloads{/tr}</td><td style="text-align:right;">{$fgal_stats.hits|@default:'0'}</td></tr>
</table>
{/if}
{* File gallleries stats *}

{* CMS stats *}
{if $cms_stats}
<br /><h2 id="cms_stats">{tr}CMS Stats{/tr}</h2>
{cycle values="odd,even" print=false advance=false}
<table class="normal">
	<tr class="{cycle}"><td>{tr}Articles{/tr}</td><td style="text-align:right;">{$cms_stats.articles}</td></tr>
	<tr class="{cycle}"><td>{tr}Total reads{/tr}</td><td style="text-align:right;">{$cms_stats.reads|@default:'0'}</td></tr>
	<tr class="{cycle}"><td>{tr}Average reads per article{/tr}</td><td style="text-align:right;">{$cms_stats.rpa|string_format:"%.2f"}</td></tr>
	<tr class="{cycle}"><td>{tr}Total articles size{/tr}</td><td style="text-align:right;">{$cms_stats.size} {tr}bytes{/tr}</td></tr>
	<tr class="{cycle}"><td>{tr}Average article size{/tr}</td><td style="text-align:right;">{$cms_stats.bpa|string_format:"%.2f"} {tr}bytes{/tr}</td></tr>
	<tr class="{cycle}"><td>{tr}Topics{/tr}</td><td style="text-align:right;">{$cms_stats.topics}</td></tr>
</table>
{/if}
{* CMS stats *}

{* Forum stats *}
{if $forum_stats}
{cycle values="odd,even" print=false advance=false}
<br /><h2 id="forum_stats">{tr}Forum Stats{/tr}</h2>
<table class="normal">
	<tr class="{cycle}"><td>{tr}Forums{/tr}</td><td style="text-align:right;">{$forum_stats.forums}</td></tr>
	<tr class="{cycle}"><td>{tr}Total topics{/tr}</td><td style="text-align:right;">{$forum_stats.topics}</td></tr>
	<tr class="{cycle}"><td>{tr}Average topics per forums{/tr}</td><td style="text-align:right;">{$forum_stats.tpf|string_format:"%.2f"}</td></tr>
	<tr class="{cycle}"><td>{tr}Total replies{/tr}</td><td style="text-align:right;">{$forum_stats.threads}</td></tr>
	<tr class="{cycle}"><td>{tr}Average number of replies per topic{/tr}</td><td style="text-align:right;">{$forum_stats.tpt|string_format:"%.2f"}</td></tr>
	<tr class="{cycle}"><td>{tr}Visits to forums{/tr}</td><td style="text-align:right;">{$forum_stats.visits|@default:'0'}</td></tr>
</table>
{/if}
{* Forum stats *}

{* Blogs stats *}
{if $blog_stats}
<br /><h2 id="blog_stats">{tr}Blog Stats{/tr}</h2>
{cycle values="odd,even" print=false advance=false}
<table class="normal">
	<tr class="{cycle}"><td>{tr}Weblogs{/tr}</td><td style="text-align:right;">{$blog_stats.blogs}</td></tr>
	<tr class="{cycle}"><td>{tr}Total posts{/tr}</td><td style="text-align:right;">{$blog_stats.posts}</td></tr>
	<tr class="{cycle}"><td>{tr}Average posts per weblog{/tr}</td><td style="text-align:right;">{$blog_stats.ppb|string_format:"%.2f"}</td></tr>
	<tr class="{cycle}"><td>{tr}Total size of blog posts{/tr}</td><td style="text-align:right;">{$blog_stats.size|@default:'0'}</td></tr>
	<tr class="{cycle}"><td>{tr}Average posts size{/tr}</td><td style="text-align:right;">{$blog_stats.bpp|string_format:"%.2f"}</td></tr>
	<tr class="{cycle}"><td>{tr}Visits to weblogs{/tr}</td><td style="text-align:right;">{$blog_stats.visits|@default:'0'}</td></tr>
</table>
{/if}
{* Blogs stats *}

{* Poll stats *}
{if $poll_stats}
<br /><h2 id="poll_stats">{tr}Poll Stats{/tr}</h2>
{cycle values="odd,even" print=false advance=false}
<table class="normal">
	<tr class="{cycle}"><td>{tr}Polls{/tr}</td><td  style="text-align:right;">{$poll_stats.polls}</td></tr>
	<tr class="{cycle}"><td>{tr}Total votes{/tr}</td><td style="text-align:right;">{$poll_stats.votes|@default:'0'}</td></tr>
	<tr class="{cycle}"><td>{tr}Average votes per poll{/tr}</td><td style="text-align:right;">{$poll_stats.vpp|string_format:"%.2f"}</td></tr>
</table>
{/if}
{* Poll stats *}

{* FAQ stats *}
{if $faq_stats}
<br /><h2 id="faq_stats">{tr}FAQ Stats{/tr}</h2>
{cycle values="odd,even" print=false advance=false}
<table class="normal">
	<tr class="{cycle}"><td>{tr}FAQs{/tr}</td><td style="text-align:right;">{$faq_stats.faqs}</td></tr>
	<tr class="{cycle}"><td>{tr}Total questions{/tr}</td><td style="text-align:right;">{$faq_stats.questions}</td></tr>
	<tr class="{cycle}"><td>{tr}Average questions per FAQ{/tr}</td><td style="text-align:right;">{$faq_stats.qpf|string_format:"%.2f"}</td></tr>
</table>
{/if}
{* FAQ stats *}

{* Users stats *}
{if $user_stats}
<br /><h2 id="user_stats">{tr}User Stats{/tr}</h2>
{cycle values="odd,even" print=false advance=false}
<table class="normal">
	<tr class="{cycle}"><td>{tr}Users{/tr}</td><td style="text-align:right;">{$user_stats.users}</td></tr>
	<tr class="{cycle}"><td>{tr}User bookmarks{/tr}</td><td style="text-align:right;">{$user_stats.bookmarks}</td></tr>
	<tr class="{cycle}"><td>{tr}Average bookmarks per user{/tr}</td><td style="text-align:right;">{$user_stats.bpu|string_format:"%.2f"}</td></tr>
</table>
{/if}
{* Usersstats *}

{* Quiz stats *}
{if $quiz_stats}
<br /><h2 id="quiz_stats">{tr}Quiz Stats{/tr}</h2>
{cycle values="odd,even" print=false advance=false}
<table class="normal">
	<tr class="{cycle}"><td>{tr}Quizzes{/tr}</td><td style="text-align:right;">{$quiz_stats.quizzes}</td></tr>
	<tr class="{cycle}"><td>{tr}Questions{/tr}</td><td style="text-align:right;">{$quiz_stats.questions}</td></tr>
	<tr class="{cycle}"><td>{tr}Average questions per quiz{/tr}</td><td style="text-align:right;">{$quiz_stats.qpq|string_format:"%.2f"}</td></tr>
	<tr class="{cycle}"><td>{tr}Quizzes taken{/tr}</td><td style="text-align:right;">{$quiz_stats.visits|@default:'0'}</td></tr>
	<tr class="{cycle}"><td>{tr}Average quiz score{/tr}</td><td style="text-align:right;">{$quiz_stats.avg|string_format:"%.2f"}</td></tr>
	<tr class="{cycle}"><td>{tr}Average time per quiz{/tr}</td><td style="text-align:right;">{$quiz_stats.avgtime|string_format:"%.2f"} {tr}secs{/tr}</td></tr>
</table>
{/if}
{* Quiz stats *}

{if $best_objects_stats}
<br /><h2 id="best_objects_stats">{tr}Most viewed objects{/tr}</h2>

<table class="normal">
	<tr><th class="heading">{tr}Object{/tr}</td><th class="heading">{tr}Section{/tr}</td><th class="heading">{tr}Hits{/tr}</td></tr>
{cycle values="odd,even" print=false advance=false}
{section name=i loop=$best_objects_stats}
	<tr>
		<td class="{cycle advance=false}">{$best_objects_stats[i]->object}</th>
		<td class="{cycle advance=false}">{tr}{$best_objects_stats[i]->type}{/tr}</th>
		<td class="{cycle}">{$best_objects_stats[i]->hits}</th>
	</tr>
{/section}
</table>
{/if}

{if $best_objects_stats_lastweek}
<br /><h2 id="best_objects_stats_lastweek">{tr}Most viewed objects in the last 7 days{/tr}</h2>
<table class="normal">
	<tr>
		<th class="heading">{tr}Object{/tr}</th>
		<th class="heading">{tr}Section{/tr}</th>
		<th class="heading">{tr}Hits{/tr}</th></tr>
{cycle values="odd,even" print=false advance=false}
{section name=i loop=$best_objects_stats_lastweek}
	<tr>
		<td class="{cycle advance=false}">{$best_objects_stats_lastweek[i]->object}</td>
		<td class="{cycle advance=false}">{tr}{$best_objects_stats_lastweek[i]->type}{/tr}</td>
		<td class="{cycle}">{$best_objects_stats_lastweek[i]->hits}</td>
	</tr>
{/section}
</table>
{/if}
<br />
<br />
<br />
<a name="charts" href="tiki-stats.php?chart=usage#charts" class="link">{tr}Usage chart{/tr}</a><br /><br />

{if $usage_chart eq 'y'}
<br /> 
<div align="center">
<img src="tiki-usage_chart.php" alt='{tr}Usage chart image{/tr}'/>
</div>
<br />
<div align="center">
<img src="tiki-usage_chart.php?type=daily" alt='{tr}Daily Usage{/tr}'/>
</div>
<br /><br />
{/if}

{* Removed for 1.9.0
<form action="tiki-stats.php" method="post">
{tr}Show chart for the last {/tr}
<input type="text" name="days" size="10" value="{$days|escape}" /> {tr}days (0=all){/tr}
<input type="submit" name="pv_chart" value="{tr}Display{/tr}" />
</form>
{if $pv_chart eq 'y'} 
<br /> 
<div align="center">
<img src="tiki-pv_chart.php?days={$days}" alt=''/>
</div>
<br />
{/if}
<br />
  *}
