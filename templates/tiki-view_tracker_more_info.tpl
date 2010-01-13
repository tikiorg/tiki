{* $Id$ *}
<!DOCTYPE html 
	PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="{if !empty($pageLang)}{$pageLang}{else}{$prefs.language}{/if}" lang="{if !empty($pageLang)}{$pageLang}{else}{$prefs.language}{/if}">
	<head>
{include file="header.tpl"}
	</head>
	<body{if isset($section) and $section eq 'wiki page' and $prefs.user_dbl eq 'y' and $dblclickedit eq 'y' and $tiki_p_edit eq 'y'} ondblclick="location.href='tiki-editpage.php?page={$page|escape:"url"}';"{/if} onload="{if $prefs.feature_tabs eq 'y'}tikitabs({if $cookietab neq ''}{$cookietab}{else}1{/if},50);{/if}{if $msgError} javascript:location.hash='msgError'{/if}"{if $section_class or $smarty.session.fullscreen eq 'y'} class="{if $section_class}tiki_{$section_class}{/if}{if $smarty.session.fullscreen eq 'y'} fullscreen{/if}"{/if}>

{if $prefs.feature_bidi eq 'y'}
<table dir="rtl" ><tr><td>
{/if}
<div id="tiki-main" class="simplebox">
<h3>{tr}Details{/tr}</h3>
<table class="normalnoborder">
{if $info.name}
<tr class="formcolor"><td>{tr}Name{/tr}</td><td><b>{$info.name}</b></td></tr>
{/if}
{if $info.version}
<tr class="formcolor"><td>{tr}Version{/tr}</td><td><b>{$info.version}</b></td></tr>
{/if}
{if $info.longdesc}
<tr class="formcolor"><td colspan="2">{$info.longdesc}</td></tr>
{/if}
{if $info.hits}
<tr class="formcolor"><td>{tr}Downloads{/tr}</td><td>{$info.hits}</td></tr>
{/if}
</table>
<div class="cbox">
<a href="#" onclick="javascript:window.close();" class="link">{tr}close{/tr}</a>
</div>
</div>
{if $prefs.feature_bidi eq 'y'}
</td></tr></table>
{/if}
{include file="footer.tpl"}
	</body>
</html>
