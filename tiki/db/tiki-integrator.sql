--
-- Table structure for table 'tiki_integrator_repositories'
--

CREATE TABLE tiki_integrator_repositories (
  repID int(11) NOT NULL auto_increment,
  name varchar(255) NOT NULL default '',
  path varchar(255) NOT NULL default '',
  start_page varchar(255) NOT NULL default '',
  css_file varchar(255) NOT NULL default '',
  visibility char(1) NOT NULL default 'y',
  description text NOT NULL,
  PRIMARY KEY  (repID)
) TYPE=MyISAM;

--
-- Dumping data for table 'tiki_integrator_repositories'
--


INSERT INTO tiki_integrator_repositories VALUES (1,'Doxygened (1.3.4) Documentation','unexisted','index.html','doxygen.css','n','Use this repository as rule source for all your repositories based on doxygened docs. To setup yours just add new repository and copy rules from this repository :)');

--
-- Table structure for table 'tiki_integrator_rules'
--

CREATE TABLE tiki_integrator_rules (
  ruleID int(11) NOT NULL auto_increment,
  repID int(11) NOT NULL default '0',
  srch blob NOT NULL,
  repl blob NOT NULL,
  type char(1) NOT NULL default 'n',
  casesense char(1) NOT NULL default 'y',
  rxmod varchar(20) NOT NULL default '',
  description text NOT NULL,
  PRIMARY KEY  (ruleID),
  KEY repID (repID)
) TYPE=MyISAM;

--
-- Dumping data for table 'tiki_integrator_rules'
--

INSERT INTO tiki_integrator_rules VALUES (1,1,'<\\!DOCTYPE','<!-- Commented by Tiki integrator <!DOCTYPE','y','n','i','Start comment from the begining of document');
INSERT INTO tiki_integrator_rules VALUES (2,1,'</html>','','y','n','','Remove </html>');
INSERT INTO tiki_integrator_rules VALUES (3,1,'<body>','-->','y','n','i','End of comment just after <body>');
INSERT INTO tiki_integrator_rules VALUES (4,1,'</body>','','y','n','i','Remove </body>');
INSERT INTO tiki_integrator_rules VALUES (5,1,'img src=\"','img src=\"/{path}/','y','n','','Fix images path');
INSERT INTO tiki_integrator_rules VALUES (6,1,'href=\"','href=\"tiki-integrator.php?repID=<N>&file=','y','n','','Relace links to integrator. Attention! Don not forget to replace <N> with ID of your repository!!!');

