{* $Header: /cvsroot/tikiwiki/tiki/templates/tiki-chat_box.tpl,v 1.6 2006-08-29 20:19:13 sylvieg Exp $ *}
<!--<form onsubmit="javascript:top.document.frames[0].document.write('<span style=\'color:blue;\'>{$nickname}'+':'+getElementById('chatedit').value+'</span><br />');">-->
<form name="dform">
<input type="hidden" name="channelId" value="{$channelId|escape}" />
<input type="hidden" name="nickname" value="{$nickname|escape}" />
{tr}User{/tr}: {$nickname} 
<input id="chatedit" accesskey="s" type="text" name="data" size="50" />
<!--<input type="submit" name="send" value="send" />-->
</form>
<!-- set focus in the input box every time to have user experience-->
{literal}
 <script type="text/javascript">
	window.onLoad = initLoad();
	function initLoad()
	{
 	//getElementById('chatedit').focus();
  	document.dform.data.focus();
 	}
 </script>
{/literal}

