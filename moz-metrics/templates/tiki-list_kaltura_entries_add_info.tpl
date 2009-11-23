	{capture name=add_info}{strip}
	<div class='opaque'>
			<div class='box-title'><b>{tr}Additional Info{/tr}</b></div>
      			<div class='box-data'>

          			{if $item->description eq ''}
           				 {assign var=propval value="No Description"}
          			{else}
           				 {assign var=propval value=$item->description}
          			{/if}
           				 <b>Description</b>: {$propval}<br />
            
          			{if $item->duration eq ''}
          				 {assign var=propval value=0}
         			{else}
           				 {assign var=propval value=$item->duration}
          			{/if}
            			 <b>Duration</b>: {$propval}s<br />
            
          			{if $item->views eq ''}
            			 {assign var=propval value=0}
          			{else}
            			 {assign var=propval value=$item->views}
          			{/if}
            			 <b>Views</b>: {$propval}<br />
            
          			{if $item->plays eq ''}
            			 {assign var=propval value=0}
          			{else}
            			 {assign var=propval value=$item->plays}
          			{/if}
            			 <b>Plays</b>: {$propval}<br />

				</div>
	</div>
	{/strip}{/capture}