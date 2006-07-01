--
-- PostgreSQL database dump
--

SET client_encoding = 'LATIN1';
SET check_function_bodies = false;
SET client_min_messages = warning;

SET search_path = public, pg_catalog;

SET default_tablespace = '';

SET default_with_oids = true;

--
-- Name: Line_Table; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE "Line_Table" (
    gid serial NOT NULL,
    nom text,
    url text,
    author text,
    created timestamp without time zone,
    modified timestamp without time zone,
    the_geom geometry,
    CONSTRAINT enforce_dims_the_geom CHECK ((ndims(the_geom) = 2)),
    CONSTRAINT enforce_geotype_the_geom CHECK (((geometrytype(the_geom) = 'LINESTRING'::text) OR (the_geom IS NULL))),
    CONSTRAINT enforce_srid_the_geom CHECK ((srid(the_geom) = -1))
);


ALTER TABLE public."Line_Table" OWNER TO postgres;

--
-- Name: sentiers_gid_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval(pg_catalog.pg_get_serial_sequence('"Line_Table"', 'gid'), 1, false);


--
-- Data for Name: Line_Table; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY "Line_Table" (gid, nom, url, author, created, modified, the_geom) FROM stdin;
\.


--
-- Name: sentiers_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY "Line_Table"
    ADD CONSTRAINT sentiers_pkey PRIMARY KEY (gid);


--
-- Name: Line_Table; Type: ACL; Schema: public; Owner: postgres
--

REVOKE ALL ON TABLE "Line_Table" FROM PUBLIC;
REVOKE ALL ON TABLE "Line_Table" FROM postgres;
GRANT ALL ON TABLE "Line_Table" TO postgres;
GRANT INSERT,SELECT,UPDATE,DELETE ON TABLE "Line_Table" TO "www-data";


--
-- Name: sentiers_gid_seq; Type: ACL; Schema: public; Owner: postgres
--

REVOKE ALL ON TABLE sentiers_gid_seq FROM PUBLIC;
REVOKE ALL ON TABLE sentiers_gid_seq FROM postgres;
GRANT ALL ON TABLE sentiers_gid_seq TO postgres;
GRANT INSERT,SELECT,UPDATE,DELETE ON TABLE sentiers_gid_seq TO "www-data";


--
-- PostgreSQL database dump complete
--

