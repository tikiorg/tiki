{* @version $Id: solve-top_nav.tpl,v 1.1 2005-09-21 21:13:00 michael_davey Exp $ *}

    {* top nav *}
    <table width='100%'>
      <tr>
        <td style="width:60%" valign="middle" align='left'>
          <span class="buttonheading">
            <a href="{$base_url2}">List</a> {$nav_separator}
            <a href="{$base_url2}&task=new">New...</a> {$nav_separator}
            <a href="{$base_url2}&task=search">Search</a>
            <?php
            {if $isHome}
                {$nav_separator} <a href="{$base_url2}&task=refresh">Refresh</a> 
            {/if}
          </span>
        </td>
        <td style="text-align: right;" valign="middle">
        {if $task ne 'search'}
            <form method="get" action="solve">
                <input type="hidden" name="option" value="{$option}" />
                <input type="hidden" name="task" value="search" />
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
