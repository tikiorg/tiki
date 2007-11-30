<span id="topleft">
<a href="/"><img src="styles/tiki/tiki_head.png" width="180" height="37" border="0" alt="Tikiwiki" hspace="0" vspace="0" /></a>
</span>
<span id="topcenter">
{include file="tiki-top_bar.tpl"}
{if $user}<span id="usermenu">{include file="tiki-top_user_menu.tpl"}</span>{/if}
</span>
<span id="topright">
<a href="/"><img src="styles/tiki/tiki_face.png" width="58" height="40" border="0" alt="Tiki" hspace="0" vspace="0" /></a>
</span>
{if $prefs.feature_featuredLinks eq 'y' and count($featuredLinks)}{include file="tiki-top_links.tpl"}{/if}
