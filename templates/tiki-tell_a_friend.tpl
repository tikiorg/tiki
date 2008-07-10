{* $Id$ *}
<h1>{if $report eq 'y'}{tr}Report to Webmaster{/tr}{else}{tr}Send a link to a friend{/tr}{/if}</h1>
<span class="button2"><a href="{$url}" class="linkbut">{tr}Back{/tr}</a></span>
{if isset($sent)}
<div class="simplebox highlight">{icon _id=accept alt="{tr}OK{/tr}" style="vertical-align:middle" align="left"} 
{if $report eq 'y'}
{tr}Your email was sent{/tr}.
{else}
{tr}The link was sent to the following addresses:{/tr}<br />
{$sent|escape}
{/if}
</div>
{/if}

{if !empty($errors)}
<div class="simplebox highlight">
{icon _id=exclamation alt="{tr}Error{/tr}" style="vertical-align:middle" align="left"} 
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
    
	{if $report ne 'y'}
    <tr class="formcolor">
      <td class="formcolor">{tr}Friend's email{/tr}</td>
      <td class="formcolor"><input style="width:95%;" type="text" size="60" name="addresses" value="{$addresses|escape}"/>
	  <br /><em>{tr}Separate multiple email addresses with a comma.</em>{/tr}
	  </td>
    </tr>
    {else}
      <input type="hidden" name="report" value="y" />
	{/if}
  
    <tr class="formcolor">
      <td class="formcolor">{tr}Your name{/tr}</td>
      <td class="formcolor"><input style="width:95%;" type="text" name="name" value="{$name}" /></td>
    </tr>

    <tr class="formcolor">
      <td class="formcolor">{tr}Your email{/tr}</td>
      <td class="formcolor"><input style="width:95%;" type="text" name="email" value="{$email}" /></td>
    </tr>

    <tr class="formcolor">
      <td class="formcolor">
        {tr}Your comment{/tr}
        <br /><br />
        {include file="textareasize.tpl" area_name='comment' formId='tellafriend'}
      </td>
      
      <td class="formcolor">
        <textarea name="comment" style="width:95%;" rows="10" cols='{$cols}' id='comment'>{$comment|escape|@default:'{tr}I found an interesting page that I thought you would like.{/tr}'}</textarea>
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
