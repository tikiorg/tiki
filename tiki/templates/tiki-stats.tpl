<a href="tiki-stats.php" class="pagetitle">{tr}Stats{/tr}</a><br/><br/>
<a href="tiki-stats.php?chart=usage" class="link">{tr}Usage chart{/tr}</a><br/><br/>
{if $usage_chart eq 'y'}
<br/> 
<div align="center">
<img src="tiki-usage_chart.php" />
</div>
<br/><br/>
{/if}
[<a class="link" href="#site_stats">{tr}Site{/tr}</a> | <a class="link" href="#wiki_stats">{tr}Wiki{/tr}</a> | <a class="link" href="#igal_stats">{tr}Image galleries{/tr}</a> | <a class="link" href="#fgal_stats">{tr}File galleries{/tr}</a> | <a class="link" href="#forum_stats">{tr}Forums{/tr}</a> | <a class="link" href="#faq_stats">{tr}FAQs{/tr}</a> | <a class="link" href="#user_stats">{tr}User{/tr}</a> | <a class="link" href="#poll_stats">{tr}Polls{/tr}</a> | <a class="link" href="#cms_stats">{tr}CMS{/tr}</a> | <a class="link" href="#blog_stats">{tr}Blogs{/tr}</a> | <a class="link" href="#quiz_stats">{tr}Quizzes{/tr}</a>] 
<!-- Site stats -->
<a name="site_stats"></a>
<table class="normal">
<tr><td class="heading" colspan="2">{tr}Site Stats{/tr}</td></tr>
<tr><td class="even">{tr}Started{/tr}</td><td class="even" style="text-align:right;">{$site_stats.started|tiki_short_date}</td></tr>
<tr><td class="odd">{tr}Days online{/tr}</td><td class="odd" style="text-align:right;">{$site_stats.days}</td></tr>
<tr><td class="even">{tr}Total pageviews{/tr}</td><td class="even" style="text-align:right;">{$site_stats.pageviews}</td></tr>
<tr><td class="odd">{tr}Average pageviews per day{/tr}</td><td class="odd" style="text-align:right;">{$site_stats.ppd|string_format:"%.2f"}</td></tr>
<tr><td class="even">{tr}Best day{/tr}</td><td class="even" style="text-align:right;">{$site_stats.bestday|tiki_short_date} ({$site_stats.bestpvs} {tr}pvs{/tr})</td></tr>
<tr><td class="odd">{tr}Worst day{/tr}</td><td class="odd" style="text-align:right;">{$site_stats.worstday|tiki_short_date} ({$site_stats.worstpvs} {tr}pvs{/tr})</td></tr>
</table>
<br/>
<form action="tiki-stats.php" method="post">
{tr}Show chart for the last {/tr}
<input type="text" name="days" size="10" value="7" /> {tr}days (0=all){/tr}
<input type="submit" name="pv_chart" value="{tr}display{/tr}" />
</form>
<!-- Site stats --> 

{if $pv_chart eq 'y'} 
<br/> 
<div align="center">
<img src="tiki-pv_chart.php?days={$days}" />
</div>
<br/>
{/if}
<br/>

