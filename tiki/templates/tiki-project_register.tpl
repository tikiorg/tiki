{* Register a Tiki based Project
   $Header: /cvsroot/tikiwiki/tiki/templates/tiki-project_register.tpl,v 1.2 2005-01-22 22:56:24 mose Exp $ 
   Damian aka Damosoft *}

<h3>{tr}Project Registration{/tr}</h3>

{if $save eq 'true'}
<div class="cbox">
<div class="cbox-data">
<p>{tr}Your project was successfully submitted.  It may be subject
to approval. Once available, you can create/link your project objects
to your project page.{/tr}</p>
<p><a href="tiki-list_projects.php">{tr}List all projects{/tr}</a></p>
</div>
</div>
{/if}

<div class="cbox">
<div class="cbox-data">
<p>{tr}Take care when you register your project, the project name
cannot be changed afterwards. Please also provide an accurate 
project description. If you havent worked out these details please
do so before your register the project.{/tr}</p>
{if $feature_project_user_cats eq 'y'}
<p>{tr}Please choose your project categories carefully. You can
however change this later on your project admin panel.{/tr}</p>
{/if}
<p>{tr}All project names are converted to lowercase.{/tr}</p>
</div>
<div class="cbox-data">
<form action="tiki-project_register.php" method="post">
<table>
<tr class="formcolor">
<td>{tr}Project Name:{/tr}</td>
  <td><input type="text" name="projectName" value="" size="40" /></td>
</tr><tr class="formcolor">
<td>{tr}Description:{/tr}</td>
  <td><textarea name="projectDescription" cols="60" rows="5"></textarea></td>
</tr>
{if $tiki_p_categorise eq 'y'}
{include file=categorize.tpl}
{/if}
<tr class="formcolor"><td colspan="2">&nbsp;</td></tr>
<tr class="formcolor"><td>&nbsp;</td>
<td><input type="submit" value="Register" name="save" /></td>
</tr>
</table>
</form>
</div>
</div>
