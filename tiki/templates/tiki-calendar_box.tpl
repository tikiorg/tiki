<div class='opaque'>
<div class='box-title'>{$cellhead}
{if $cellprio}<span class='calprio{$cellprio}' id='calprio'>{$cellprio}</span>{/if}
</div>
{if $cellcalname}<div class='box-title'><b>{$cellcalname}</b></div>{/if}
{if $celllocation}<div class='box-title'><b>{$celllocation}</b></div>{/if}
{if $cellcategory}<div class='box-title'><b>{$cellcategory}</b></div>{/if}
<div class='box-data'><b>{$cellname}</b><br />{$celldescription}{$cellextra}</div>
</div>
