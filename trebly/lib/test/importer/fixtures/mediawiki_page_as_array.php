<?php

// the expected output of TikiImporter_Wiki_Mediawiki::parseData(), used to test
// the behavior of the TikiImporter_Wiki::insertPage()
global $page;
$page = array('revisions' => array(
                  0 => array(
                      'minor' => false,
                      'lastModif' => 1123887605,
                      'user' => 'Igordebraga',
                      'ip' => '0.0.0.0',
                      'comment' => '',
                      'data' => 'lista de __redes de ensino__, com diversas ((escola))s e universidades ao redor do brasil.


*((COC))
*((Promove))
*((Rede_Pitágoras|Rede Pitágoras))
*((UNIVERSO)){{esboço-educação}} ((Categoria:Educação))

                  '),
                  1 => array(
                      'minor' => false,
                      'lastModif' => 1130805792,
                      'user' => 'Martiniano Hilário',
                      'ip' => '0.0.0.0',
                      'comment' => '',
                      'data' => '{{Portal de Educação}}Lista de __redes de ensino__, com diversas ((escola))s e universidades ao redor do Brasil.


*((COC))
*((Promove))
*((Rede_Pitágoras|Rede Pitágoras))
*((UNIVERSO)){{mínimo}} ((Categoria:Educação))

                  '),
                  2 => array(
                      'minor' => false,
                      'lastModif' => 1137345461,
                      'ip' => '201.24.14.135',
                      'user' => 'anonymous',
                      'comment' => '',
                      'data' => '{{Portal de Educação}}Lista de __redes de ensino__, com diversas ((escola))s e universidades ao redor do Brasil.


*((COC))
*((Promove))
*((Rede_Pitágoras|Rede Pitágoras))
*((UNIVERSO))
*((Rede_Salesiana|Rede Salesiana)){{mínimo}} ((Categoria:Educação))


                  '),
                  3 => array(
                      'minor' => false,
                      'lastModif' => 1138451599,
                      'ip' => '201.24.54.212',
                      'user' => 'anonymous',
                      'comment' => '',
                      'data' => '{{Portal de Educação}}Lista de __redes de ensino__, com diversas ((escola))s e universidades ao redor do Brasil.


*((COC))
*((Promove))
*((Rede_Pitágoras|Rede Pitágoras))
*((UNIVERSO))
*((Rede_Salesiana_de_Escolas|Rede Salesiana de Escolas)){{mínimo}} ((Categoria:Educação))


                  '),
                  4 => array(
                      'minor' => false,
                      'lastModif' => 1145870372,
                      'user' => 'Martiniano Hilário',
                      'ip' => '0.0.0.0',
                      'comment' => 'recat',
                      'data' => '{{Portal de Educação}}Lista de __redes de ensino__, com diversas ((escola))s e universidades ao redor do Brasil.


*((COC))
*((Promove))
*((Rede_Pitágoras|Rede Pitágoras))
*((UNIVERSO))
*((Rede_Salesiana_de_Escolas|Rede Salesiana de Escolas)){{mínimo}} ((Categoria:escolas))


                  '),
                  5 => array(
                      'minor' => 1,
                      'lastModif' => 1150287386,
                      'user' => 'Rui Silva',
                      'ip' => '0.0.0.0',
                      'comment' => 'tirado link para pág. a eliminar',
                      'data' => '{{Portal de Educação}}Lista de __redes de ensino__, com diversas ((escola))s e universidades ao redor do Brasil.


*((COC))
*((Rede_Pitágoras|Rede Pitágoras))
*((UNIVERSO))
*((Rede_Salesiana_de_Escolas|Rede Salesiana de Escolas))((Categoria:escolas))


                  '),
                  6 => array(
                      'minor' => false,
                      'lastModif' => 1226515380,
                      'user' => 'LijeBot',
                      'ip' => '0.0.0.0',
                      'comment' => 'clean up, Replaced: [Categoria:e → [[Categoria:E',
                      'data' => '{{Portal de Educação}}Lista de __redes de ensino__, com diversas ((escola))s e universidades ao redor do Brasil.


*((COC))
*((Rede_Pitágoras|Rede Pitágoras))
*((UNIVERSO))
*((Rede_Salesiana_de_Escolas|Rede Salesiana de Escolas))((Categoria:Escolas))


                  '),
                  7 => array(
                      'minor' => false,
                      'lastModif' => 1234172034,
                      'ip' => '189.41.74.5',
                      'user' => 'anonymous',
                      'comment' => '',
                      'data' => '{{Portal de Educação}}Lista de __redes de ensino__, com diversas ((escola))s e universidades ao redor do Brasil.


*((COC))
*((Rede_Pitágoras|Rede Pitágoras))
*((UNIVERSO))
*((Rede_Salesiana_de_Escolas|Rede Salesiana de Escolas))
*((MACRO_Clube_de_Ensino|MACRO Clube de Ensino))((Categoria:Escolas))


                  '),
              ),
              'name' => 'Redes de ensino'
);
