<?php

interface TikiDb_ErrorHandler
{
	function handle( TikiDb $db, $query, $values, $result );
}
