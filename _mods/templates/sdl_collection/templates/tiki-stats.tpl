{* $Header: /cvsroot/tikiwiki/_mods/templates/sdl_collection/templates/tiki-stats.tpl,v 1.1 2004-05-09 23:09:15 damosoft Exp $ *}

<a href="tiki-stats.php" class="pagetitle">{tr}Statistics{/tr}</a><br /><br />

<div id="page-bar">
<span class="button2"> <a class="linkbut" href="#site_stats">{tr}[ Site{/tr}</a></span>
{if $wiki_stats}<span class="button2"> <a class="linkbut" href="#wiki_stats">{tr}| Wiki{/tr}</a></span>{/if}
{if $igal_stats}<span class="button2"> <a class="linkbut" href="#igal_stats">{tr}| Image Galleries{/tr}</a></span>{/if}
{if $fgal_stats}<span class="button2"> <a class="linkbut" href="#fgal_stats">{tr}| File Galleries{/tr}</a></span>{/if}
{if $cms_stats}<span class="button2"> <a class="linkbut" href="#cms_stats">{tr}| Articles{/tr}</a></span>{/if}
{if $forum_stats}<span class="button2"> <a class="linkbut" href="#forum_stats">{tr}| Forums{/tr}</a></span>{/if}
{if $blog_stats}<span class="button2"> <a class="linkbut" href="#blog_stats">{tr}| Blogs{/tr}</a></span>{/if}
{if $poll_stats}<span class="button2"> <a class="linkbut" href="#poll_stats">{tr}| Polls{/tr}</a></span>{/if}
{if $faq_stats}<span class="button2"> <a class="linkbut" href="#faq_stats">{tr}| FAQs{/tr}</a></span>{/if}
{if $user_stats}<span class="button2"> <a class="linkbut" href="#user_stats">{tr}| User{/tr}</a></span>{/if}
{if $quiz_stats}<span class="button2"> <a class="linkbut" href="#quiz_stats">{tr}| Quizzes{/tr}</a></span>{/if}
<span class="button2"> <a class="linkbut" href="#charts">{tr}| Charts ]{/tr}</a></span>
</div>

<table class="normal">

<!-- Site stats -->
<tr><td class="heading" colspan="2"><a name="site_stats">{tr}Site Statistics{/tr}</a></td></tr>
<tr><td class="even">{tr}Started{/tr}</td><td class="even" style="text-align:right;">{$site_stats.started|tiki_short_date}</td></tr>
<tr><td class="odd">{tr}Days online{/tr}</td><td class="odd" style="text-align:right;">{$site_stats.days}</td></tr>
<tr><td class="even">{tr}Total pageviews{/tr}</td><td class="even" style="text-align:right;">{$site_stats.pageviews}</td></tr>
<tr><td class="odd">{tr}Average pageviews per day{/tr}</td><td class="odd" style="text-align:right;">{$site_stats.ppd|string_format:"%.2f"}</td></tr>
<tr><td class="even">{tr}Best day{/tr}</td><td class="even" style="text-align:right;">{$site_stats.bestday|tiki_short_date} ({$site_stats.bestpvs} {tr}pvs{/tr})</td></tr>
<tr><td class="odd">{tr}Worst day{/tr}</td><td class="odd" style="text-align:right;">{$site_stats.worstday|tiki_short_date} ({$site_stats.worstpvs} {tr}pvs{/tr})</td></tr>
<!-- Site stats --> 

<!-- Wiki Stats -->
{if $wiki_stats}
 <tr><td class="heading" colspan="2"><a name="wiki_stats">{tr}Wiki Statistics{/tr}</a></td></tr>
<tr><td class="even">{tr}Wiki Pages{/tr}</td><td class="even" style="text-align:right;">{$wiki_stats.pages}</td></tr>
<tr><td class="odd">{tr}Size of Wiki Pages{/tr}</td><td class="odd" style="text-align:right;">{$wiki_stats.size} {tr}Mb{/tr}</td></tr>
<tr><td class="even">{tr}Average page length{/tr}</td><td class="even" style="text-align:right;">{$wiki_stats.bpp|string_format:"%.2f"} {tr}bytes{/tr}</td></tr>
<tr><td class="odd">{tr}Versions{/tr}</td><td class="odd" style="text-align:right;">{$wiki_stats.versions}</td></tr>
<tr><td class="even">{tr}Average versions per page{/tr}</td><td class="even" style="text-align:right;">{$wiki_stats.vpp|string_format:"%.2f"}</td></tr>
<tr><td class="odd">{tr}Visits to wiki pages{/tr}</td><td class="odd" style="text-align:right;">{$wiki_stats.visits}</td></tr>
<tr><td class="even">{tr}Orphan pages{/tr}</td><td class="even" style="text-align:right;">{$wiki_stats.orphan}</td></tr>
<tr><td class="odd">{tr}Average links per page{/tr}</td><td class="odd" style="text-align:right;">{$wiki_stats.lpp|string_format:"%.2f"}</td></tr>
{/if}
<!-- Wiki Stats -->