[<a class="link" href="#site_stats">{tr}Site{/tr}</a> | <a class="link" href="#wiki_stats">{tr}Wiki{/tr}</a> | <a class="link" href="#igal_stats">{tr}Image galleries{/tr}</a> | <a class="link" href="#fgal_stats">{tr}File galleries{/tr}</a> | <a class="link" href="#forum_stats">{tr}Forums{/tr}</a> | <a class="link" href="#faq_stats">{tr}FAQs{/tr}</a> | <a class="link" href="#user_stats">{tr}User{/tr}</a> | <a class="link" href="#poll_stats">{tr}Polls{/tr}</a> | <a class="link" href="#cms_stats">{tr}CMS{/tr}</a> | <a class="link" href="#blog_stats">{tr}Blogs{/tr}</a> | <a class="link" href="#quiz_stats">{tr}Quizzes{/tr}</a>] 
<!-- Wiki Stats -->
<a name="wiki_stats"></a>
<table class="normal">
<tr><td class="heading" colspan="2">{tr}Wiki Stats{/tr}</td></tr>
<tr><td class="even">{tr}Wiki Pages{/tr}</td><td class="even" style="text-align:right;">{$wiki_stats.pages}</td></tr>
<tr><td class="odd">{tr}Size of Wiki Pages{/tr}</td><td class="odd" style="text-align:right;">{$wiki_stats.size} {tr}Mb{/tr}</td></tr>
<tr><td class="even">{tr}Average page length{/tr}</td><td class="even" style="text-align:right;">{$wiki_stats.bpp|string_format:"%.2f"} {tr}bytes{/tr}</td></tr>
<tr><td class="odd">{tr}Versions{/tr}</td><td class="odd" style="text-align:right;">{$wiki_stats.versions}</td></tr>
<tr><td class="even">{tr}Average versions per page{/tr}</td><td class="even" style="text-align:right;">{$wiki_stats.vpp|string_format:"%.2f"}</td></tr>
<tr><td class="odd">{tr}Visits to wiki pages{/tr}</td><td class="odd" style="text-align:right;">{$wiki_stats.visits}</td></tr>
<tr><td class="even">{tr}Orphan pages{/tr}</td><td class="even" style="text-align:right;">{$wiki_stats.orphan}</td></tr>
<tr><td class="odd">{tr}Average links per page{/tr}</td><td class="odd" style="text-align:right;">{$wiki_stats.lpp|string_format:"%.2f"}</td></tr>
</table>
<!-- Wiki Stats -->

<br/>

[<a class="link" href="#site_stats">{tr}Site{/tr}</a> | <a class="link" href="#wiki_stats">{tr}Wiki{/tr}</a> | <a class="link" href="#igal_stats">{tr}Image galleries{/tr}</a> | <a class="link" href="#fgal_stats">{tr}File galleries{/tr}</a> | <a class="link" href="#forum_stats">{tr}Forums{/tr}</a> | <a class="link" href="#faq_stats">{tr}FAQs{/tr}</a> | <a class="link" href="#user_stats">{tr}User{/tr}</a> | <a class="link" href="#poll_stats">{tr}Polls{/tr}</a> | <a class="link" href="#cms_stats">{tr}CMS{/tr}</a> | <a class="link" href="#blog_stats">{tr}Blogs{/tr}</a> | <a class="link" href="#quiz_stats">{tr}Quizzes{/tr}</a>] 
<!-- Image gallleries stats -->
<a name="igal_stats"></a>
<table class="normal">
<tr><td class="heading" colspan="2">{tr}Image galleries Stats{/tr}</td></tr>
<tr><td class="even">{tr}Galleries{/tr}</td><td class="even" style="text-align:right;">{$igal_stats.galleries}</td></tr>
<tr><td class="odd">{tr}Images{/tr}</td><td class="odd" style="text-align:right;">{$igal_stats.images}</td></tr>
<tr><td class="even">{tr}Average images per gallery{/tr}</td><td class="even" style="text-align:right;">{$igal_stats.ipg|string_format:"%.2f"}</td></tr>
<tr><td class="odd">{tr}Total size of images{/tr}</td><td class="odd" style="text-align:right;">{$igal_stats.size} {tr}Mb{/tr}</td></tr>
<tr><td class="even">{tr}Average image size{/tr}</td><td class="even" style="text-align:right;">{$igal_stats.bpi|string_format:"%.2f"} {tr}bytes{/tr}</td></tr>
<tr><td class="odd">{tr}Visits to image galleries{/tr}</td><td class="odd" style="text-align:right;">{$igal_stats.visits}</td></tr>
</table>  
<!-- Image gallleries stats -->

<br/>

