{if $feature_search eq 'y'}
<div class="box">
<div class="box-title">
{tr}Search{/tr}
</div>
<div class="box-data">
    <form class="forms" method="post" action="tiki-searchresults.php">
    <input id="fuser" name="words" size="14" type="text" accesskey="s" /> {tr}in:{/tr}<br/>
    <select name="where">
    {if $feature_wiki eq 'y'}
    <option value="pages">Wiki Pages</option>
    {/if}
    {if $feature_galleries eq 'y'}
    <option value="galleries">Galleries</option>
    <option value="images">Images</option>
    {/if}
    {if $feature_articles eq 'y'}
    <option value="articles">Articles</option>
    {/if}
    {if $feature_blogs eq 'y'}
    <option value="blogs">Blogs</option>
    <option value="posts">Blog Posts</option>
    {/if}
    </select>
    <input type="submit" class="wikiaction" name="search" value="{tr}go{/tr}"/> 
    </form>
</div>
</div>
{/if}