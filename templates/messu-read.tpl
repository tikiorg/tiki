<a class="pagetitle" href="messu-read.php?msgId={$msgId}">{tr}Read message{/tr}</a><br/><br/>
{include file=tiki-mytiki_bar.tpl}
{include file="messu-nav.tpl"}
<br/>
{if $prev}<a class="readlink" href="messu-read.php?offset={$offset}&amp;msgId={$prev}&amp;sort_mode={$sort_mode}&amp;find={$find}&amp;flag={$flag}&amp;priority={$priority}&amp;flagval={$flagval}">{tr}Prev{/tr}</a>{/if} 
{if $next}<a class="readlink" href="messu-read.php?offset={$offset}&amp;msgId={$next}&amp;sort_mode={$sort_mode}&amp;find={$find}&amp;flag={$flag}&amp;priority={$priority}&amp;flagval={$flagval}">{tr}Next{/tr}</a>{/if} 
<a class="readlink" href="messu-mailbox.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;find={$find}&amp;flag={$flag}&amp;priority={$priority}&amp;flagval={$flagval}">{tr}Return to messages{/tr}</a>
<br/><br/>
{if $legend}
  {$legend}
{else}
  <table>
  <tr><td>
    <form method="post" action="messu-read.php">
    <input type="hidden" name="offset" value="{$offset}" />
    <input type="hidden" name="find" value="{$find}" />
    <input type="hidden" name="sort_mode" value="{$sort_mode}" />
    <input type="hidden" name="flag" value="{$flag}" />
    <input type="hidden" name="flagval" value="{$flagval}" />
    <input type="hidden" name="priority" value="{$priority}" />
    <input type="hidden" name="msgdel" value="{$msgId}" />
    {if $next}
    <input type="hidden" name="msgId" value="{$next}" />
    {elseif $prev}
    <input type="hidden" name="msgId" value="{$prev}" />
    {else}
    <input type="hidden" name="msgId" value="" />
    {/if}
    <input type="submit" name="delete" value="{tr}delete{/tr}" />
    </form>
  </td>
  <td>
    <form method="post" action="messu-compose.php">
    <input type="hidden" name="offset" value="{$offset}" />
    <input type="hidden" name="msgId" value="{$msgId}" />
    <input type="hidden" name="find" value="{$find}" />
    <input type="hidden" name="sort_mode" value="{$sort_mode}" />
    <input type="hidden" name="flag" value="{$flag}" />
    <input type="hidden" name="priority" value="{$priority}" />
    <input type="hidden" name="flagval" value="{$flagval}" />
    <input type="hidden" name="to" value="{$msg.user_from}" />
    <input type="hidden" name="subject" value="Re:{$msg.subject}" />
    <input type="hidden" name="body" value="{$msg.body|quoted}" />
    <input type="submit" name="reply" value="{tr}reply{/tr}" />
    </form>
  </td>
  <td>
    <form method="post" action="messu-compose.php">
    <input type="hidden" name="offset" value="{$offset}" />
    <input type="hidden" name="find" value="{$find}" />
    <input type="hidden" name="msgId" value="{$msgId}" />
    <input type="hidden" name="sort_mode" value="{$sort_mode}" />
    <input type="hidden" name="flag" value="{$flag}" />
    <input type="hidden" name="priority" value="{$priority}" />
    <input type="hidden" name="flagval" value="{$flagval}" />
    <input type="hidden" name="to" value="{$msg.user_from},{$msg.user_cc},{$msg.user_to}" />
    <input type="hidden" name="subject" value="Re:{$msg.subject}" />
    <input type="hidden" name="body" value="{$msg.body|quoted}" />
    <input type="submit" name="replyall" value="{tr}replyall{/tr}" />
  </td></tr>
  </table>
  <div class="messureadflag">
  {if $msg.isFlagged eq 'y'}
  <img alt="flag" src="img/flagged.gif" /><a class="link" href="messu-read.php?offset={$offset}&amp;action=isFlagged&amp;actionval=n&amp;msgId={$msgId}&amp;sort_mode={$sort_mode}&amp;find={$find}&amp;flag={$flag}&amp;priority={$priority}&amp;flagval={$flagval}">{tr}Unflagg{/tr}</a>
  {else}
  <a class="link" href="messu-read.php?offset={$offset}&amp;action=isFlagged&amp;actionval=y&amp;msgId={$msgId}&amp;sort_mode={$sort_mode}&amp;find={$find}&amp;flag={$flag}&amp;priority={$priority}&amp;flagval={$flagval}">{tr}Flag this message{/tr}</a>
  {/if}
  </div>
  <div class="messureadhead">
  <table>
    <tr><td style="font-weight:bold;">{tr}From{/tr}:</td><td>{$msg.user_from}</td></tr>
    <tr><td style="font-weight:bold;">{tr}To{/tr}:</td><td>{$msg.user_to}</td></tr>
    <tr><td style="font-weight:bold;">{tr}Cc{/tr}:</td><td>{$msg.user_cc}</td></tr>
    <tr><td style="font-weight:bold;">{tr}Subject{/tr}:</td><td>{$msg.subject}</td></tr>
    <tr><td style="font-weight:bold;">{tr}Date{/tr}:<td>{$msg.date|tiki_short_datetime}</td></tr><!--date_format:"%a %b %Y [%H:%I]"-->
  </table>
  </div>
  <div class="messureadbody">
  {$msg.parsed}
  </div>
{/if}