<!-- Image gallleries stats -->
{if $igal_stats}
<tr><td class="heading" colspan="2"><a name="igal_stats">{tr}Image Galleries Statistics{/tr}</a></td></tr>
<tr><td class="even">{tr}Galleries{/tr}</td><td class="even" style="text-align:right;">{$igal_stats.galleries}</td></tr>
<tr><td class="odd">{tr}Images{/tr}</td><td class="odd" style="text-align:right;">{$igal_stats.images}</td></tr>
<tr><td class="even">{tr}Average images per gallery{/tr}</td><td class="even" style="text-align:right;">{$igal_stats.ipg|string_format:"%.2f"}</td></tr>
<tr><td class="odd">{tr}Total size of images{/tr}</td><td class="odd" style="text-align:right;">{$igal_stats.size} {tr}Mb{/tr}</td></tr>
<tr><td class="even">{tr}Average image size{/tr}</td><td class="even" style="text-align:right;">{$igal_stats.bpi|string_format:"%.2f"} {tr}bytes{/tr}</td></tr>
<tr><td class="odd">{tr}Visits to image galleries{/tr}</td><td class="odd" style="text-align:right;">{$igal_stats.visits}</td></tr>
{/if}
<!-- Image gallleries stats -->

<!-- File gallleries stats -->
{if $fgal_stats}
<tr><td class="heading" colspan="2"><a name="fgal_stats">{tr}File Galleries Statistics{/tr}</a></td></tr>
<tr><td class="even">{tr}Galleries{/tr}</td><td class="even" style="text-align:right;">{$fgal_stats.galleries}</td></tr>
<tr><td class="odd">{tr}Files{/tr}</td><td class="odd" style="text-align:right;">{$fgal_stats.files}</td></tr>
<tr><td class="even">{tr}Average files per gallery{/tr}</td><td class="even" style="text-align:right;">{$fgal_stats.fpg|string_format:"%.2f"}</td></tr>
<tr><td class="odd">{tr}Total size of files{/tr}</td><td class="odd" style="text-align:right;">{$fgal_stats.size} {tr}Mb{/tr}</td></tr>
<tr><td class="even">{tr}Average file size{/tr}</td><td class="even" style="text-align:right;">{$fgal_stats.bpf|string_format:"%.2f"} {tr}Mb{/tr}</td></tr>
<tr><td class="odd">{tr}Visits to file galleries{/tr}</td><td class="odd" style="text-align:right;">{$fgal_stats.visits}</td></tr>
<tr><td class="even">{tr}Downloads{/tr}</td><td class="even" style="text-align:right;">{$fgal_stats.downloads}</td></tr>
{/if}
<!-- File gallleries stats -->

<!-- CMS stats -->
{if $cms_stats}
<tr><td class="heading" colspan="2"><a name="cms_stats">{tr}Articles Statistics{/tr}</a></td></tr>
<tr><td class="even">{tr}Articles{/tr}</td><td class="even" style="text-align:right;">{$cms_stats.articles}</td></tr>
<tr><td class="odd">{tr}Total reads{/tr}</td><td class="odd" style="text-align:right;">{$cms_stats.reads}</td></tr>
<tr><td class="even">{tr}Average reads per article{/tr}</td><td class="even" style="text-align:right;">{$cms_stats.rpa|string_format:"%.2f"}</td></tr>
<tr><td class="odd">{tr}Total articles size{/tr}</td><td class="odd" style="text-align:right;">{$cms_stats.size} {tr}bytes{/tr}</td></tr>
<tr><td class="even">{tr}Average article size{/tr}</td><td class="even" style="text-align:right;">{$cms_stats.bpa|string_format:"%.2f"} {tr}bytes{/tr}</td></tr>
<tr><td class="odd">{tr}Topics{/tr}</td><td class="odd" style="text-align:right;">{$cms_stats.topics}</td></tr>
{/if}
<!-- CMS stats -->

<!-- Forum stats -->
{if $forum_stats}
<tr><td class="heading" colspan="2"><a name="forum_stats">{tr}Forum Statistics{/tr}</a></td></tr>
<tr><td class="even">{tr}Forums{/tr}</td><td class="even" style="text-align:right;">{$forum_stats.forums}</td></tr>
<tr><td class="odd">{tr}Total topics{/tr}</td><td class="odd" style="text-align:right;">{$forum_stats.topics}</td></tr>
<tr><td class="even">{tr}Average topics per forums{/tr}</td><td class="even" style="text-align:right;">{$forum_stats.tpf|string_format:"%.2f"}</td></tr>
<tr><td class="odd">{tr}Total threads{/tr}</td><td class="odd" style="text-align:right;">{$forum_stats.threads}</td></tr>
<tr><td class="even">{tr}Average threads per topic{/tr}</td><td class="even" style="text-align:right;">{$forum_stats.tpt|string_format:"%.2f"}</td></tr>
<tr><td class="odd">{tr}Visits to forums{/tr}</td><td class="odd" style="text-align:right;">{$forum_stats.visits}</td></tr>
{/if}
<!-- Forum stats -->

