<h2>{tr}Chatroom{/tr}: {$channelName}</h2>
<table class="chatroom">
<tr>

  <td class="chatarea" valign="top" >
  <!-- <iframe name="chatdata" scrolling="auto" frameborder="0" height="360" src="tiki-chat_center.html">{tr}Browser not supported{/tr}</iframe> -->
  <iframe name="chatdata" width="50%" scrolling="auto" frameborder="0" height="360" src="tiki-chat_loader.php?refresh={$refresh}&amp;enterTime={$now}&amp;nickname={$nickname}&amp;channelId={$channelId}">{tr}Browser not supported{/tr}</iframe>
  </td>
  <td class="chatchannels"  valign="top" width="180">
  <div class="chattitle">{tr}Active Channels{/tr}:</div>
  {section name=chan loop=$channels}
    <a class="link" href="tiki-chatroom.php?nickname={$nickname}&amp;channelId={$channels[chan].channelId}">{$channels[chan].name}</a><br />
  {/section}
  <br />
  <div class="chattitle">{tr}Users in this channel{/tr}:</div>
  <iframe width="100%" frameborder="0" height="200" scrolling="auto" marginwidth="0" marginheight="0"  src="tiki-chat_users.php?channelId={$channelId}"></iframe>
  </td>
  <!--
  <td  valign="top">
    <div class="texthead">{tr}Channel Information{/tr}<a class="link" href="chat.php">(re)</a></div>
    <div class="text">
    {tr}Channel{/tr}: {$channel_info.name}<br />
    {tr}Ratio{/tr}: {$channel_info.ratio} <br /><br />
    {tr}Desc{/tr}: {$channel_info.description}
    </div>
  </td>
  -->
</tr>
</table>
<table class="chatform">
<tr>
  <td class="tdchatform">
  <iframe name="chatbox" width="100%" scrolling="no"  height="52" frameborder="0" src="tiki-chat_box.php?nickname={$nickname}&amp;channelId={$channelId}">Browser not supported</iframe>
  </td>
</tr>  
</table>
<table class="chatform">
<tr>
  <td class="tdchatform">
   <small>
   {tr}Use :nickname:message for private messages{/tr}<br /> 
   {tr}Use [URL|description] or [URL] for links{/tr}<br />
   {if $feature_smileys eq 'y'}
   {tr}Use (:smileyname:) for smileys{/tr} (smile, biggrin, cool, evil, frown, rolleyes, confused, cry, eek, exclaim, idea, mad, surprised, lol, redface, neutral, sad, twisted, wink)<br />
   {/if}
   </small>
  </td>
</tr>  
</table>


          
