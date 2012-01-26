<div id="broadcasterContent" style="width:760px;height:455px;">
<object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" width="100%" height="100%" id="{$wsd_objectId|escape}">
<param name="flashvars" value="sid={$wsd_sessionId|escape}&cid={$wsd_brandId|escape}%2F{$wsd_channelCode|escape}"/>
<param name="allowfullscreen" value="true"/><param name="allowscriptaccess" value="always"/>
<param name="movie" value="http://www.ustream.tv/flash/broadcaster.swf?r=api&v=5"/>
<embed flashvars="sid={$wsd_sessionId|escape}&cid={$wsd_brandId|escape}%2F{$wsd_channelCode|escape}"
 width="100%" height="100%" allowfullscreen="true" allowscriptaccess="always"
 id="{$wsd_objectId|escape}" name="{$wsd_embedName|escape}"
 src="http://www.ustream.tv/flash/broadcaster.swf?r=api&v=5"
 type="application/x-shockwave-flash" />
</object>
</div>
{jq notonready=true}
function resize(w,h){if ( w > 0 ){document.getElementById("broadcasterContent").style.width = w + "px";}if ( h > 0 ){document.getElementById("broadcasterContent").style.height = h + "px";}return true;}
{/jq}