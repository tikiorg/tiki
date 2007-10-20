{* $Header: /cvsroot/tikiwiki/tiki/templates/tiki-tell_a_friend.tpl,v 1.4.2.1 2007-10-20 21:45:16 pkdille Exp $ *}
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

<form method="post" action="tiki-tell_a_friend.php" id="tellafriend">
  <input type="hidden" name="url" value="{$url|escape:url}" />
  <table class="normal">
    <tr class="formcolor">
      <td>{tr}Link{/tr}</td>
      <td><a href={$prefix}{$url}>{$prefix}{$url}</a></td>
    </tr>
    
    <tr class="formcolor">
      <td class="formcolor">{tr}List of email addresses separated by commas{/tr}</td>
      <td class="formcolor"><input type="text" size="60" name="addresses" value="{$addresses|escape}"/></td>
    </tr>
  
    <tr class="formcolor">
      <td class="formcolor">{tr}Your name{/tr}</td>
      <td class="formcolor"><input type="text" name="name" value="{$name}" /></td>
    </tr>

    <tr class="formcolor">
      <td class="formcolor">
        {tr}Your comment{/tr}
        <br /><br />
        {include file="textareasize.tpl" area_name='comment' formId='tellafriend'}
      </td>
      
      <td class="formcolor">
        <textarea name="comment" rows="10" cols='{$cols}' id='comment'>{$comment|escape}</textarea>
      </td>
    </tr>
    
    <tr>
      <td class="formcolor"></td>
      <td class="formcolor"><input type="submit" name="send" value="{tr}Send{/tr}" /></td>
    </tr>
  </table>
</form>
