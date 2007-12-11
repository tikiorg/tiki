<div style="padding-bottom:3px; padding-left:4px; padding-top:1px; margin-top:20px; background-color:white; border:1px solid black;">
<small>{$stats.processes} {tr}processes{/tr} ({$stats.active_processes} {tr}active{/tr}) ({$stats.running_processes} {tr}being run{/tr})</small>
<br />
<small>{tr}Instances{/tr}: [<a style="color:green;" href="tiki-g-monitor_instances.php?filter_status=active">{$stats.active_instances} {tr}active{/tr}</a>]</small>
<small>[<a style="color:black;" href="tiki-g-monitor_instances.php?filter_status=completed">{$stats.completed_instances} {tr}Completed{/tr}</a>]</small>
<small>[<a style="color:grey;" href="tiki-g-monitor_instances.php?filter_status=aborted">{$stats.aborted_instances} {tr}aborted{/tr}</a>]</small>
<small>[<a style="color:red;" href="tiki-g-monitor_instances.php?filter_status=exception">{$stats.exception_instances} {tr}exceptions{/tr}</a>]</small>
</div>