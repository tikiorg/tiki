<h2>Community Currencies Registry Guidelines</h2>

{if $msg}<div class="simplebox">{$msg}</div>{/if}

<h2><a href="cc.php?page=currencies&new">Create Currency</a></h2><ul>
<li>Any registered account holder can create currencies; you only need to set:</li><ol>
<li>Currency id: this is the codename used for the currency, preferably short and memorable.  Within a cc registry, cc ids are unique.</li>
<li>Currency Name: a more freeform label to describe a currency, with no more use than for display in some reports.</li>
<li>Description: a brief introduction about that - purpose, unit, limitations, values ....</li>
<li>Requires approval: do people need your agreement to register to use this currency?</li>
<li>Listed Publicly: set currency as visible or hidden in the public currency list.  Hiding gives no guarantee the id will not become known, but it helps preserve private groups from casual
access.</li></ol>
<li>Register Owner: auto-register (or not) the currency owner.</li>
<li>Owner: the registry administrator can create a cc and specify an account holder as currency owner.</li>
</ul>

<h2><a href="cc.php?page=transactions&new">Add Transaction</a></h2><ul>
<li>That's the most common operation in the system, the feeding of the ledger. Parameters are :</li>
<ol>
<li>Currency: specify the cc you want to use for the transaction.</li>
<li> enter acct id to recceive transfer. </li> </ol>
<li> amount to transfer. </li>
<li> any useful text. </li>
<li>Click to submit. </li>
<li>Balances and records are updated. </li>
<li>Confirmation will display, or any reasons for failure. </li>

</ul>

<h2><a href="cc.php?page=currencies">Register for cc</a> </h2><ul>
<li>List of available currencies.
</li><li>Click to join a system.
</li><li>Confirmation of registration.
</li></ul>

<h2><a href="cc.php?page=currencies&own">cc administration</a></h2>
<ul>
<li>Presents a menu of all cc this user administers / owns.</li>
</ul>

