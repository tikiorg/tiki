--  jonnyb: remove all category permissions so as to start again with 4.x style perms
DELETE FROM  `users_objectpermissions` WHERE  `objectType` =  'category';