<!-- Blogs stats -->
{if $blog_stats}
<tr><td class="heading" colspan="2"><a name="blog_stats">{tr}Blog Statistics{/tr}</a></td></tr>
<tr><td class="even">{tr}Weblogs{/tr}</td><td class="even" style="text-align:right;">{$blog_stats.blogs}</td></tr>
<tr><td class="odd">{tr}Total posts{/tr}</td><td class="odd" style="text-align:right;">{$blog_stats.posts}</td></tr>
<tr><td class="even">{tr}Average posts per weblog{/tr}</td><td class="even" style="text-align:right;">{$blog_stats.ppb|string_format:"%.2f"}</td></tr>
<tr><td class="odd">{tr}Total size of blog posts{/tr}</td><td class="odd" style="text-align:right;">{$blog_stats.size}</td></tr>
<tr><td class="even">{tr}Average posts size{/tr}</td><td class="even" style="text-align:right;">{$blog_stats.bpp|string_format:"%.2f"}</td></tr>
<tr><td class="odd">{tr}Visits to weblogs{/tr}</td><td class="odd" style="text-align:right;">{$blog_stats.visits}</td></tr>
{/if}
<!-- Blogs stats -->

<!-- Poll stats -->
{if $poll_stats}
<tr><td class="heading" colspan="2"><a name="poll_stats"> {tr}Poll Statistics{/tr}</a></td></tr>
<tr><td class="even">{tr}Polls{/tr}</td><td class="even" style="text-align:right;">{$poll_stats.polls}</td></tr>
<tr><td class="odd">{tr}Total votes{/tr}</td><td class="odd" style="text-align:right;">{$poll_stats.votes}</td></tr>
<tr><td class="even">{tr}Average votes per poll{/tr}</td><td class="even" style="text-align:right;">{$poll_stats.vpp|string_format:"%.2f"}</td></tr>
{/if}
<!-- Poll stats -->

<!-- FAQ stats -->
{if $faq_stats}
<tr><td class="heading" colspan="2"><a name="faq_stats">{tr}FAQs Statistics{/tr}</a></td></tr>
<tr><td class="even">{tr}FAQs{/tr}</td><td class="even" style="text-align:right;">{$faq_stats.faqs}</td></tr>
<tr><td class="odd">{tr}Total questions{/tr}</td><td class="odd" style="text-align:right;">{$faq_stats.questions}</td></tr>
<tr><td class="even">{tr}Average questions per FAQ{/tr}</td><td class="even" style="text-align:right;">{$faq_stats.qpf|string_format:"%.2f"}</td></tr>
{/if}
<!-- FAQ stats -->

<!-- Users stats -->
{if $user_stats}
<tr><td class="heading" colspan="2"><a name="user_stats">{tr}User Statistics</a>{/tr}</td></tr>
<tr><td class="even">{tr}Users{/tr}</td><td class="even" style="text-align:right;">{$user_stats.users}</td></tr>
<tr><td class="odd">{tr}User bookmarks{/tr}</td><td class="odd" style="text-align:right;">{$user_stats.bookmarks}</td></tr>
<tr><td class="even">{tr}Average bookmarks per user{/tr}</td><td class="even" style="text-align:right;">{$user_stats.bpu|string_format:"%.2f"}</td></tr>
{/if}
<!-- Usersstats -->

<!-- Quiz stats -->
{if $quiz_stats}

<tr><td class="heading" colspan="2"><a name="quiz_stats">{tr}Quiz Statistics{/tr}</a></td></tr>
<tr><td class="even">{tr}Quizzes{/tr}</td><td class="even" style="text-align:right;">{$quiz_stats.quizzes}</td></tr>
<tr><td class="odd">{tr}Questions{/tr}</td><td class="odd" style="text-align:right;">{$quiz_stats.questions}</td></tr>
<tr><td class="even">{tr}Average questions per quiz{/tr}</td><td class="even" style="text-align:right;">{$quiz_stats.qpq|string_format:"%.2f"}</td></tr>
<tr><td class="odd">{tr}Quizzes taken{/tr}</td><td class="odd" style="text-align:right;">{$quiz_stats.visits}</td></tr>
<tr><td class="even">{tr}Average quiz score{/tr}</td><td class="even" style="text-align:right;">{$quiz_stats.avg|string_format:"%.2f"}</td></tr>
<tr><td class="odd">{tr}Average time per quiz{/tr}</td><td class="odd" style="text-align:right;">{$quiz_stats.avgtime|string_format:"%.2f"} {tr}secs{/tr}</td></tr>
{/if}
<!-- Quiz stats -->
</table>
<br />
<br />
<br />
<a href="tiki-stats.php?chart=usage" class="link" name="charts">{tr}Usage Chart{/tr}</a><br /><br />

{if $usage_chart eq 'y'}
<br /> 
<div align="center">
<img src="tiki-usage_chart.php" alt='{tr}Usage chart image{/tr}'/>
</div>
<br /><br />
{/if}

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

<br />
<br />
<br />
