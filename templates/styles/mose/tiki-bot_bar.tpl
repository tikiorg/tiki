<table>
<tr>
{if $prefs.feature_bot_bar_icons eq 'y'}
<td id="power">
<a href="http://tikiwiki.org/" title="Tikiwiki"><img style="border: 0; vertical-align: middle" alt="{tr}Powered by{/tr} Tikiwiki" src="img/tiki/tikibutton2.png" /></a>
<a href="http://www.php.net/" title="PHP"><img style="border: 0; vertical-align: middle" alt="{tr}Powered by{/tr} PHP" src="img/php.png" /></a>
<a href="http://smarty.php.net/" title="Smarty"><img style="border: 0; vertical-align: middle" alt="{tr}Powered by{/tr} Smarty" src="img/smarty.gif"  /></a>
<a href="http://adodb.sourceforge.net/" title="ADOdb"><img style="border: 0; vertical-align: middle" alt="{tr}Powered by{/tr} ADOdb" src="img/adodb.png" /></a>
<a href="http://www.w3.org/Style/CSS/" title="CSS"><img style="border: 0; vertical-align: middle" alt="{tr}Made with{/tr} CSS" src="img/css1.png" /></a>
<a href="http://www.w3.org/RDF/" title="RDF"><img style="border: 0; vertical-align: middle" alt="{tr}Powered by{/tr} RDF" src="img/rdf.gif"  /></a>
{if $prefs.feature_phplayers eq 'y'}
<a href="http://phplayersmenu.sourceforge.net/" title="PHP Layers Menu"><img style="border: 0; vertical-align: middle" alt="{tr}powered by{/tr} The PHP Layers Menu System" src="lib/phplayers/LOGOS/powered_by_phplm.png"  /></a>
{/if}
{if $prefs.feature_mobile eq 'y'}
<a href="http://www.hawhaw.de/" title="HAWHAW"><img style="border: 0; vertical-align: middle" alt="{tr}powered by{/tr} HAWHAW" src="img/poweredbyhawhaw.gif"  /></a>
{/if}
</td>
{/if}
<td >
<div align="center" style="color:#727272;font-size:80%;">
<small>[ {tr}Execution time{/tr}: {elapsed} {tr}secs{/tr} ] &nbsp; 
[ {$num_queries} {tr}database queries used{/tr} ]<br />
[ GZIP {$gzip} ] &nbsp; 
[ {tr}Server load{/tr}: {$server_load} ] &nbsp; 
[ <a href="http://tikiwiki.org" class="link" style="color:#727272;font-size:85%;">Tikiwiki</a> ]</small>
</div>
</td>
{if $prefs.rss_wiki eq 'y'}
<td width="50">
<a href="tiki-wiki_rss.php"><img src='pics/icons/feed.png' style='border: 0; vertical-align: text-bottom;' alt='{tr}RSS feed{/tr}' title='{tr}RSS feed{/tr}' width='16' height='16' /></a><br />
<small>{tr}Wiki{/tr}</small>
</td>
{/if}
{if $prefs.rss_blogs eq 'y'}
<td width="50">
<a href="tiki-blogs_rss.php"><img src='pics/icons/feed.png' style='border: 0; vertical-align: text-bottom;' alt='{tr}RSS feed{/tr}' title='{tr}RSS feed{/tr}' width='16' height='16' /></a><br />
<small>{tr}Blogs{/tr}</small>
</td>
{/if}
{if $prefs.rss_articles eq 'y'}
<td width="50">
<a href="tiki-articles_rss.php"><img src='pics/icons/feed.png' style='border: 0; vertical-align: text-bottom;' alt='{tr}RSS feed{/tr}' title='{tr}RSS feed{/tr}' width='16' height='16' /></a><br />
<small>{tr}Articles{/tr}</small>
</td>
{/if}
{if $prefs.rss_image_galleries eq 'y'}
<td width="50">
<a href="tiki-image_galleries_rss.php"><img src='pics/icons/feed.png' style='border: 0; vertical-align: text-bottom;' alt='{tr}RSS feed{/tr}' title='{tr}RSS feed{/tr}' width='16' height='16' /></a><br />
<small>{tr}Images{/tr}</small>
</td>
{/if}
{if $prefs.rss_file_galleries eq 'y'}
<td width="50">
<a href="tiki-file_galleries_rss.php"><img src='pics/icons/feed.png' style='border: 0; vertical-align: text-bottom;' alt='{tr}RSS feed{/tr}' title='{tr}RSS feed{/tr}' width='16' height='16' /></a><br />
<small>{tr}Files{/tr}</small>
</td>
{/if}
{if $prefs.rss_forums eq 'y'}
<td width="50">
<a href="tiki-forums_rss.php"><img src='pics/icons/feed.png' style='border: 0; vertical-align: text-bottom;' alt='{tr}RSS feed{/tr}' title='{tr}RSS feed{/tr}' width='16' height='16' /></a><br />
<small>{tr}Forums{/tr}</small>
</td>
{/if}

</tr>
</table>

{include file="babelfish.tpl"}

