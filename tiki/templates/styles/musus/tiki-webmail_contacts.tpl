<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
{* Index we display a wiki page here *}

<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <link rel="stylesheet" href="styles/{$style}" type="text/css" />
  {include file="bidi.tpl"}
  <title>{tr}Address book{/tr}</title>
</head>
{strip}

<body>

<div id="tiki-clean">

  <h2>{tr}Contacts{/tr}</h2>

  <table class="findtable" summary="">
    <colgroup><col /><col /></colgroup>
    <tbody>
      <tr>
        <td>{tr}Find{/tr}</td>
        <td>
          <form method="get" action="tiki-webmail_contacts.php">
            <fieldset>
              <legend>{tr}Find{/tr}</legend>
              <input class="text" type="text" name="find" value="{$find|escape}" />
              <input class="submit" type="submit" value="{tr}find{/tr}" name="search" />
              <input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
              <input type="hidden" name="element" value="{$element|escape}" />
              <input type="hidden" name="section" value="contacts" />
            </fieldset>
          </form>
        </td>
      </tr>
    </tbody>
  </table>

  <a href="tiki-webmail_contacts.php?element={$element}&amp;section=contacts&amp;offset={$offset}&amp;sort_mode={$sort_mode}&amp;find={$find}">{tr}all{/tr}</a>

  {section name=ix loop=$letters}
  <a href="tiki-webmail_contacts.php?element={$element}&amp;section=contacts&amp;offset={$offset}&amp;sort_mode={$sort_mode}&amp;find={$find}&amp;letter={$letters[ix]}">{$letters[ix]}</a>
  {/section}

  <table summary="">
    <colgroup><col /><col /><col /><col /></colgroup>
    <thead>
      <tr>
        <th><a href="tiki-webmail_contacts.php?element={$element}&amp;section=contacts&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'firstName_desc'}firstName_asc{else}firstName_desc{/if}">{tr}First Name{/tr}</a></th>
        <th><a href="tiki-webmail_contacts.php?element={$element}&amp;section=contacts&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'lastName_desc'}lastName_asc{else}lastName_desc{/if}">{tr}Last Name{/tr}</a></th>
        <th><a href="tiki-webmail_contacts.php?element={$element}&amp;section=contacts&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'email_desc'}email_asc{else}email_desc{/if}">{tr}Email{/tr}</a></th>
        <th><a href="tiki-webmail_contacts.php?element={$element}&amp;section=contacts&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'nickname_desc'}nickname_asc{else}nickname_desc{/if}">{tr}Nickname{/tr}</a></th>
      </tr>
    </thead>
    <tbody>
      {cycle values="odd,even" print=false}
      {section name=user loop=$channels}
      <tr>
        <td class="{cycle advance=false}">{$channels[user].firstName}</td>
        <td class="{cycle advance=false}">{$channels[user].lastName}</td>
        <td class="{cycle advance=false}">
          <a href="#" onclick="javascript:window.opener.document.getElementById('{$element}').value=window.opener.document.getElementById('{$element}').value + '{$channels[user].email}' + ' ';">{$channels[user].email}</a>
          [<a
           
            href="tiki-webmail_contacts.php?element={$element}&amp;section=contacts&amp;offset={$offset}&amp;sort_mode={$sort_mode}&amp;find={$find}&amp;remove={$channels[user].contactId}"
            onclick="return confirmTheLink(this,'{tr}Are you sure you want to delete this contact?{/tr}')"
            title="{tr}Click here to delete this contact{/tr}">
              <img alt="{tr}Remove{/tr}" src="img/icons2/delete.gif" />
          </a>]
        </td>
        <td class="{cycle advance=false}">{$channels[user].nickname}</td>
      </tr>
      {/section}
    </tbody>
  </table>

  <div class="mini">
    {if $prev_offset >= 0}
    [<a class="prevnext" href="tiki-webmail_contacts.php?element={$element}&amp;section=contacts&amp;find={$find}&amp;offset={$prev_offset}&amp;sort_mode={$sort_mode}">{tr}prev{/tr}</a>]&nbsp;
    {/if}
    {tr}Page{/tr}: {$actual_page}/{$cant_pages}
    {if $next_offset >= 0}
    &nbsp;
    [<a class="prevnext" href="tiki-webmail_contacts.php?element={$element}&amp;section=contacts&amp;find={$find}&amp;offset={$next_offset}&amp;sort_mode={$sort_mode}">{tr}next{/tr}</a>]
    {/if}
    {if $direct_pagination eq 'y'}
    <br />
    {section loop=$cant_pages name=foo}
    {assign var=selector_offset value=$smarty.section.foo.index|times:$maxRecords}
    <a class="prevnext" href="tiki-webmail_contacts.php?element={$element}&amp;section=contacts&amp;find={$find}&amp;offset={$selector_offset}&amp;sort_mode={$sort_mode}">
    {$smarty.section.foo.index_next}</a>&nbsp;
    {/section}
    {/if}
  </div>

</div>

{/strip}
</body>
</html>
