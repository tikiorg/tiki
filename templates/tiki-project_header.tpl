<div class="project-header">
<a class="linkbut" href="tiki-project.php?projectId={$project.projectId}">Summary</a> 
<a class="linkbut" href="tiki-project.php?projectId={$project.projectId}&admin=yes">Admin</a> 
{* If wiki page exists of same project name then we have a Home Page :) *}
<a class="linkbut" href="tiki-index.php?page={$feature_project_home_prefix}{$project.projectName}">Home Page</a> 
{* Now list project objects *}
<br />
<br />
{if $adminview eq 'y'}
<a href="tiki-edit_project.php?projectId={$project.projectId}" class="linkbut">{tr}Edit Project{/tr}</a>
{if $feature_blogs eq 'y'}
<a href="tiki-edit_project.php?projectId={$project.projectId}&add=blog" class="linkbut">{tr}Add Blog{/tr}</a>
{/if}
{if $feature_articles eq 'y'}
<a href="tiki-edit_project.php?projectId={$project.projectId}&add=arttopic" class="linkbut">{tr}Add Articles{/tr}</a>
{/if}
{if $feature_calendar eq 'y'}
<a href="tiki-edit_project.php?projectId={$project.projectId}&add=calendar" class="linkbut">{tr}Add Calendar{/tr}</a>
{/if}
{if $feature_faqs eq 'y'}
<a href="tiki-edit_project.php?projectId={$project.projectId}&add=faq" class="linkbut">{tr}Add FAQ{/tr}</a>
{/if}
{if $feature_file_galleries eq 'y'}
<a href="tiki-edit_project.php?projectId={$project.projectId}&add=filegal" class="linkbut">{tr}Add File Gallery{/tr}</a>
{/if}
{if $feature_forums eq 'y'}
<a href="tiki-edit_project.php?projectId={$project.projectId}&add=forums" class="linkbut">{tr}Add Forum{/tr}</a>
{/if}
{if $feature_galleries eq 'y'}
<a href="tiki-edit_project.php?projectId={$project.projectId}&add=imagegal" class="linkbut">{tr}Add Image Gallery{/tr}</a>
{/if}
{if $feature_newsletters eq 'y'}
<a href="tiki-edit_project.php?projectId={$project.projectId}&add=newsletter" class="linkbut">{tr}Add Newsletter{/tr}</a>
{/if}
<a href="tiki-edit_project.php?projectId={$project.projectId}&add=url" class="linkbut">{tr}Add URL{/tr}</a>
<br />
{/if}
</div>
