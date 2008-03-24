<h2>{tr}My activities{/tr}</h2>
<br />
<table class="normal">
  <tr>
    <td class="heading">{tr}Process{/tr}</td>
    <td class="heading">{tr}Start{/tr}</td>
    <td class="heading">{tr}Status{/tr}</td>
    <td class="heading">{tr}Current Activity{/tr}</td>
    <td class="heading">{tr}Assigned To{/tr}</td>
    <td class="heading">{tr}Action{/tr}</td>
  </tr>
{cycle values="odd,even" print=false}
  {foreach from=$processes item=process}
    <tr>
      <td class="{cycle advance=false}">{$process.procname} {$process.version}</td>
      <td class="{cycle advance=false}">{$process.started|tiki_long_datetime}</td>
      <td class="{cycle advance=false}">
        {if $process.status eq 'active'}
          <span style="color:green;">{$process.status}</span>
        {elseif $process.status eq 'completed'}
          <span style="color:black;">{$process.status}</span>
        {elseif $process.status eq 'aborted'}
          <span style="color:grey;">{$process.status}</span>
        {elseif $process.status eq 'exception'}
          <span style="color:red;">{$process.status}</span>
        {else}
          {$process.status}
        {/if}
      </td>
      <td class="{cycle advance=false}">{$process.type|act_icon:$process.isInteractive} {$process.name}</td>
      <td class="{cycle advance=false}">{$process.user}</td>
      <td class="{cycle}">
        {*actions*}
        <table>
          <tr>
            {*exception*}
            {if $tiki_p_exception_instance eq 'y'}
              {if $process.status ne 'aborted' and $process.status ne 'exception' and $process.user eq $user}
                <a onclick="javascript:return confirm('Are you sure you want to exception this instance?');" href="tiki-g-user_instances.php?abort=1&amp;iid={$process.instanceId}&amp;aid={$process.activityId}"><img border='0' title='{tr}exception instance{/tr}' alt='{tr}exceptions instance{/tr}' src='lib/Galaxia/img/icons/stop.gif' /></a>
              {else}
                <img border='0' src='lib/Galaxia/img/icons/trdot.gif' width="16" />
              {/if}
            {/if}
            {if $process.isAutoRouted eq 'n' and $process.actstatus eq 'completed'}
              {*send*}
              <a href="tiki-g-user_instances.php?send=1&amp;iid={$process.instanceId}&amp;aid={$process.activityId}"><img border='0' title='{tr}Send Instance{/tr}' alt='{tr}Send Instance{/tr}' src='lib/Galaxia/img/icons/linkto.gif' /></a>
            {else}
              <img border='0' src='lib/Galaxia/img/icons/trdot.gif' width="16" />
            {/if}
            {if $process.isInteractive eq 'y' and $process.status eq 'active'}
              {*run*}
              <a href="tiki-g-run_activity.php?iid={$process.instanceId}&amp;activityId={$process.activityId}"><img border='0' title='{tr}run instance{/tr}' alt='{tr}run instance{/tr}' src='lib/Galaxia/img/icons/next.gif' /></a>
            {else}
              <img border='0' src='lib/Galaxia/img/icons/trdot.gif' width="16" />
            {/if}
            {*abort*}
            {if $tiki_p_abort_instance eq 'y'}
              {if $process.status ne 'aborted' and $process.user eq $user}
                <a onclick="javascript:return confirm('Are you sure you want to abort this instance?');" href="tiki-g-user_instances.php?abort=1&amp;iid={$process.instanceId}&amp;aid={$process.activityId}"><img border='0' title='{tr}abort instance{/tr}' alt='{tr}abort instance{/tr}' src='lib/Galaxia/img/icons/trash.gif' /></a>
              {else}
                <img border='0' src='lib/Galaxia/img/icons/trdot.gif' width="16" />
              {/if}
            {/if}
            {if $process.user eq '*' and $process.status eq 'active'}
              {*grab*}
              <a href="tiki-g-user_instances.php?grab=1&amp;iid={$process.instanceId}&amp;aid={$process.activityId}"><img border='0' title='{tr}grab instance{/tr}' alt='{tr}grab instance{/tr}' src='lib/Galaxia/img/icons/fix.gif' /></a>
            {else}
              {*release*}
              {if $process.status eq 'active'}
                <a href="tiki-g-user_instances.php?release=1&amp;iid={$process.instanceId}&amp;aid={$process.activityId}"><img border='0' title='{tr}release instance{/tr}' alt='{tr}release instance{/tr}' src='lib/Galaxia/img/icons/float.gif' /></a>
              {/if}
            {/if}
          </tr>
        </table>
      </td>
    </tr>
  {foreachelse}
    <td class="{cycle advance=false}" colspan="6">{tr}No activities found{/tr}</td>
  {/foreach}
</table>
