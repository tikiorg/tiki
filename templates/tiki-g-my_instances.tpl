<h2>{tr}My instances{/tr}</h2>
<br />
<table class="normal">
  <tr>
    <td class="heading">{tr}Process{/tr}</td>
    <td class="heading">{tr}Start{/tr}</td>
    <td class="heading">{tr}Owner{/tr}</td>
    <td class="heading">{tr}Current Activity{/tr}</td>
    <td class="heading">{tr}Assigned To{/tr}</td>
    <td class="heading">{tr}Action{/tr}</td>
  </tr>
{cycle values="odd,even" print=false}
  {foreach from=$instances item=instance}
    <tr>
      <td class="{cycle advance=false}">{$instance.procname} {$instance.version}</td>
      <td class="{cycle advance=false}">{$instance.started|tiki_long_datetime}</td>
      <td class="{cycle advance=false}">{$instance.owner}</td>
      <td class="{cycle advance=false}">{$instance.type|act_icon:$instance.isInteractive} {$instance.name}</td>
      <td class="{cycle advance=false}">{$instance.user}</td>
      <td class="{cycle}">
        {*actions*}
        <table>
          <tr>
            {*exception*}
            {if $tiki_p_exception_instance eq 'y'}
              {if $instance.status ne 'aborted' and $instance.status ne 'exception' and $instance.user eq $user}
                <td><a onclick="javascript:return confirm('Are you sure you want to exception this instance?');" href="tiki-g-user_instances.php?exception=1&amp;iid={$instance.instanceId}&amp;aid={$instance.activityId}"><img border='0' title='{tr}exception instance{/tr}' alt='{tr}exceptions instance{/tr}' src='lib/Galaxia/img/icons/stop.gif' /></a></td>
              {else}
                <td><img border='0' src='lib/Galaxia/img/icons/trdot.gif' width="16" /></td>
              {/if}
            {/if}
            {if $instance.isAutoRouted eq 'n' and $instance.actstatus eq 'completed'}
              {*send*}
              <td><a href="tiki-g-user_instances.php?send=1&amp;iid={$instance.instanceId}&amp;aid={$instance.activityId}"><img border='0' title='{tr}Send Instance{/tr}' alt='{tr}Send Instance{/tr}' src='lib/Galaxia/img/icons/linkto.gif' /></a></td>
            {else}
              <td><img border='0' src='lib/Galaxia/img/icons/trdot.gif' width="16" /></td>
            {/if}
            {if $instance.isInteractive eq 'y' and $instance.status eq 'active'}
             {*run*}
              <td><a href="tiki-g-run_activity.php?iid={$instance.instanceId}&amp;activityId={$instance.activityId}"><img border='0' title='{tr}run instance{/tr}' alt='{tr}run instance{/tr}' src='lib/Galaxia/img/icons/next.gif' /></a></td>
            {else}
              <td><img border='0' src='lib/Galaxia/img/icons/trdot.gif' width="16" /></td>
            {/if}
            {*abort*}
            {if $tiki_p_abort_instance eq 'y'}
              {if $instance.status ne 'aborted' and $instance.user eq $user}
                <td><a onclick="javascript:return confirm('Are you sure you want to abort this instance?');" href="tiki-g-user_instances.php?abort=1&amp;iid={$instance.instanceId}&amp;aid={$instance.activityId}"><img border='0' title='{tr}abort instance{/tr}' alt='{tr}abort instance{/tr}' src='lib/Galaxia/img/icons/trash.gif' /></a></td>
              {else}
                <td><img border='0' src='lib/Galaxia/img/icons/trdot.gif' width="16" /></td>
              {/if}
            {/if}
            {if $instance.user eq '*' and $instance.status eq 'active'}
              {*grab*}
              <td><a href="tiki-g-user_instances.php?grab=1&amp;iid={$instance.instanceId}&amp;aid={$instance.activityId}"><img border='0' title='{tr}grab instance{/tr}' alt='{tr}grab instance{/tr}' src='lib/Galaxia/img/icons/fix.gif' /></a></td>
            {else}
              {*release*}
              {if $instance.status eq 'active'}
                <td><a href="tiki-g-user_instances.php?release=1&amp;iid={$instance.instanceId}&amp;aid={$instance.activityId}"><img border='0' title='{tr}release instance{/tr}' alt='{tr}release instance{/tr}' src='lib/Galaxia/img/icons/float.gif' /></a></td>
              {/if}
            {/if}
          </tr>
        </table>
      </td>
    </tr>
  {foreachelse}
    <tr>
      <td class="{cycle advance=false}" colspan="6">{tr}No instances found{/tr}</td>
    </tr>	
  {/foreach}
</table>
