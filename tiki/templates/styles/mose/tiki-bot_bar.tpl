<table cellpadding="3" width="100%">
<tr>
<td>
<a target="_blank" href="http://www.w3.org/Style/CSS/"><img alt="css" border="0" src="img/css.gif" width="62" height="22"/></a>
</td>
<td>
<a href="http://validator.w3.org/check/referer"><img border="0" src="img/valid-xhtml10.png" alt="Valid XHTML 1.0!" height="22" width="62" /></a>
</td>
<td>
<a target="_blank" href="http://www.php.net"><img border="0" alt="php" src="img/php.png" width="62" height="22"/></a>
</td>
<td width="100%">
<div align="center">
<small>{tr}Page generated in{/tr}: {elapsed} {tr}seconds{/tr}
{tr}by{/tr} <a href="http://tikiwiki.sf.net" class="link">TikiWiki</a></small>
</div>
</td>
{if $rss_wiki eq 'y'}
<td>
<a href="tiki-wiki_rss.php"><img alt="rss" border="0" src="img/rss.png" /></a><br />
<small>Wiki</small>
</td>
{/if}
{if $rss_blogs eq 'y'}
<td>
<a href="tiki-blogs_rss.php"><img alt="rss" border="0" src="img/rss.png" /></a><br />
<small>Blogs</small>
</td>
{/if}
{if $rss_articles eq 'y'}
<td>
<a href="tiki-articles_rss.php"><img alt="rss" border="0" src="img/rss.png" /></a><br />
<small>Articles</small>
</td>
{/if}
{if $rss_image_galleries eq 'y'}
<td>
<a href="tiki-image_galleries_rss.php"><img alt="rss" border="0" src="img/rss.png" /></a><br />
<small>Images</small>
</td>
{/if}
{if $rss_file_galleries eq 'y'}
<td>
<a href="tiki-file_galleries_rss.php"><img alt="rss" border="0" src="img/rss.png" /></a><br />
<small>Files</small>
</td>
{/if}
{if $rss_forums eq 'y'}
<td>
<a href="tiki-forums_rss.php"><img alt="rss" border="0" src="img/rss.png" /></a><br />
<small>Forums</small>
</td>
{/if}

</tr>
</table>

<div align="center">
	<small>{$babelfish_links}</small>
</div>
<div align="center">
	{$babelfish_logo}
</div>
