{*
@author: Javier Reyes Gomez (jreyes@escire.com)
@date: 27/01/2006
@copyright (C) 2006 Javier Reyes Gomez (eScire.com)
@license http://www.gnu.org/copyleft/lgpl.html GNU/LGPL
*}
<div class="blogtitle">{tr}Blog{/tr}: {$title}</div>
<div class="bloginfo">
{tr}Created by{/tr} {$creator}{tr} on {/tr}{$created|tiki_short_datetime}<br />
{tr}Last modified{/tr} {$lastModif|tiki_short_datetime}<br /><br />
<table><tr><td>
({$posts} {tr}posts{/tr} | {$hits} {tr}visits{/tr} | {tr}Activity={/tr}{$activity|string_format:"%.2f"})</td>
<td style="text-align:right;">
{if $tiki_p_blog_post eq "y"}
{if ($user and $creator eq $user) or $tiki_p_blog_admin eq "y" or $public eq "y"}
<a class="bloglink" href="tiki-blog_post.php?blogId={$blogId}"><img src="img/icons/edit.gif" border="0" alt="{tr}Post{/tr}" title="{tr}post{/tr}" /></a>{/if}{/if}
{if $rss_blog eq "y"}
<a class="bloglink" href="tiki-blog_rss.php?blogId={$blogId}"><img src="img/icons/mode_desc.gif" border="0" alt="{tr}RSS feed{/tr}" title="{tr}RSS feed{/tr}" /></a>{/if}
{if ($user and $creator eq $user) or $tiki_p_blog_admin eq "y"}
<a class="bloglink" href="tiki-edit_blog.php?blogId={$blogId}"><img src="img/icons/config.gif" border="0" alt="{tr}Edit blog{/tr}" title="{tr}Edit blog{/tr}" /></a>{/if}
{if $user and $feature_user_watches eq "y"}
{if $user_watching_blog eq "n"}
<a href="tiki-view_blog.php?blogId={$blogId}&amp;watch_event=blog_post&amp;watch_object={$blogId}&amp;watch_action=add"><img border="0" alt="{tr}monitor this blog{/tr}" title="{tr}monitor this blog{/tr}" src="img/icons/icon_watch.png" /></a>
{else}<a href="tiki-view_blog.php?blogId={$blogId}&amp;watch_event=blog_post&amp;watch_object={$blogId}&amp;watch_action=remove"><img border="0" alt="{tr}stop monitoring this blog{/tr}" title="{tr}stop monitoring this blog{/tr}" src="img/icons/icon_unwatch.png" /></a>
{/if}{/if}</td></tr></table></div>
<div class="blogdesc">{tr}Description:{/tr} {$description}</div>