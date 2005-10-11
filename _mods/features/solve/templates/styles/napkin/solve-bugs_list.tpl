{* @version $Id: solve-bugs_list.tpl,v 1.3 2005-10-11 13:10:45 michael_davey Exp $ *}

{breadcrumbs type="trail" loc="page" crumbs=$crumbs}{breadcrumbs type="pagetitle" loc="page" crumbs=$crumbs}
<br />
{breadcrumbs type="desc" loc="page" crumbs=$crumbs}

<div align="center">
  <table border="0" cellpadding="0" cellspacing="0" width="743">
    <tr>
      <td class="outline" width="99%">

    {include file=solve-top_nav.tpl}

    <!-- list -->
    {* search header *}
    {if $task eq 'search'}
        {literal}
        <script type="text/javascript" language="JavaScript">
        <!-- Begin
        statustextholder=window.status;
        function clear_form(form) {
            form.name.value = '';
            form.number.value = '';
            form.priority.selectedIndex = 0;
            form.status.selectedIndex = 0;
        }
        
        function set_order_by_and_submit(form, orderby) {
            form.order_by.value = orderby;
            form.submit();
        }
        
        function show_status(statustext) {
            statustextholder=window.status;
            window.status = statustext;
            return true;
        }
        
        function restore_status() {
            window.status = statustextholder;
            return true;
        }
        //  End -->
        </script>
        {/literal}

        <form method="get" action="{$base_url2}" name="SearchForm">
            <input type="hidden" name="task" value="search" />
            <input type="hidden" name="order_by" value="{$sortcolumn},{$sortorder}" />
        
        <table width="100%" border="0" cellspacing="0" cellpadding="0"   class="tabForm">
        <tr>
          <td style="width: 80%">
            <table style="width: 100%;" border="0" cellspacing="1" cellpadding="1" class="tabForm">
            <tr>
              {foreach from=$queryfields key=field item=value name=list}
                <td style="width: 20%;">{$queryfields.$field.label}</td>
                <td style="width: 25%;">
                  {$queryfields.$field.formfield}
                </td>
                {if ($smarty.foreach.list.iteration % 2) != 1}
                  </tr><tr>
                {/if}
              {/foreach}
            </tr>
            </table>
          </td>
          <td width="10%" rowspan="3" align="right">
            <input class="button" type="submit" name="button" value="Search"/>&nbsp;
            <input onclick="clear_form(this.form);" class="button" type="button" name="clear" value="Clear" />
          </td>
        </tr>
      </table>
    {/if}
    {* /search header *}
    
    
    {* list *}
    {if ! $items }
      <p>No {$section} to display.</p>
    {else}

      <table class="contentpaneopen;" style="width: 100%;">
        <tr>
        
          {foreach from=$columns item=column}
            {if $column.inlist}
              <th style="text-align: left;">
                <a {$column.href} {$column.onClick} onmouseover="javascript: return show_status('Sort by {$column.orderby} ');" onMouseout="javascript: return restore_status();">{$column.name}</a></th>
            {/if}
          {/foreach}
        
          <td>&nbsp;</td>
        </tr>

        {foreach from=$items item=item}
          <tr>
            {foreach from=$item.columns item=column}
              {if (bool)$column.inlist}
                <td>{$column.inputWidget}</td>
              {/if}
            {/foreach}
              {if $editbutton}
                <td><a href="{$base_url2}/edit/{$item.id}">Edit</a></td>
              {/if}
              {if $editbutton && $viewbutton}<td>{$nav_separator}</td>{/if}
              {if $viewbutton}
                <td><a href="{$base_url2}/view/{$item.id}">View</a></td>
              {/if}
          </tr>
        {/foreach}
      </table>
    {/if}
    {* /list *}


    {* search footer *}
    {if $task eq 'search'}
        </form>
    {/if}
    {* /search footer *}

<!-- /list -->
{* end *}

      </td>
    </tr>
  </table>
</div>

