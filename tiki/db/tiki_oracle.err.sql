CREATE INDEX heading_tiki_articles on tiki_articles (heading)
ORA-02327: cannot create index on expression with datatype LOB

CREATE INDEX body_tiki_articles on tiki_articles (body)
ORA-02327: cannot create index on expression with datatype LOB

CREATE INDEX data_tiki_blog_posts on tiki_blog_posts (data)
ORA-02327: cannot create index on expression with datatype LOB

CREATE INDEX description_tiki_blogs on tiki_blogs (description)
ORA-02327: cannot create index on expression with datatype LOB

CREATE INDEX data_tiki_comments on tiki_comments (data)
ORA-02327: cannot create index on expression with datatype LOB

CREATE INDEX question_tiki_faq_questions on tiki_faq_questions (question)
ORA-02327: cannot create index on expression with datatype LOB

CREATE INDEX answer_tiki_faq_questions on tiki_faq_questions (answer)
ORA-02327: cannot create index on expression with datatype LOB

CREATE INDEX description_tiki_faqs on tiki_faqs (description)
ORA-02327: cannot create index on expression with datatype LOB

CREATE INDEX description_tiki_files on tiki_files (description)
ORA-02327: cannot create index on expression with datatype LOB

CREATE INDEX description_tiki_galleries on tiki_galleries (description)
ORA-02327: cannot create index on expression with datatype LOB

CREATE INDEX description_tiki_images on tiki_images (description)
ORA-02327: cannot create index on expression with datatype LOB

CREATE TABLE tiki_language (
  source blob CONSTRAINT nn_source NOT NULL,
  lang char(2) DEFAULT '' CONSTRAINT nn_lang02 NOT NULL,
  tran blob,
  CONSTRAINT pk_tiki_language PRIMARY KEY (source, lang)
)
ORA-02329: column of datatype LOB cannot be unique or a primary key

CREATE INDEX data_tiki_pages on tiki_pages (data)
ORA-02327: cannot create index on expression with datatype LOB

CREATE TABLE tiki_untranslated (
  id number(14) CONSTRAINT nn_id NOT NULL,
  source blob CONSTRAINT nn_source02 NOT NULL,
  lang char(2) DEFAULT '' CONSTRAINT nn_lang04 NOT NULL,
  CONSTRAINT pk_tiki_untranslated PRIMARY KEY (source, lang),
  CONSTRAINT id UNIQUE (id)
)
ORA-02329: column of datatype LOB cannot be unique or a primary key
