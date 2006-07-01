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
-- Name: point_table; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE point_table (
    gid serial NOT NULL,
    nom text,
    url text,
    author text,
    created timestamp without time zone,
    modified timestamp without time zone,
    the_geom geometry,
    CONSTRAINT enforce_dims_the_geom CHECK ((ndims(the_geom) = 2)),
    CONSTRAINT enforce_geotype_the_geom CHECK (((geometrytype(the_geom) = 'POINT'::text) OR (the_geom IS NULL))),
    CONSTRAINT enforce_srid_the_geom CHECK ((srid(the_geom) = -1))
);


ALTER TABLE public.point_table OWNER TO postgres;

--
-- Name: chambre_hote_gid_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval(pg_catalog.pg_get_serial_sequence('point_table', 'gid'), 10, true);


--
-- Data for Name: point_table; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY point_table (gid, nom, url, author, created, modified, the_geom) FROM stdin;
1	le merlet	\N	\N	\N	\N	010100000023BE873CE4CF25415521D8BDD1723D41
2	le viala	\N	\N	\N	\N	0101000000790E5BD92D192641616C3F975B573D41
3	le mas du cros	\N	\N	\N	\N	010100000028A4A83E4F1B2641920EA4ED985B3D41
4	la lézardière	\N	\N	\N	\N	010100000056B40F5473252641F2DC0E11576A3D41
5	la maison de juliette	\N	\N	\N	\N	01010000002BFBF63A8A1826418F5D961FFE643D41
6	gentiâne	\N	\N	\N	\N	01010000006F85B0188313264143A1807D95613D41
7	l'oustaou de joséphine	\N	\N	\N	\N	01010000002F14251DC21C264114948A3D96533D41
8	les cessenades	\N	\N	\N	\N	0101000000B8AC8B36C303264122EEBEDB2E503D41
9	vimbouches	\N	\N	\N	\N	01010000004EA4F1CB40F92541FF6C5940B6553D41
10	le salson	\N	\N	\N	\N	0101000000BB94B630F8FC2541ECE1276B1A583D41
\.


--
-- Name: chambre_hote_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY point_table
    ADD CONSTRAINT chambre_hote_pkey PRIMARY KEY (gid);


--
-- Name: point_table; Type: ACL; Schema: public; Owner: postgres
--

REVOKE ALL ON TABLE point_table FROM PUBLIC;
REVOKE ALL ON TABLE point_table FROM postgres;
GRANT ALL ON TABLE point_table TO postgres;
GRANT INSERT,SELECT,UPDATE,DELETE ON TABLE point_table TO "www-data";


--
-- PostgreSQL database dump complete
--

