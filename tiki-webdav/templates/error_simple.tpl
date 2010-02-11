{* $Id$ *}<!DOCTYPE html 
	PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="{if !empty($pageLang)}{$pageLang}{else}{$prefs.language}{/if}" lang="{if !empty($pageLang)}{$pageLang}{else}{$prefs.language}{/if}">
	<head>
{include file='header.tpl'}
	</head>
	<body{if isset($section) and $section eq 'wiki page' and $prefs.user_dbl eq 'y' and $dblclickedit eq 'y' and $tiki_p_edit eq 'y'} ondblclick="location.href='tiki-editpage.php?page={$page|escape:"url"}';"{/if} onload="{if $prefs.feature_tabs eq 'y'}tikitabs({if $cookietab neq ''}{$cookietab}{else}1{/if},50);{/if}{if $msgError} javascript:location.hash='msgError'{/if}"{if $section_class or $smarty.session.fullscreen eq 'y'} class="{if $section_class}tiki {$section_class}{/if}{if $smarty.session.fullscreen eq 'y'} fullscreen{/if}"{/if}>
{if $prefs.feature_bidi eq 'y'}
<table dir="rtl" ><tr><td>
{/if}

<div id="tiki-mid">
<div class="cbox">
<div class="cbox-title">{icon _id=exclamation alt="{tr}Error{/tr}" style=vertical-align:middle"}{tr}Error{/tr}</div>
<div class="cbox-data">
{$msg}<br /><br />
<a href="javascript:window.close()" class="linkmenu">{tr}Close Window{/tr}</a><br /><br />
</div>
</div>
</div>
{if $prefs.feature_bidi eq 'y'}
</td></tr></table>
{/if}
{include file='footer.tpl'}
	</body>
</html>
