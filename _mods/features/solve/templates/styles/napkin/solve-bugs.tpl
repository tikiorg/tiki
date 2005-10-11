{* @version $Id: solve-bugs.tpl,v 1.4 2005-10-11 13:10:45 michael_davey Exp $ *}

{breadcrumbs type="trail" loc="page" crumbs=$crumbs}{breadcrumbs type="pagetitle" loc="page" crumbs=$crumbs}
<br />
{breadcrumbs type="desc" loc="page" crumbs=$crumbs}

<div align="center">
  <table border="0" cellpadding="0" cellspacing="0" width="743">
    <tr>
      <td class="outline" width="99%">

    {include file=solve-top_nav.tpl}

    {* appForm *}
        <table class="contentpaneopen">
          <tr>
            <td class='contentheading'>{$item_title}</td>
          </tr>
        </table>
        {if $task eq 'edit'}
        <form name="NewView" method="post" action="{$base_url2}/{$savetype}">
        {else}
        <form name="NewView" method="post" action="{$base_url2}">
        {/if}
          <table class="contentpaneopen">
            <tr>
              <td style="padding-bottom: 2px;">
              {if $task eq 'edit'}
                <input class="button" type="submit" name="button" value="Save" >
                <input class="button" type="submit" name="button" value="Cancel">
              {else}
                <input class="button" type="submit" name="button" value="Return">
              {/if}
                <input type="hidden" name="id" value="{$tmpCase.id}" />
              </td>
            </tr>
        {foreach from=$columns.selected item=column}
          {if $column.showme}
             <tr>
               <td style="width: 20%; vertical-align: top;">{$column.name}</td>
               <td>{$column.inputWidget}</td>
             </tr>
          {/if}
        {/foreach}
        </table>
        </form>
    {* /appForm *}
    

    {* notes *}
    {if $item != null}
        <table class="contentpaneopen"><tr><td class='contentheading'>Notes</td></tr></table>
        {if $notes}
            <table class="contentpaneopen">
            {foreach from=$notes item=note}
              <tr><td><b>Subject:</b>&nbsp;{$note.name}</td><td><b>Last Modified:</b>&nbsp;{$note.date_modified|date_format:$datetimeformat}</td></tr>
              <tr>
                <td colspan="2" style="margin-left: 10%; margin-right: 10%;">
                  <b>Note:</b><br>
                  {$note.description|nl2br}<br />
                </td>
              </tr>
              <tr><td><b>Attachment:</b>&nbsp;{$note.htmlfilename}</td></tr>
              <tr><td colspan=2><hr></td></tr>
            {/foreach}
            </table>
        {else}
          <p>No notes at this time.</p>
        {/if}
        {if $task eq 'edit'}
        <table class="contentpaneopen">
          <tr>
            <td class='contentheading'>New Note</td>
          </tr>
        </table>
        <form name="NewView" enctype="multipart/form-data" method="POST" action="{$base_url2}/newnote">
            <!-- MAX_FILE_SIZE must precede the file input field -->
            <input type="hidden" name="MAX_FILE_SIZE" value="2097152" />
            <input type="hidden" name="caseID" value="{$itemID}" />
        <table class="contentpaneopen">
          <tr>
            <th>New Note</th>
          </tr>
          <tr>
            <td>Subject: <input type="text" class="inputbox" name="name" /></td>
          </tr>
          <tr>
            <td>Note:</td>
          </tr>
          <tr>
            <td>
                <textarea class="inputbox" cols="50" rows="10" name="description"></textarea>
            </td>
          <tr>
            <td>File Attachment: <input type="file" name="attachment"/></td>
          </tr>
          <tr><td height="5">&nbsp;</td></tr>
          </tr>
          {*<tr>
            <td>Attachment: 
              <input name="attachment" type="file" class="inputbox" />
            </td>
          </tr> *}
          <tr>
            <td>
                <input class="button" type="submit" name="button" value="Save Note" />
            </td>
          </tr>
        </table>
        </form>
        {/if}
    {/if}
    {* /notes *}
 
      </td>
    </tr>
  </table>
</div>
