{* $Header: /cvsroot/tikiwiki/tiki/templates/styles/notheme/tiki-bot_bar.tpl,v 1.8 2007-10-04 22:17:49 nyloth Exp $ *}

{include file="styles/notheme/babelfish.tpl"}

<table >
  <tr>
    <td >
    <table>
      <tr>
        <td colspan="2">
          <img src='pics/icons/feed.png' border='0' alt='{tr}RSS feed{/tr}' title='{tr}RSS feed{/tr}' width='16' height='16' />
        </td>
      </tr><tr>
        {if $prefs.rss_wiki eq 'y'}
          <td> <a href="tiki-wiki_rss.php"> {tr}Wiki{/tr} </a> </td>
        {/if}
        {if $prefs.rss_blogs eq 'y'}
          <td> <a href="tiki-blogs_rss.php"> {tr}Blogs{/tr} </a> </td>
        {/if}
      </tr><tr>
        {if $prefs.rss_articles eq 'y'}
          <td> <a href="tiki-articles_rss.php"> {tr}Articles{/tr} </a> </td>
        {/if}
        {if $prefs.rss_forums eq 'y'}
          <td> <a href="tiki-forums_rss.php"> {tr}Forums{/tr} </a> </td>
        {/if}
      </tr><tr>
        {if $prefs.rss_file_galleries eq 'y'}
          <td> <a href="tiki-file_galleries_rss.php"> {tr}File galleries{/tr} </a> </td>
        {/if}
        {if $prefs.rss_image_galleries eq 'y'}
          <td> <a href="tiki-image_galleries_rss.php"> {tr}Image galleries{/tr} </a> </td>
        {/if}
      </tr>
    </table>
    </td>
    <td align="center"> {tr}Page generated in{/tr}: {elapsed} {tr}seconds{/tr}  </td>
    <td  align=right>
    <table>
      <tr>
        <td>
          <a target="_blank" href="http://www.w3.org/Style/CSS/"><img alt="css" border="0" src="img/css.gif" width="62" height="22"/></a>
        </td>
        <td>
          <a href="http://validator.w3.org/check/referer"><img border="0" src="img/valid-xhtml10.png" alt="Valid XHTML 1.0!" height="22" width="62" /></a>
        </td>
      </tr>

      <tr>
        <td>
          <a target="_blank" href="http://www.php.net"><img border="0" alt="php" src="img/php.png" width="62" height="22"/></a>
        </td>
        <td>
          <a target="_blank" href="http://pear.php.net/"><img border="0" alt="pear" src="img/pear.png" width="62" height="22"/></a>
        </td>
      </tr>

      <tr>
        <td>
        </td>
        <td>
          <a target="_blank" href="http://smarty.php.net/"><img border="0" alt="smarty" src="img/smarty.gif" width="62" height="22"/></a>
        </td>
      </tr>
    </table>
    </td>
  </tr>
</table>