[<a class="link" href="#site_stats">{tr}Site{/tr}</a> | <a class="link" href="#wiki_stats">{tr}Wiki{/tr}</a> | <a class="link" href="#igal_stats">{tr}Image galleries{/tr}</a> | <a class="link" href="#fgal_stats">{tr}File galleries{/tr}</a> | <a class="link" href="#forum_stats">{tr}Forums{/tr}</a> | <a class="link" href="#faq_stats">{tr}FAQs{/tr}</a> | <a class="link" href="#user_stats">{tr}User{/tr}</a> | <a class="link" href="#poll_stats">{tr}Polls{/tr}</a> | <a class="link" href="#cms_stats">{tr}CMS{/tr}</a> | <a class="link" href="#blog_stats">{tr}Blogs{/tr}</a> | <a class="link" href="#quiz_stats">{tr}Quizzes{/tr}</a>] 
<!-- File gallleries stats -->
<a name="fgal_stats"></a>
<table class="normal">
<tr><td class="heading" colspan="2">{tr}File galleries Stats{/tr}</td></tr>
<tr><td class="even">{tr}Galleries{/tr}</td><td class="even" style="text-align:right;">{$fgal_stats.galleries}</td></tr>
<tr><td class="odd">{tr}Files{/tr}</td><td class="odd" style="text-align:right;">{$fgal_stats.files}</td></tr>
<tr><td class="even">{tr}Average files per gallery{/tr}</td><td class="even" style="text-align:right;">{$fgal_stats.fpg|string_format:"%.2f"}</td></tr>
<tr><td class="odd">{tr}Total size of files{/tr}</td><td class="odd" style="text-align:right;">{$fgal_stats.size} {tr}Mb{/tr}</td></tr>
<tr><td class="even">{tr}Average file size{/tr}</td><td class="even" style="text-align:right;">{$fgal_stats.bpf|string_format:"%.2f"} {tr}Mb{/tr}</td></tr>
<tr><td class="odd">{tr}Visits to file galleries{/tr}</td><td class="odd" style="text-align:right;">{$fgal_stats.visits}</td></tr>
<tr><td class="even">{tr}Downloads{/tr}</td><td class="even" style="text-align:right;">{$fgal_stats.downloads}</td></tr>
</table>
<!-- File gallleries stats -->

<br/>

[<a class="link" href="#site_stats">{tr}Site{/tr}</a> | <a class="link" href="#wiki_stats">{tr}Wiki{/tr}</a> | <a class="link" href="#igal_stats">{tr}Image galleries{/tr}</a> | <a class="link" href="#fgal_stats">{tr}File galleries{/tr}</a> | <a class="link" href="#forum_stats">{tr}Forums{/tr}</a> | <a class="link" href="#faq_stats">{tr}FAQs{/tr}</a> | <a class="link" href="#user_stats">{tr}User{/tr}</a> | <a class="link" href="#poll_stats">{tr}Polls{/tr}</a> | <a class="link" href="#cms_stats">{tr}CMS{/tr}</a> | <a class="link" href="#blog_stats">{tr}Blogs{/tr}</a> | <a class="link" href="#quiz_stats">{tr}Quizzes{/tr}</a>] 
<!-- CMS stats -->
<a name="cms_stats"></a>
<table class="normal">
<tr><td class="heading" colspan="2">{tr}CMS Stats{/tr}</td></tr>
<tr><td class="even">{tr}Articles{/tr}</td><td class="even" style="text-align:right;">{$cms_stats.articles}</td></tr>
<tr><td class="odd">{tr}Total reads{/tr}</td><td class="odd" style="text-align:right;">{$cms_stats.reads}</td></tr>
<tr><td class="even">{tr}Average reads per article{/tr}</td><td class="even" style="text-align:right;">{$cms_stats.rpa|string_format:"%.2f"}</td></tr>
<tr><td class="odd">{tr}Total articles size{/tr}</td><td class="odd" style="text-align:right;">{$cms_stats.size} {tr}bytes{/tr}</td></tr>
<tr><td class="even">{tr}Average article size{/tr}</td><td class="even" style="text-align:right;">{$cms_stats.bpa|string_format:"%.2f"} {tr}bytes{/tr}</td></tr>
<tr><td class="odd">{tr}Topics{/tr}</td><td class="odd" style="text-align:right;">{$cms_stats.topics}</td></tr>
</table>
<!-- CMS stats -->

