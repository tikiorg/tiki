<h2>{tr}Copyrights{/tr}: <a href="tiki-index.php?page={$page}">{$page}</a></h2>

<table border="0" width="100%">
{section name=i loop=$copyrights}
  <tr><td class="formcolor">
    <form action="copyrights.php?page={$page}" method="post">
    <input type="hidden" name="page" value="{$page}"/>
    <input type="hidden" name="copyrightId" value="{$copyrights[i].copyrightId}"/>
    <table border="0">
      <tr><td class="formcolor">Title:</td><td><input size="40" class="wikitext" name="copyrightTitle" value="{$copyrights[i].title}" /></td></tr>
      <tr><td class="formcolor">Year:</td><td><input size="4" class="wikitext" name="copyrightYear" value="{$copyrights[i].year}" /></td></tr>
      <tr><td class="formcolor">Authors:</td><td><input size="40" class="wikitext" name="copyrightAuthors" value="{$copyrights[i].authors}" /></td></tr>
    </table>
  </td><td class="formcolor" align="right">
    <input type="submit" name="editcopyright" value="{tr}edit{/tr}"/><br />
    <a href="copyrights.php?page={$page}&action=delete&copyrightId={$copyrights[i].copyrightId}">x</a>
    <a href="copyrights.php?page={$page}&action=up&copyrightId={$copyrights[i].copyrightId}">up</a>
    <a href="copyrights.php?page={$page}&action=down&copyrightId={$copyrights[i].copyrightId}">down</a>
    </form>
  </td></tr>
{/section}
<form action="copyrights.php?page={$page}">
<tr><td class="formcolor">
    <table border="0">
      <tr><td class="formcolor">Title:</td><td><input size="40" class="wikitext" name="copyrightTitle" value="{$copyrights[i].title}" /></td></tr>
      <tr><td class="formcolor">Year:</td><td><input size="4" class="wikitext" name="copyrightYear" value="{$copyrights[i].year}" /></td></tr>
      <tr><td class="formcolor">Authors:</td><td><input size="40" class="wikitext" name="copyrightAuthors" value="{$copyrights[i].authors}" /></td></tr>
    </table><input type="hidden" name="page" value="{$page}"/>
</td><td class="formcolor"><input type="submit" name="addcopyright" value="{tr}add{/tr}"/> </td></tr>
</form>
</table>
