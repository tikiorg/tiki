<h2>How to use the cc Registry</h2>

{if $msg}<div class="simplebox">{$msg}</div>{/if}
<br>
<h2><a href="cc.php?page=currencies&new">Create Currency</a></h2>
<p>Any registered account holder can create currencies; you only need to set:</p>
<ul>
<li>Currency id: this is the codename used for the currency, preferably short and memorable.  Within a cc registry, cc ids are unique.</li>
<li>Currency Name: a more freeform label to describe a currency, sometimes used in reports.</li>
<li>Description: a brief introduction about that - purpose, unit, limitations, values ....</li>
<li>Requires approval: do people need your agreement to register to use this currency?</li>
<li>Listed Publicly: set currency as visible or hidden in the public currency list.  Hiding gives no guarantee the id will not become known, but it helps preserve private groups from casual access.</li>
<li>Register Owner: auto-register (or not) the currency owner.</li>
<li>Owner: the registry administrator can create a cc and specify an account holder as currency owner.</li>
</ul>
<br>
<h2><a href="cc.php?page=transactions&new">Add Transaction</a></h2>

<p>This is the most common operation in the system, the feeding of the ledger. Parameters are :</p>
<ul>
<li>Any useful text. </li>
<li>Account id to recceive transfer. </li> 
<li>Amount to transfer. </li>
<li>Currency: specify the cc you want to use for the transaction.</li>
<li>Click to submit. </li>
<br>
<li>Balances and records are updated. </li>
<li>Confirmation will display, or any reasons for failure. </li>

</ul>
<br>
<p>Balances and records are updated. Confirmation will display, or any reasons for failure. </p>


<p> <b><i>*** warning - screen refresh after posting may cause the transaction to replicate ****</i></b> </p>
<br>
<h2><a href="cc.php?page=currencies">Register for cc</a> </h2>
<ul>
<li>List of available currencies.</li>
<li>Click to join a system.</li>
<li>Confirmation of registration.</li>
</ul>
<br>
<h2><a href="cc.php?page=currencies&own">cc administration</a></h2>
<ul>
<li>Presents a menu of all cc this user administers / owns.</li>
</ul>

