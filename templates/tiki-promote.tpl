{* $Id: $ *}
{title}    {tr}Promote this page{/tr}{/title}

{if isset($sent)}
<div class="simplebox highlight">{icon _id=accept alt="{tr}OK{/tr}" style="vertical-align:middle" align="left"}
{if isset($emailSent)} 
{tr}The link was sent to the following addresses:{/tr}<br />
{$sent|escape}
{/if}
{if isset($tweetId)}
<div><a href="http://www.twitter.com/"><img src="img/icons/twitter_t_logo_32.png" border="0" /> </a>The link was sent via Twitter</div>
{/if}
{if isset($facebookId)}
<div><img src="img/icons/facebook_logo_32.png" border="0" /> </a>The link was posted on your facebook wall</div>
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

<form method="post" action="tiki-promote.php" id="promote-form">
  <input type="hidden" name="url" value="{$url|escape:url}" />
  <table class="normal">
    <tr class="formcolor">
      <td>{tr}Link{/tr}</td>
      <td><a href="{$prefix}{$url}">{$prefix}{$url}</a></td>
    </tr>
    <tr class="formcolor">
      <td>{tr}Short link{/tr}</td>
      <td>{$shorturl}</td>
    </tr>
    
    <tr class="formcolor">
      <td class="formcolor">{tr}Subject{/tr}</td>
      <td class="formcolor"><input style="width:95%;" type="text" name="subject" value="{$subject|escape|default:"{tr}Have a look at this page{/tr}"}" /></div></td>
    </tr>

    <tr class="formcolor">
      <td class="formcolor">
        {tr}Your message{/tr}
        <br /><br />
        {include file='textareasize.tpl' area_name='comment' formId='promote-form'}
      </td>
      
      <td class="formcolor">
        <textarea name="comment" style="width:95%;" rows="10" cols='{$cols}' id='comment'>{$comment|escape|@default:"{tr}I found an interesting page that I thought you would like.{/tr}"}</textarea>
      </td>
    </tr>

    <tr class="formcolor">
      <td class="formcolor" rowspan="2">{tr}Send via e-Mail{/tr}</td>
      <td class="formcolor">
		<input type="radio" name="do_email" value="1" checked="checked" />{tr}Yes{/tr}
		<input type="radio" name="do_email" value="0" />{tr}No{/tr}
	  </td>
	</tr>
	<tr class="formcolor" id="emailrow">
	 <td class="formcolor">
	 	<table class="normal"  id="emailtable">
	 	 <tr class="formcolor">
	 	  <td class="formcolor">{tr}Recipient(s){/tr}</td>
	 	  <td class="formcolor">
	 	    <input style="width:95%;" type="text" size="60" name="addresses" value="{$addresses|escape}"/>
	 	    <br /><em>{tr}Separate multiple email addresses with a comma.{/tr}</em>
	 	  </td>
	 	 </tr>
	 	 <tr class="formcolor">
	 	  <td class="formcolor">{tr}Your name{/tr}</td>
	 	  <td class="formcolor"><input style="width:95%;" type="text" name="name" value="{$name}" /></td>
	 	 </tr>
	 	 <tr class="formcolor">
	 	  <td class="formcolor">{tr}Your email{/tr}{if empty($email)} <strong class="mandatory_star">*</strong>{/if}</td>
	 	  <td class="formcolor"><div class="mandatory_field"><input style="width:95%;" type="text" name="email" value="{$email}" /></div></td>
	 	 </tr>
      	</table>
      </td>
    </tr>
    
    <tr class="formcolor">
     <td class="formcolor" rowspan="2">{tr}Tweet via Twitter{/tr}</td>
     <td class="formcolor">{if !$twitterRegistered}
		{remarksbox type="note" title="{tr}Note{/tr}"}
		<p>{tr}To use Twitter integration, the site admin must register this site as an application at <a href="http://twitter.com/oauth_clients/" target="_blank">http://twitter.com/oauth_clients/</a> and allow write access for the application.{/tr}</p>
		{/remarksbox}{else}
		{if $twitter}
		<input type="radio" name="do_tweet" value="1" checked="checked" />{tr}Yes{/tr} 	
		<input type="radio" name="do_tweet" value="0" />{tr}No{/tr}		 
		{else}
		{remarksbox type="note" title="{tr}Note{/tr}"}
		<p><a href="tiki-socialnetworks.php">{tr}Authorize with twitter first{/tr}</a>
		{/remarksbox}
		{/if}
		{/if}
     </td>
    </tr>
    <tr class="formcolor" id="twitterrow">
     <td class="formcolor">
     	{if $twitter}
     	<table class="normal" id="twittertable">
     	 <tr class="formcolor">
     	  <td class="formcolor">{tr}Tweet{/tr}</td>
     	  <td class="formcolor"><input type="text" name="tweet" maxlength="140" style="width:95%;" id="tweet" value="{$tweet|escape|@default:"{tr}Have a look at{/tr} {$shorturl}"}" /></td>
     	 </tr>
     	</table>
     	{else}&nbsp;
     	{/if}
     </td>
    </tr>
    <tr class="formcolor">
     <td class="formcolor" rowspan="2">{tr}Put on my facebook wall{/tr}</td>
     <td class="formcolor">{if !$facebookRegistered}
		{remarksbox type="note" title="{tr}Note{/tr}"}
  <p>{tr}To use Facebook integration, the site admin must register this site as an application at <a href="http://developers.facebook.com/setup/" target="_blank">http://developers.facebook.com/setup/</a> first.{/tr}</p>
 {/remarksbox}{else}
		{if $facebook}
		<input type="radio" name="do_fb" value="1" checked="checked"/>{tr}Yes{/tr}
		<input type="radio" name="do_fb" value="0" />{tr}No{/tr}		
		{else}
		{remarksbox type="note" title="{tr}Note{/tr}"}
		<p><a href="tiki-socialnetworks.php">{tr}Authorize with facebook first{/tr}</a>
		{/remarksbox}
		{/if}
		{/if}
     </td>
    </tr>
    <tr class="formcolor" id="fbrow">
     <td class="formcolor">
     {if $facebook}
      <table class="normal" id="fbtable">
       <tr class="formcolor">
        <td class="formcolor">{tr}Link text{/tr}</td>
        <td class="formcolor"><input type="text" name="fblinktitle" id="fblinktitle" value="{$fblinktitle" style="width: 95%;" /></td>
       </tr>
       <tr class="formcolor">
        <td class="formcolor">{tr}Message{/tr}</td>
        <td class="formcolor">
         <input name="fbpost" style="width:95%;" id="fbpost" value="{$fbpost|escape|@default:"{tr}Have a look at{/tr} {$shorturl}"}" />
         <br /><em>{tr}This will be the title for the URL{/tr}</em>
        </td>
       </tr>
      </table>
      {else}&nbsp;
      {/if}
     </td>
    </tr>
	{if $prefs.feature_antibot eq 'y' && $user eq ''}
		{include file='antibot.tpl' td_style="formcolor"}
	{/if}
    <tr>
      <td class="formcolor"></td>
      <td class="formcolor">
        <input type="submit" class="button" name="send" value="{tr}Send{/tr}" />
		{if $prefs.auth_token_promote eq 'y'}
			<input type="checkbox" name="share_access" value="1" id="share_access"/>
			<label for="share_access">{tr}Share access rights{/tr}</label>
		{/if}
      </td>
    </tr>
  </table>
</form>
