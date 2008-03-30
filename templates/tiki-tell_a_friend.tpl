{* $Id$ *}
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
      <td class="formcolor">{tr}Your email{/tr}</td>
      <td class="formcolor"><input type="text" name="email" value="{$email}" /></td>
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
	{if $prefs.feature_antibot eq 'y' && $user eq ''}
		{include file="antibot.tpl"}
	{/if}

    
    <tr>
      <td class="formcolor"></td>
      <td class="formcolor"><input type="submit" name="send" value="{tr}Send{/tr}" /></td>
    </tr>
  </table>
</form>
