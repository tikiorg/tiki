<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

function map_query($map,$corx,$cory) {
$map_querymsg='<table class="normal">';
$map_querymsg.='<tr><td class="heading" colspan="2">'.tra('Query point:').' <b>'.$corx.' / '.$cory.'</b></td><tr>';
$query_point= ms_newpointObj();
$query_point->setXY($corx,$cory);
for ($j=0;$j<$map->numlayers;$j++)
{
  $my_layer=$map->getLayer($j);
  if ($my_layer->status==MS_ON) // We have a query
  {
    if (@$my_layer->queryByPoint($query_point,MS_MULTIPLE,0)==MS_SUCCESS)
    {
      $my_layer->open();
      $map_querymsg.='<tr><td class="heading" colspan="2">'.tra('layer:').' <b>'.$my_layer->name.'</b></td></tr>';
      for($k=0;$k<$my_layer->getNumResults();$k++)
      {       
        $my_resultcache=$my_layer->getResult($k);
        $my_shape=$my_layer->getShape($my_resultcache->tileindex,$my_resultcache->shapeindex);
        $my_array=$my_shape->values;
        $map_querymsg.='<tr><td class="heading" colspan="2"><small>'.tra('Record:').' '.$my_resultcache->shapeindex.'</small></td></tr>';
        $col=0;
        foreach ($my_array as $key => $value)
        {
          if ($col %2) {
            $map_querymsg.='<tr><td class="even"><small>'.$key.'</small></td><td class="even"><small>'.$value.'</small></td></tr>';
          } else {
            $map_querymsg.='<tr><td class="odd"><small>'.$key.'</small></td><td class="odd"><small>'.$value.'</small></td></tr>';
          }
          $col++;            
        }
      }
      $my_layer->close();
    }   
  }
}    
$map_querymsg.="</table>";
return($map_querymsg);
}
?>