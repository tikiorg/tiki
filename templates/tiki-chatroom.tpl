<h2>{tr}Chatroom{/tr}: {$channelName}</h2>
<table class="chatroom">
<tr>
  <td class="chatchannels" width="20%" valign="top">
  <div class="chattitle">{tr}Active Channels{/tr}:</div>
  {section name=chan loop=$channels}
    <a class="link" href="tiki-chatroom.php?nickname={$nickname}&amp;channelId={$channels[chan].channelId}">{$channels[chan].name}</a><br/>
  {/section}
  </td>
  <td class="chatarea" valign="top" width="80%">
  <iframe width="100%" name="chatdata" scrolling="auto" frameborder="0" height="360" src="tiki-chat_center.html">Browser not supported</iframe>
  <iframe width='0' height='0' frameborder="0" src="tiki-chat_loader.php?refresh={$refresh}&amp;enterTime={$now}&amp;nickname={$nickname}&amp;channelId={$channelId}">Browser not supported</iframe>
  </td>
  <!--
  <td width="20%" valign="top">
    <div class="texthead">{tr}Channel Information{/tr}<a class="link" href="chat.php">(re)</a></div>
    <div class="text">
    Channel: {$channel_info.name}<br/>
    Ratio: {$channel_info.ratio} <br/><br/>
    Desc: {$channel_info.description}
    </div>
  </td>
  -->
</tr>
</table>
<table class="chatform">
<tr>
  <td class="tdchatform">
  <iframe scrolling="no" width="100%" height="42" frameborder="0" src="tiki-chat_box.php?nickname={$nickname}&amp;channelId={$channelId}">Browser not supported</iframe>
  </td>
</tr>  
</table>