<br/>

[<a class="link" href="#site_stats">{tr}Site{/tr}</a> | <a class="link" href="#wiki_stats">{tr}Wiki{/tr}</a> | <a class="link" href="#igal_stats">{tr}Image galleries{/tr}</a> | <a class="link" href="#fgal_stats">{tr}File galleries{/tr}</a> | <a class="link" href="#forum_stats">{tr}Forums{/tr}</a> | <a class="link" href="#faq_stats">{tr}FAQs{/tr}</a> | <a class="link" href="#user_stats">{tr}User{/tr}</a> | <a class="link" href="#poll_stats">{tr}Polls{/tr}</a> | <a class="link" href="#cms_stats">{tr}CMS{/tr}</a> | <a class="link" href="#blog_stats">{tr}Blogs{/tr}</a> | <a class="link" href="#quiz_stats">{tr}Quizzes{/tr}</a>] 
<!-- Forum stats -->
<a name="forum_stats"></a>
<table class="normal">
<tr><td class="heading" colspan="2">{tr}Forum Stats{/tr}</td></tr>
<tr><td class="even">{tr}Forums{/tr}</td><td class="even" style="text-align:right;">{$forum_stats.forums}</td></tr>
<tr><td class="odd">{tr}Total topics{/tr}</td><td class="odd" style="text-align:right;">{$forum_stats.topics}</td></tr>
<tr><td class="even">{tr}Average topics per forums{/tr}</td><td class="even" style="text-align:right;">{$forum_stats.tpf|string_format:"%.2f"}</td></tr>
<tr><td class="odd">{tr}Total threads{/tr}</td><td class="odd" style="text-align:right;">{$forum_stats.threads}</td></tr>
<tr><td class="even">{tr}Average threads per topic{/tr}</td><td class="even" style="text-align:right;">{$forum_stats.tpt|string_format:"%.2f"}</td></tr>
<tr><td class="odd">{tr}Visits to forums{/tr}</td><td class="odd" style="text-align:right;">{$forum_stats.visits}</td></tr>
</table>
<!-- Forum stats -->

<br/>

[<a class="link" href="#site_stats">{tr}Site{/tr}</a> | <a class="link" href="#wiki_stats">{tr}Wiki{/tr}</a> | <a class="link" href="#igal_stats">{tr}Image galleries{/tr}</a> | <a class="link" href="#fgal_stats">{tr}File galleries{/tr}</a> | <a class="link" href="#forum_stats">{tr}Forums{/tr}</a> | <a class="link" href="#faq_stats">{tr}FAQs{/tr}</a> | <a class="link" href="#user_stats">{tr}User{/tr}</a> | <a class="link" href="#poll_stats">{tr}Polls{/tr}</a> | <a class="link" href="#cms_stats">{tr}CMS{/tr}</a> | <a class="link" href="#blog_stats">{tr}Blogs{/tr}</a> | <a class="link" href="#quiz_stats">{tr}Quizzes{/tr}</a>] 
<!-- Blogs stats -->
<a name="blog_stats"></a>
<table class="normal">
<tr><td class="heading" colspan="2">{tr}Blog Stats{/tr}</td></tr>
<tr><td class="even">{tr}Weblogs{/tr}</td><td class="even" style="text-align:right;">{$blog_stats.blogs}</td></tr>
<tr><td class="odd">{tr}Total posts{/tr}</td><td class="odd" style="text-align:right;">{$blog_stats.posts}</td></tr>
<tr><td class="even">{tr}Average posts pero weblog{/tr}</td><td class="even" style="text-align:right;">{$blog_stats.ppb|string_format:"%.2f"}</td></tr>
<tr><td class="odd">{tr}Total size of blog posts{/tr}</td><td class="odd" style="text-align:right;">{$blog_stats.size}</td></tr>
<tr><td class="even">{tr}Average posts size{/tr}</td><td class="even" style="text-align:right;">{$blog_stats.bpp|string_format:"%.2f"}</td></tr>
<tr><td class="odd">{tr}Visits to weblogs{/tr}</td><td class="odd" style="text-align:right;">{$blog_stats.visits}</td></tr>
</table>
<!-- Blogs stats -->

