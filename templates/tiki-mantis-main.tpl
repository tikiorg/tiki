<a class="pagetitle" href="tiki-mantis-main.php">{tr}Mantis Bug Tracker{/tr}</a>
{include file=tiki-mantis-current_project.tpl}
<br /><br />
<h3>{tr}Open and assigned to me{/tr}:&nbsp;
<a href="tiki-mantis-view_all_set.php?type=1&amp;reporter_id=any&amp;
show_status=any&amp;show_severity=any&amp;show_category=any&amp;
handler_id={$user}&amp;hide_closed=on&amp;hide_resolved=on">
{$assignedOpenBugCount}</a></h3>

<h3>{tr}Open and reported to me{/tr}:&nbsp;
<a href="tiki-mantis-view_all_set.php?type=1&amp;reporter_id={$user}&amp;
show_status=any&amp;show_severity=any&amp;show_category=any&amp;
handler_id=any&amp;hide_closed=on&amp;hide_resolved=on">
{$reportedOpenBugCount}</a></h3>
<br/>

