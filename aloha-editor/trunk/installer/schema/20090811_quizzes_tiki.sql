#pkdille
ALTER TABLE tiki_quizzes DROP PRIMARY KEY , ADD PRIMARY KEY (`quizId`);
ALTER TABLE tiki_quizzes DROP COLUMN `nVersion`;