<br/>

[<a class="link" href="#site_stats">{tr}Site{/tr}</a> | <a class="link" href="#wiki_stats">{tr}Wiki{/tr}</a> | <a class="link" href="#igal_stats">{tr}Image galleries{/tr}</a> | <a class="link" href="#fgal_stats">{tr}File galleries{/tr}</a> | <a class="link" href="#forum_stats">{tr}Forums{/tr}</a> | <a class="link" href="#faq_stats">{tr}FAQs{/tr}</a> | <a class="link" href="#user_stats">{tr}User{/tr}</a> | <a class="link" href="#poll_stats">{tr}Polls{/tr}</a> | <a class="link" href="#cms_stats">{tr}CMS{/tr}</a> | <a class="link" href="#blog_stats">{tr}Blogs{/tr}</a> | <a class="link" href="#quiz_stats">{tr}Quizzes{/tr}</a>] 
<!-- Poll stats -->
<a name="poll_stats"></a>
<table class="normal">
<tr><td class="heading" colspan="2">{tr}Poll Stats{/tr}</td></tr>
<tr><td class="even">{tr}Polls{/tr}</td><td class="even" style="text-align:right;">{$poll_stats.polls}</td></tr>
<tr><td class="odd">{tr}Total votes{/tr}</td><td class="odd" style="text-align:right;">{$poll_stats.votes}</td></tr>
<tr><td class="even">{tr}Average votes per poll{/tr}</td><td class="even" style="text-align:right;">{$poll_stats.vpp|string_format:"%.2f"}</td></tr>
</table>
<!-- Poll stats -->

<br/>

<!-- FAQ stats -->
[<a class="link" href="#site_stats">{tr}Site{/tr}</a> | <a class="link" href="#wiki_stats">{tr}Wiki{/tr}</a> | <a class="link" href="#igal_stats">{tr}Image galleries{/tr}</a> | <a class="link" href="#fgal_stats">{tr}File galleries{/tr}</a> | <a class="link" href="#forum_stats">{tr}Forums{/tr}</a> | <a class="link" href="#faq_stats">{tr}FAQs{/tr}</a> | <a class="link" href="#user_stats">{tr}User{/tr}</a> | <a class="link" href="#poll_stats">{tr}Polls{/tr}</a> | <a class="link" href="#cms_stats">{tr}CMS{/tr}</a> | <a class="link" href="#blog_stats">{tr}Blogs{/tr}</a> | <a class="link" href="#quiz_stats">{tr}Quizzes{/tr}</a>] 
<a name="faq_stats"></a>
<table class="normal">
<tr><td class="heading" colspan="2">{tr}Faq Stats{/tr}</td></tr>
<tr><td class="even">{tr}FAQs{/tr}</td><td class="even" style="text-align:right;">{$faq_stats.faqs}</td></tr>
<tr><td class="odd">{tr}Total questions{/tr}</td><td class="odd" style="text-align:right;">{$faq_stats.questions}</td></tr>
<tr><td class="even">{tr}Average questions per FAQ{/tr}</td><td class="even" style="text-align:right;">{$faq_stats.qpf|string_format:"%.2f"}</td></tr>
</table>
<!-- FAQ stats -->

