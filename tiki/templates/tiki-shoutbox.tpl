<a class="pagetitle" href="tiki-shoutbox.php">{tr}Tiki ShoutBox{/tr}!</a><br/>
{if $tiki_p_post_shoutbox eq 'y'}
<h2>{tr}Post or edit a message{/tr}</h2>
<form action="tiki-shoutbox.php" method="post">
<input type="hidden" name="msgId" value="{$msgId}" />
<table class="normal">
<tr><td class="formcolor">{tr}message{/tr}:</td><td class="formcolor"><textarea rows="4" cols="60" name="message" maxlength="250">{$message}</textarea></td></tr>
<tr><td  class="formcolor">&nbsp;</td><td class="formcolor"><input type="submit" name="save" value="{tr}Save{/tr}" /></td></tr>
</table>
</form>
{/if}


<h2>{tr}Messages{/tr}</h2>
<table class="findtable">
<tr><td class="findtable">{tr}Find{/tr}</td>
   <td class="findtable">
   <form method="get" action="tiki-shoutbox.php">
     <input type="text" name="find" value="{$find}" />
     <input type="submit" value="{tr}find{/tr}" name="search" />
     <input type="hidden" name="sort_mode" value="{$sort_mode}" />
   </form>
   </td>
</tr>
</table>


{section name=user loop=$channels}
<div class="shoutboxmsg">
<b>{$channels[user].user}</b> {tr}at{/tr}: {$channels[user].timestamp|tiki_long_time}
{if $tiki_p_admin_shoutbox eq 'y'}
[<a href="tiki-shoutbox.php?find={$find}&amp;offset={$offset}&amp;sort_mode={$sort_mode}&amp;remove={$channels[user].msgId}" class="link">x</a>
|<a href="tiki-shoutbox.php?find={$find}&amp;offset={$offset}&amp;sort_mode={$sort_mode}&amp;msgId={$channels[user].msgId}" class="link">edit</a>]
{/if}
<br/>
{$channels[user].message}
</div>
{/section}

<div align="center">
<div class="mini">
{if $prev_offset >= 0}
[<a class="prevnext" href="tiki-shoutbox.php?find={$find}&amp;offset={$prev_offset}&amp;sort_mode={$sort_mode}">{tr}prev{/tr}</a>]&nbsp;
{/if}
{tr}Page{/tr}: {$actual_page}/{$cant_pages}
{if $next_offset >= 0}
&nbsp;[<a class="prevnext" href="tiki-shoutbox.php?find={$find}&amp;offset={$next_offset}&amp;sort_mode={$sort_mode}">{tr}next{/tr}</a>]
{/if}
{if $direct_pagination eq 'y'}
<br/>
{section loop=$cant_pages name=foo}
{assign var=selector_offset value=$smarty.section.foo.index|times:$maxRecords}
<a class="prevnext" href="tiki-shoutbox.php?find={$find}&amp;offset={$selector_offset}&amp;sort_mode={$sort_mode}">
{$smarty.section.foo.index_next}</a>&nbsp;
{/section}
{/if}

</div>
</div>

