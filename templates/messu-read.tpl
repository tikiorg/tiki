<a class="pagetitle" href="messu-read.php?msgId={$msgId}">{tr}Read message{/tr}</a><br/><br/>
{include file="messu-nav.tpl"}
<br/>
{if $prev}<a class="readlink" href="messu-read.php?msgId={$prev}&amp;sort_mode={$sort_mode}&amp;find={$find}&amp;flag={$flag}&amp;priority={$priority}&amp;flagval={$flagval}">Prev</a>{/if} 
{if $next}<a class="readlink" href="messu-read.php?msgId={$next}&amp;sort_mode={$sort_mode}&amp;find={$find}&amp;flag={$flag}&amp;priority={$priority}&amp;flagval={$flagval}">Next</a>{/if} 
<a class="readlink" href="messu-mailbox.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;find={$find}&amp;flag={$flag}&amp;priority={$priority}&amp;flagval={$flagval}">Return to messages</a>
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
    <input type="submit" name="delete" value="delete" />
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
    <input type="submit" name="reply" value="reply" />
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
    <input type="submit" name="replyall" value="replyall" />
  </td></tr>
  </table>
  <div class="messureadflag">
  {if $msg.isFlagged eq 'y'}
  <img alt="flag" src="img/flagged.gif" /><a class="link" href="messu-read.php?action=isFlagged&amp;actionval=n&amp;msgId={$msgId}&amp;sort_mode={$sort_mode}&amp;find={$find}&amp;flag={$flag}&amp;priority={$priority}&amp;flagval={$flagval}">Unflagg</a>
  {else}
  <a class="link" href="messu-read.php?action=isFlagged&amp;actionval=y&amp;msgId={$msgId}&amp;sort_mode={$sort_mode}&amp;find={$find}&amp;flag={$flag}&amp;priority={$priority}&amp;flagval={$flagval}">Flag this message</a>
  {/if}
  </div>
  <div class="messureadhead">
  <table>
    <tr><td style="font-weight:bold;">From:</td><td>{$msg.user_from}</td></tr>
    <tr><td style="font-weight:bold;">To:</td><td>{$msg.user_to}</td></tr>
    <tr><td style="font-weight:bold;">Cc:</td><td>{$msg.user_cc}</td></tr>
    <tr><td style="font-weight:bold;">Subject:</td><td>{$msg.subject}</td></tr>
    <tr><td style="font-weight:bold;">Date:<td>{$msg.date|date_format:"%a %b %Y [%H:%I]"}</td></tr>
  </table>
  </div>
  <div class="messureadbody">
  {$msg.body|nl2br}
  </div>
{/if}