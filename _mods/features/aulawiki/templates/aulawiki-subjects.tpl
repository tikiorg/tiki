<p class="pagetitle">{tr}Subject Administration{/tr}</p>
<form name="form1" method="post" action="aulawiki-subjects.php">
  <input name="id" type="hidden" id="id" value="{$subject.subjectId}"> 
  <table class="normal">
    <tr> 
      <td class="formcolor" width="30%"><label for="code">{tr}Code{/tr}</label></td>
      <td class="formcolor" width="70%"> <input name="code" type="text" id="code" value="{$subject.code}" size="30" maxlength="30"> 
      </td>
    </tr>
    <tr> 
      <td class="formcolor"><label for="name">{tr}Name{/tr}</label></td>
      <td class="formcolor"><input name="name" type="text" id="name" value="{$subject.name}" size="60" maxlength="100"></td>
    </tr>
    <tr> 
      <td class="formcolor" ><label for="desc">{tr}Description{/tr}</label></td>
      <td class="formcolor"><textarea name="desc" id="desc" size="60" cols="60" rows="4">{$subject.description}</textarea></td>
    </tr>
     <tr> 
      <td class="formcolor" ><label for="studies">{tr}Studies{/tr}</label></td>
      <td class="formcolor">
      
      <select name="studies" id="studies">
	      {section name=i loop=$studies}
	      	<option value="{$studies[i].id}" {if $studies[i].id eq $subject.studieId}selected{/if}>{$studies[i].name}</option>
	      {/section}
      </select>

      </td>
    </tr>
 
     <tr> 
      <td class="formcolor" colspan="2"><center><input class="edubutton" type="submit" name="send" value="{tr}Send{/tr}"></center></td>
    </tr>
  </table>
</form>

<br/>
<table class="findtable">
<tr>
   <td>
   <form method="get" action="aulawiki-subjects.php">
     <label for="find">{tr}Find{/tr}</label>
     <input type="text" name="find" value="{$find|escape}" />
     <input class="edubutton" type="submit" value="{tr}find{/tr}" name="search" />
		 <label for="numrows">{tr}Number of displayed rows{/tr}</label>
		 <input type="text" size="4" name="numrows" value="{$numrows|escape}">
     <input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
   </form>
   </td>
</tr>
</table>


<table class="normal" width="100%">
    <tr> 
      <td class="heading" width="10%">ID</td>
      <td class="heading" width="10%">{tr}Code{/tr}</td>
      <td class="heading" width="40%"><a class="tableheading" href="aulawiki-subject.php?offset={$offset}&amp;numrows={$numrows}&amp;sort_mode={if $sort_mode eq 'nombre_desc'}nombre_asc{else}nombre_desc{/if}">{tr}Name{/tr}</a></td>
      <td class="heading" width="50%">{tr}Description{/tr}</td>
      <td class="heading"> </td>
      <td class="heading"> </td>
    </tr>

{section name=i loop=$subjects}
    {cycle values="odd,even" assign="parImpar"}
    <tr> 
      <td class="{$parImpar}"><a href="./aulawiki-homeasg.php?idsubject={$subjects[i].idsubject}">{$subjects[i].subjectId}</a></td>
      <td class="{$parImpar}">{$subjects[i].code}</td>
      <td class="{$parImpar}">{$subjects[i].name}</td>
      <td class="{$parImpar}">{$subjects[i].description}</td>
      <td class="{$parImpar}"> <a class="link" href="aulawiki-subjects.php?edit={$subjects[i].subjectId}">
           <img src='img/icons/edit.gif' border='0' alt='Editar' title='Editar' /></a></td>
      <td class="{$parImpar}"><a class="link" href="aulawiki-subjects.php?delete={$subjects[i].subjectId}">
      	   <img src='img/icons2/delete.gif' border='0' alt='Borrar' title='Borrar' /></a>
      </td>
    </tr>
{/section}
</table>


<br/>
<div class="mini">
{if $prev_offset >= 0}
[<a class="prevnext" href="aulawiki-subjects.php?find={$find}&amp;offset={$prev_offset}&amp;numrows={$numrows}&amp;sort_mode={$sort_mode}">{tr}prev{/tr}</a>] 
{/if}
{tr}Page{/tr}: {$actual_page}/{$cant_pages}
{if $next_offset >= 0}
 [<a class="prevnext" href="aulawiki-subjects.php?find={$find}&amp;offset={$next_offset}&amp;numrows={$numrows}&amp;sort_mode={$sort_mode}">{tr}next{/tr}</a>]
{/if}
{if $direct_pagination eq 'y'}
<br/>
{section loop=$cant_pages name=foo}
{assign var=selector_offset value=$smarty.section.foo.index|times:$maxRecords}
<a class="prevnext" href="aulawiki-subjects.php?find={$find}&amp;offset={$selector_offset}&amp;numrows={$numrows}&amp;sort_mode={$sort_mode}">
{$smarty.section.foo.index_next}</a>
{/section}
{/if}

</div>
</div>