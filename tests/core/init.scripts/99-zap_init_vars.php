<?php
/**
 * \file
 * $Header: /cvsroot/tikiwiki/tiki/tests/core/init.scripts/99-zap_init_vars.php,v 1.1 2003-08-22 19:04:40 zaufi Exp $
 *
 * \brief Forget db info so that malicious PHP may not get password etc.
 */

$host_tiki = NULL;
$user_tiki = NULL;
$pass_tiki = NULL;
$dbs_tiki = NULL;

unset ($host_map);
unset ($api_tiki);
unset ($db_tiki);
unset ($host_tiki);
unset ($user_tiki);
unset ($pass_tiki);
unset ($dbs_tiki);

?>