<br/>

<!-- Users stats -->
[<a class="link" href="#site_stats">{tr}Site{/tr}</a> | <a class="link" href="#wiki_stats">{tr}Wiki{/tr}</a> | <a class="link" href="#igal_stats">{tr}Image galleries{/tr}</a> | <a class="link" href="#fgal_stats">{tr}File galleries{/tr}</a> | <a class="link" href="#forum_stats">{tr}Forums{/tr}</a> | <a class="link" href="#faq_stats">{tr}FAQs{/tr}</a> | <a class="link" href="#user_stats">{tr}User{/tr}</a> | <a class="link" href="#poll_stats">{tr}Polls{/tr}</a> | <a class="link" href="#cms_stats">{tr}CMS{/tr}</a> | <a class="link" href="#blog_stats">{tr}Blogs{/tr}</a> | <a class="link" href="#quiz_stats">{tr}Quizzes{/tr}</a>] 
<a name="user_stats"></a>
<table class="normal">
<tr><td class="heading" colspan="2">{tr}User Stats{/tr}</td></tr>
<tr><td class="even">{tr}Users{/tr}</td><td class="even" style="text-align:right;">{$user_stats.users}</td></tr>
<tr><td class="odd">{tr}User bookmarks{/tr}</td><td class="odd" style="text-align:right;">{$user_stats.bookmarks}</td></tr>
<tr><td class="even">{tr}Average bookmarks per user{/tr}</td><td class="even" style="text-align:right;">{$user_stats.bpu|string_format:"%.2f"}</td></tr>
</table>
<!-- Usersstats -->

<br/>

<!-- Quiz stats -->
[<a class="link" href="#site_stats">{tr}Site{/tr}</a> | <a class="link" href="#wiki_stats">{tr}Wiki{/tr}</a> | <a class="link" href="#igal_stats">{tr}Image galleries{/tr}</a> | <a class="link" href="#fgal_stats">{tr}File galleries{/tr}</a> | <a class="link" href="#forum_stats">{tr}Forums{/tr}</a> | <a class="link" href="#faq_stats">{tr}FAQs{/tr}</a> | <a class="link" href="#user_stats">{tr}User{/tr}</a> | <a class="link" href="#poll_stats">{tr}Polls{/tr}</a> | <a class="link" href="#cms_stats">{tr}CMS{/tr}</a> | <a class="link" href="#blog_stats">{tr}Blogs{/tr}</a> | <a class="link" href="#quiz_stats">{tr}Quizzes{/tr}</a>] 
<a name="quiz_stats"></a>
<table class="normal">
<tr><td class="heading" colspan="2">{tr}Quiz Stats{/tr}</td></tr>
<tr><td class="even">{tr}Quizzes{/tr}</td><td class="even" style="text-align:right;">{$quiz_stats.quizzes}</td></tr>
<tr><td class="odd">{tr}Questions{/tr}</td><td class="odd" style="text-align:right;">{$quiz_stats.questions}</td></tr>
<tr><td class="even">{tr}Average questions per quiz{/tr}</td><td class="even" style="text-align:right;">{$quiz_stats.qpq|string_format:"%.2f"}</td></tr>
<tr><td class="odd">{tr}Quizzes taken{/tr}</td><td class="odd" style="text-align:right;">{$quiz_stats.visits}</td></tr>
<tr><td class="even">{tr}Average quiz score{/tr}</td><td class="even" style="text-align:right;">{$quiz_stats.avg|string_format:"%.2f"}</td></tr>
<tr><td class="odd">{tr}Average time per quiz{/tr}</td><td class="odd" style="text-align:right;">{$quiz_stats.avgtime|string_format:"%.2f"} {tr}secs{/tr}</td></tr>
</table>
<!-- Quiz stats -->


<br/>
<br/>
<br/>
