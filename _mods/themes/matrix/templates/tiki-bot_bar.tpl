{* $Header: /cvsroot/tikiwiki/_mods/themes/matrix/templates/tiki-bot_bar.tpl,v 1.1 2004-10-29 19:14:22 damosoft Exp $ *}


{include file="babelfish.tpl"}

<table >
  <tr>
    <td >
    <table>
      <tr>
        <td colspan="2">
          <img alt="rss" border="0" src="img/rss.png" />
        </td>
      </tr><tr>
        {if $rss_wiki eq 'y'}
          <td> <a href="tiki-wiki_rss.php"> Wiki </a> </td>
        {/if}
        {if $rss_blogs eq 'y'}
          <td> <a href="tiki-blogs_rss.php"> Blogs </a> </td>
        {/if}
      </tr><tr>
        {if $rss_articles eq 'y'}
          <td> <a href="tiki-articles_rss.php"> Acticles </a> </td>
        {/if}
        {if $rss_forums eq 'y'}
          <td> <a href="tiki-forums_rss.php"> Forums </a> </td>
        {/if}
      </tr><tr>
        {if $rss_file_galleries eq 'y'}
          <td> <a href="tiki-file_galleries_rss.php"> File galleries </a> </td>
        {/if}
        {if $rss_image_galleries eq 'y'}
          <td> <a href="tiki-image_galleries_rss.php"> Image galleries </a> </td>
        {/if}
      </tr>
    </table>
    </td>
    <td align="center"> {tr}Page generated in{/tr}: {elapsed} {tr}seconds{/tr}  </td>
    <td >
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
