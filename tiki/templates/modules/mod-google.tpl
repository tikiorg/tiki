{* $Header: /cvsroot/tikiwiki/tiki/templates/modules/mod-google.tpl,v 1.8 2003-08-07 20:56:53 zaufi Exp $ *}

<div class="box">
<div class="box-title">
{include file="modules/module-title.tpl" module_title="{tr}Google Search{/tr}" module_name="google"}
</div>
<div class="box-data">
<form method="get" action="http://www.google.com/search" target="Google" style="margin-bottom:2px;">
<inpt type="hidden" name="hl" value="en"/>
<inpt type="hidden" name="oe" value="UTF-8"/>
<inpt type="hidden" name="ie" value="UTF-8"/>
<inpt type="hidden" name="btnG" value="Google Search"/>
<input name="googles" type="image" width='16' height='16' src="img/googleg.gif" border="0" alt="Google" align="left" vspace="0" hspace="4"/>
<input type="text" name="q" size="12"  maxlength="100" style="height:16px;"/>
</form>
</div>
</div>
