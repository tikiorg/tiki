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
    <option value="pages">{tr}Wiki Pages{/tr}</option>
    {/if}
    {if $feature_galleries eq 'y'}
    <option value="galleries">{tr}Image Gals{/tr}</option>
    <option value="images">{tr}Images{/tr}</option>
    {/if}
    {if $feature_file_galleries eq 'y'}
    <option value="files">{tr}Files{/tr}</option>
    {/if}
    {if $feature_articles eq 'y'}
    <option value="articles">{tr}Articles{/tr}</option>
    {/if}
    {if $feature_forums eq 'y'}
    <option value="forums">{tr}Forums{/tr}</option>
    {/if}
    {if $feature_blogs eq 'y'}
    <option value="blogs">{tr}Blogs{/tr}</option>
    <option value="posts">{tr}Blog Posts{/tr}</option>
    {/if}
    {if $feature_faqs eq 'y'}
    <option value="faqs">{tr}FAQs{/tr}</option>
    {/if}
    </select>
    <input type="submit" class="wikiaction" name="search" value="{tr}go{/tr}"/> 
    </form>
</div>
</div>
{/if}