{* $Header: /cvsroot/tikiwiki/tiki/templates/tiki-tell_a_friend.tpl,v 1.1 2007-03-09 19:01:21 sylvieg Exp $ *}
<h1>{tr}Send a link to a friend{/tr}</h1>
<span class="button2"><a href="{$url}" class="linkbut">{tr}Back{/tr}</a></span>
{if !empty($sent)}
<div class="simplebox highlight">
{tr}The link was sent to the following addresses:{/tr}<br />
{$sent|escape}
</div>
{/if}

{if !empty($errors)}
<div class="simplebox highlight">
{tr}One of the email addresses you typed is invalid{/tr}<br />
{foreach from=$errors item=m name=errors}
{$m}
{if !$smarty.foreach.errors.last}<br />{/if}
{/foreach}
</div>
{/if}

<form method="post" action="tiki-tell_a_friend.php">
<input type="hidden" name="url" value="{$url|escape:url}" />
<table class="normal">
<tr>
<td class="formcolor">{tr}Link{/tr}</td>
<td class="formcolor">{$prefix}{$url}</td>
</tr>
<tr>
<td class="formcolor">{tr}List of email addresses separated by commas{/tr}</td>
<td class="formcolor"><input type="text" size="60" name="addresses" value="{$addresses|escape}"/></td>
</tr>
<tr>
<td class="formcolor">{tr}Your name{/tr}</td>
<td class="formcolor"><input type="text" name="name" value="{$name}" /></td>
</tr>
<tr>
<td class="formcolor">{tr}Your comment{/tr}</td>
<td class="formcolor"><textarea name="comment" cols="60">{$comment|escape}</textarea></td>
</tr>
<tr>
<td class="formcolor"></td>
<td class="formcolor"><input type="submit" name="send" value="{tr}send{/tr}" /></td>
</tr>
</table>
</form>
