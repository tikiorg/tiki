<table class="normal">
  <form method="post" action="tiki-index.php?page={$page}">
  <tr>
    <td class="heading">Instance</td>
{assign var="colspan" value=1}
{foreach from=$candidates item=c}
    <td class="heading">{$c.label}</td>
  {assign var="colspan" value=$colspan+1}
{/foreach}
  </tr>

{cycle values="odd,even" print=false}
{foreach from=$instances item=inst}
{*include file="wikiplugin_galaxiamultiroute_commentwindow.tpl"*}


  <script language="JavaScript">
    {assign var=instance value=$inst.properties}
    {eval var=$instance_template assign="instance_data"}
    {*include file="wikiplugin_galaxiamultiroute_instance.tpl" assign="instance_data" instance=$inst.properties*}
    var instance_{$inst.instanceId} = '{$instance_data|escape:javascript}';
  </script>
  <tr>
    <td class="{cycle advance=false}"><a onmouseover="return overlib(instance_{$inst.instanceId}, VAUTO, HAUTO, CAPTION, '{$inst.label|escape:"javascript"}');" onmouseout="nd();" onmousedown="toggle('commentconsole_{$inst.instanceId}');"{if $inst.url} href="{$inst.url}"{/if}>{$inst.label}</a>
    </td>
{foreach from=$candidates item=c}
  {if $type eq 'switch'}  
    <td class="{cycle advance=false}"><input onmouseover="return overlib(instance_{$inst.instanceId}, VAUTO, HAUTO, CAPTION, '{$inst.label|escape:"javascript"}');" onmouseout="nd();" type="radio" name="route_{$inst.instanceId}" value="{$c.value}"></td>
  {else}
    <td class="{cycle advance=false}"><input onmouseover="return overlib(instance_{$inst.instanceId}, VAUTO, HAUTO, CAPTION, '{$inst.label|escape:"javascript"}');" onmouseout="nd();" type="checkbox" name="route_{$inst.instanceId}" value="{$c.value}"></td>
  {/if}
{/foreach}
  </tr>
  {cycle print=false advance=true}
{/foreach}
  <tr>
    <td class="{cycle}" colspan={$colspan}>
      <input type="submit" name="route" value="Submit">
    </td>
  </tr>
  </form>
</table>

