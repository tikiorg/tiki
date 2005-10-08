{* @version $Id: solve-top_nav.tpl,v 1.3 2005-10-08 20:14:16 michael_davey Exp $ *}

    {* top nav *}
    <table width='100%'>
      <tr>
        <td style="width:60%" valign="middle" align='left'>
          <span class="buttonheading">
            <a href="{$base_url2}">List</a> {$nav_separator}
            <a href="{$base_url2}/new">New...</a> {$nav_separator}
            <a href="{$base_url2}/search">Search</a>
            {if $isHome}
                {$nav_separator} <a href="{$base_url2}/refresh">Refresh</a> 
            {/if}
          </span>
        </td>
        <td style="text-align: right;" valign="middle">
        {if $task ne 'search'}
            <form method="get" action="{$base_url2}/search">
                <input type="hidden" name="number" value="" />
                <input type="hidden" name="priority" value="" />
                <input type="hidden" name="status" value="" />
                <input type="text" name="name" size="20" class="inputbox" />&nbsp;
                <input type="submit" name="Search" class="button" value="Go"/>
            </form>
        {else}
            &nbsp;
        {/if}
        </td>
      </tr>
    </table>    
    {* /top nav *}

{* end *}
