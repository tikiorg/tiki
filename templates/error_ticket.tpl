<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="{if !empty($pageLang)}{$pageLang}{else}{$prefs.language}{/if}" lang="{if !empty($pageLang)}{$pageLang}{else}{$prefs.language}{/if}">
	<head>
{include file='header.tpl'}
	</head>
	<body{html_body_attributes}>
{* Index we display a wiki page here *}
{if $prefs.feature_bidi eq 'y'}
<table dir="rtl" ><tr><td>
{/if}

<div id="tiki-main">
  <div id="tiki-mid">
    <table border="0" cellpadding="0" cellspacing="0" id="tikimidtbl">
    <tr>
      {if $prefs.feature_left_column eq 'y'}
      <td id="leftcolumn">
      {section name=homeix loop=$left_modules}
      {$left_modules[homeix].data}
      {/section}
      </td>
      {/if}
      <td id="centercolumn"><div id="tiki-center">
      <br />
        <div class="cbox">
        <div class="cbox-title">{icon _id=exclamation alt="{tr}Error{/tr}" style="vertical-align:middle"} {$errortitle|default:"{tr}Error{/tr}"}
        </div>
        <div class="cbox-data">
        <br />{$msg}
<form action="{$self}{if $query}?{$query|escape}{/if}" method="post">
{foreach key=k item=i from=$post}
<input type="hidden" name="{$k}" value="{$i|escape}" />
{/foreach}
<input type="submit" name="ticket_action_button" value="{tr}Click here to confirm your action{/tr}" />
</form><br /><br />
        {if $prefs.javascript_enabled eq 'y'}{button href="javascript:history.back()" _text="{tr}Go back{/tr}"}<br /><br />{/if}
        {button href="$prefs.tikiIndex" _text="{tr}Return to home page{/tr}"}
        </div>
        </div>
      </div></td>
      {if $prefs.feature_right_column eq 'y'}
      <td id="rightcolumn">
      {section name=homeix loop=$right_modules}
      {$right_modules[homeix].data}
      {/section}
      </td>
      {/if}
    </tr>
    </table>
  </div>
</div>
{if $prefs.feature_bidi eq 'y'}
</td></tr></table>
{/if}
{include file='footer.tpl'}

	</body>
</html>
