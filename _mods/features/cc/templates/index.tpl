<h2>How to use the cc Registry</h2>

{if $msg}<div class="simplebox">{$msg}</div>{/if}
<br>
<h2><a href="cc.php?page=currencies&new">Create Currency</a></h2>
<p>Any registered account holder can create currencies; you only need to set:</p>
<ul>
<li>cc id: this is the codename used for the currency, preferably short and memorable.  Within a cc registry, cc ids are unique.</li>
<li>cc name: a more freeform label to describe a currency, sometimes used in reports.</li>
<li>description: a brief introduction about this cc - purpose, unit, limitations, values ....</li>
<!-- li>requires approval: do people need your agreement to register to use this currency?</li>  -->
<li>listed publicly: set currency as visible or hidden in the public currency list.  Hiding gives no guarantee the id will not become known, but it helps preserve private groups from casual access.</li>
<li>register owner: auto-register (or not) the currency owner.</li>
<li>owner: the registry administrator can create a cc and specify any account holder as currency owner.</li>
</ul>
<br>
<h2><a href="cc.php?page=transactions&new">Add Transaction</a></h2>

<p>This is the most common operation in the system.  Parameters are :</p>
<ul>
<li>any useful text. </li>
<li>account id to receive transfer. </li> 
<li>amount to transfer. </li>
<li><em>select cc</em>: specify the cc you want to use for the transaction.</li>
<br>
<ul>and <strong>click</strong> to record. </ul>
<br>
<li>balances and records are updated. </li>
<li>confirmation will display, or any reasons for failure. </li>
</ul>
<br>
<p> <b><i>*** warning - screen refresh after posting may cause the transaction to replicate ****</i></b> </p>
<br>

<h2><a href="cc.php?page=currencies">Register for cc</a> </h2>
<p>You can only use a currency when you have registered for it.</p>
<ul>
<li>list of available currencies.</li>
<li>click "register" to join a system.</li>
<li>confirmation of registration.</li>
</ul>
<br>
<h2><a href="cc.php?page=currencies&own">cc administration</a></h2>
<p>This lists all the cc administered ("owned") by this user.</p>

