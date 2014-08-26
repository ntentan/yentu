--
-- PostgreSQL database dump
--

-- Dumped from database version 9.3.4
-- Dumped by pg_dump version 9.3.4
-- Started on 2014-08-26 18:30:39 GMT

SET statement_timeout = 0;
SET lock_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SET check_function_bodies = false;
SET client_min_messages = warning;

--
-- TOC entry 186 (class 3079 OID 12670)
-- Name: plpgsql; Type: EXTENSION; Schema: -; Owner: -
--

CREATE EXTENSION IF NOT EXISTS plpgsql WITH SCHEMA pg_catalog;


--
-- TOC entry 2953 (class 0 OID 0)
-- Dependencies: 186
-- Name: EXTENSION plpgsql; Type: COMMENT; Schema: -; Owner: -
--

COMMENT ON EXTENSION plpgsql IS 'PL/pgSQL procedural language';


SET search_path = public, pg_catalog;

SET default_with_oids = false;

--
-- TOC entry 170 (class 1259 OID 96939)
-- Name: api_keys; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE api_keys (
    api_key_id integer NOT NULL,
    user_id integer NOT NULL,
    active boolean NOT NULL,
    key character varying(512) NOT NULL,
    secret character varying(512) NOT NULL
);


--
-- TOC entry 171 (class 1259 OID 96945)
-- Name: api_keys_api_key_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE api_keys_api_key_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- TOC entry 2954 (class 0 OID 0)
-- Dependencies: 171
-- Name: api_keys_api_key_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE api_keys_api_key_id_seq OWNED BY api_keys.api_key_id;


--
-- TOC entry 172 (class 1259 OID 96947)
-- Name: audit_trail; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE audit_trail (
    audit_trail_id integer NOT NULL,
    user_id integer NOT NULL,
    item_id integer NOT NULL,
    item_type character varying(64) NOT NULL,
    description character varying(4000) NOT NULL,
    audit_date timestamp without time zone NOT NULL,
    type numeric NOT NULL,
    data text
);


--
-- TOC entry 173 (class 1259 OID 96953)
-- Name: audit_trail_audit_trail_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE audit_trail_audit_trail_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- TOC entry 2955 (class 0 OID 0)
-- Dependencies: 173
-- Name: audit_trail_audit_trail_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE audit_trail_audit_trail_id_seq OWNED BY audit_trail.audit_trail_id;


--
-- TOC entry 174 (class 1259 OID 96955)
-- Name: audit_trail_data; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE audit_trail_data (
    audit_trail_data_id integer NOT NULL,
    audit_trail_id integer NOT NULL,
    data text
);


--
-- TOC entry 175 (class 1259 OID 96961)
-- Name: audit_trail_data_audit_trail_data_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE audit_trail_data_audit_trail_data_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- TOC entry 2956 (class 0 OID 0)
-- Dependencies: 175
-- Name: audit_trail_data_audit_trail_data_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE audit_trail_data_audit_trail_data_id_seq OWNED BY audit_trail_data.audit_trail_data_id;


--
-- TOC entry 176 (class 1259 OID 96963)
-- Name: keystore; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE keystore (
    keystore_id integer NOT NULL,
    key character varying(255) NOT NULL,
    value text
);


--
-- TOC entry 177 (class 1259 OID 96969)
-- Name: keystore_keystore_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE keystore_keystore_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- TOC entry 2957 (class 0 OID 0)
-- Dependencies: 177
-- Name: keystore_keystore_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE keystore_keystore_id_seq OWNED BY keystore.keystore_id;


--
-- TOC entry 178 (class 1259 OID 96971)
-- Name: permissions; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE permissions (
    permission_id integer NOT NULL,
    role_id integer NOT NULL,
    permission character varying(4000),
    value numeric NOT NULL,
    module character varying(4000)
);


--
-- TOC entry 179 (class 1259 OID 96977)
-- Name: permissions_permission_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE permissions_permission_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- TOC entry 2958 (class 0 OID 0)
-- Dependencies: 179
-- Name: permissions_permission_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE permissions_permission_id_seq OWNED BY permissions.permission_id;


--
-- TOC entry 180 (class 1259 OID 96979)
-- Name: roles; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE roles (
    role_id integer NOT NULL,
    role_name character varying(64)
);


--
-- TOC entry 181 (class 1259 OID 96982)
-- Name: roles_role_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE roles_role_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- TOC entry 2959 (class 0 OID 0)
-- Dependencies: 181
-- Name: roles_role_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE roles_role_id_seq OWNED BY roles.role_id;


--
-- TOC entry 182 (class 1259 OID 96984)
-- Name: users; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE users (
    user_id integer NOT NULL,
    user_name character varying(64) NOT NULL,
    password character varying(64) NOT NULL,
    role_id integer,
    first_name character varying(64) NOT NULL,
    last_name character varying(64) NOT NULL,
    other_names character varying(64),
    user_status numeric(1,0),
    email character varying(64) NOT NULL
);


--
-- TOC entry 183 (class 1259 OID 96987)
-- Name: users_user_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE users_user_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- TOC entry 2960 (class 0 OID 0)
-- Dependencies: 183
-- Name: users_user_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE users_user_id_seq OWNED BY users.user_id;


--
-- TOC entry 185 (class 1259 OID 100391)
-- Name: yentu_history_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE yentu_history_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- TOC entry 184 (class 1259 OID 100383)
-- Name: yentu_history; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE yentu_history (
    session character varying,
    version character varying,
    method character varying,
    arguments text,
    migration character varying,
    id integer DEFAULT nextval('yentu_history_id_seq'::regclass) NOT NULL
);


--
-- TOC entry 2790 (class 2604 OID 96989)
-- Name: api_key_id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY api_keys ALTER COLUMN api_key_id SET DEFAULT nextval('api_keys_api_key_id_seq'::regclass);


--
-- TOC entry 2791 (class 2604 OID 96990)
-- Name: audit_trail_id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY audit_trail ALTER COLUMN audit_trail_id SET DEFAULT nextval('audit_trail_audit_trail_id_seq'::regclass);


--
-- TOC entry 2792 (class 2604 OID 96991)
-- Name: audit_trail_data_id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY audit_trail_data ALTER COLUMN audit_trail_data_id SET DEFAULT nextval('audit_trail_data_audit_trail_data_id_seq'::regclass);


--
-- TOC entry 2793 (class 2604 OID 96992)
-- Name: keystore_id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY keystore ALTER COLUMN keystore_id SET DEFAULT nextval('keystore_keystore_id_seq'::regclass);


--
-- TOC entry 2794 (class 2604 OID 96993)
-- Name: permission_id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY permissions ALTER COLUMN permission_id SET DEFAULT nextval('permissions_permission_id_seq'::regclass);


--
-- TOC entry 2795 (class 2604 OID 96994)
-- Name: role_id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY roles ALTER COLUMN role_id SET DEFAULT nextval('roles_role_id_seq'::regclass);


--
-- TOC entry 2796 (class 2604 OID 96995)
-- Name: user_id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY users ALTER COLUMN user_id SET DEFAULT nextval('users_user_id_seq'::regclass);


--
-- TOC entry 2931 (class 0 OID 96939)
-- Dependencies: 170
-- Data for Name: api_keys; Type: TABLE DATA; Schema: public; Owner: -
--

COPY api_keys (api_key_id, user_id, active, key, secret) FROM stdin;
\.


--
-- TOC entry 2961 (class 0 OID 0)
-- Dependencies: 171
-- Name: api_keys_api_key_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('api_keys_api_key_id_seq', 1, false);


--
-- TOC entry 2933 (class 0 OID 96947)
-- Dependencies: 172
-- Data for Name: audit_trail; Type: TABLE DATA; Schema: public; Owner: -
--

COPY audit_trail (audit_trail_id, user_id, item_id, item_type, description, audit_date, type, data) FROM stdin;
\.


--
-- TOC entry 2962 (class 0 OID 0)
-- Dependencies: 173
-- Name: audit_trail_audit_trail_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('audit_trail_audit_trail_id_seq', 1, false);


--
-- TOC entry 2935 (class 0 OID 96955)
-- Dependencies: 174
-- Data for Name: audit_trail_data; Type: TABLE DATA; Schema: public; Owner: -
--

COPY audit_trail_data (audit_trail_data_id, audit_trail_id, data) FROM stdin;
\.


--
-- TOC entry 2963 (class 0 OID 0)
-- Dependencies: 175
-- Name: audit_trail_data_audit_trail_data_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('audit_trail_data_audit_trail_data_id_seq', 1, false);


--
-- TOC entry 2937 (class 0 OID 96963)
-- Dependencies: 176
-- Data for Name: keystore; Type: TABLE DATA; Schema: public; Owner: -
--

COPY keystore (keystore_id, key, value) FROM stdin;
\.


--
-- TOC entry 2964 (class 0 OID 0)
-- Dependencies: 177
-- Name: keystore_keystore_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('keystore_keystore_id_seq', 1, false);


--
-- TOC entry 2939 (class 0 OID 96971)
-- Dependencies: 178
-- Data for Name: permissions; Type: TABLE DATA; Schema: public; Owner: -
--

COPY permissions (permission_id, role_id, permission, value, module) FROM stdin;
\.


--
-- TOC entry 2965 (class 0 OID 0)
-- Dependencies: 179
-- Name: permissions_permission_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('permissions_permission_id_seq', 1, false);


--
-- TOC entry 2941 (class 0 OID 96979)
-- Dependencies: 180
-- Data for Name: roles; Type: TABLE DATA; Schema: public; Owner: -
--

COPY roles (role_id, role_name) FROM stdin;
\.


--
-- TOC entry 2966 (class 0 OID 0)
-- Dependencies: 181
-- Name: roles_role_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('roles_role_id_seq', 1, false);


--
-- TOC entry 2943 (class 0 OID 96984)
-- Dependencies: 182
-- Data for Name: users; Type: TABLE DATA; Schema: public; Owner: -
--

COPY users (user_id, user_name, password, role_id, first_name, last_name, other_names, user_status, email) FROM stdin;
\.


--
-- TOC entry 2967 (class 0 OID 0)
-- Dependencies: 183
-- Name: users_user_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('users_user_id_seq', 1, false);


--
-- TOC entry 2945 (class 0 OID 100383)
-- Dependencies: 184
-- Data for Name: yentu_history; Type: TABLE DATA; Schema: public; Owner: -
--

COPY yentu_history (session, version, method, arguments, migration, id) FROM stdin;
\N	20140826182510	\N	\N	\N	1
\.


--
-- TOC entry 2968 (class 0 OID 0)
-- Dependencies: 185
-- Name: yentu_history_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('yentu_history_id_seq', 1, true);


--
-- TOC entry 2799 (class 2606 OID 96997)
-- Name: api_keys_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY api_keys
    ADD CONSTRAINT api_keys_pkey PRIMARY KEY (api_key_id);


--
-- TOC entry 2801 (class 2606 OID 96999)
-- Name: audit_trail_audit_id_pk; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY audit_trail
    ADD CONSTRAINT audit_trail_audit_id_pk PRIMARY KEY (audit_trail_id);


--
-- TOC entry 2805 (class 2606 OID 97001)
-- Name: audit_trail_data_id_pk; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY audit_trail_data
    ADD CONSTRAINT audit_trail_data_id_pk PRIMARY KEY (audit_trail_data_id);


--
-- TOC entry 2807 (class 2606 OID 97003)
-- Name: keystore_key_key; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY keystore
    ADD CONSTRAINT keystore_key_key UNIQUE (key);


--
-- TOC entry 2809 (class 2606 OID 97005)
-- Name: keystore_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY keystore
    ADD CONSTRAINT keystore_pkey PRIMARY KEY (keystore_id);


--
-- TOC entry 2811 (class 2606 OID 97007)
-- Name: perm_id_pk; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY permissions
    ADD CONSTRAINT perm_id_pk PRIMARY KEY (permission_id);


--
-- TOC entry 2813 (class 2606 OID 97009)
-- Name: role_id_pk; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY roles
    ADD CONSTRAINT role_id_pk PRIMARY KEY (role_id);


--
-- TOC entry 2815 (class 2606 OID 97011)
-- Name: user_id_pk; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY users
    ADD CONSTRAINT user_id_pk PRIMARY KEY (user_id);


--
-- TOC entry 2817 (class 2606 OID 97013)
-- Name: user_name_uk; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY users
    ADD CONSTRAINT user_name_uk UNIQUE (user_name);


--
-- TOC entry 2819 (class 2606 OID 100390)
-- Name: yentu_history_pk; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY yentu_history
    ADD CONSTRAINT yentu_history_pk PRIMARY KEY (id);


--
-- TOC entry 2802 (class 1259 OID 100421)
-- Name: audit_trail_item_id_idx; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX audit_trail_item_id_idx ON audit_trail USING btree (item_id);


--
-- TOC entry 2803 (class 1259 OID 100420)
-- Name: audit_trail_item_type_idx; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX audit_trail_item_type_idx ON audit_trail USING btree (item_type);


--
-- TOC entry 2820 (class 2606 OID 97014)
-- Name: api_keys_user_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY api_keys
    ADD CONSTRAINT api_keys_user_id_fkey FOREIGN KEY (user_id) REFERENCES users(user_id);


--
-- TOC entry 2821 (class 2606 OID 97019)
-- Name: audit_trail_user_id_fk; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY audit_trail
    ADD CONSTRAINT audit_trail_user_id_fk FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE SET NULL;


--
-- TOC entry 2822 (class 2606 OID 97024)
-- Name: permissios_role_id_fk; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY permissions
    ADD CONSTRAINT permissios_role_id_fk FOREIGN KEY (role_id) REFERENCES roles(role_id) ON DELETE CASCADE;


--
-- TOC entry 2823 (class 2606 OID 97029)
-- Name: users_role_id_fk; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY users
    ADD CONSTRAINT users_role_id_fk FOREIGN KEY (role_id) REFERENCES roles(role_id) ON DELETE SET NULL;


-- Completed on 2014-08-26 18:30:39 GMT

--
-- PostgreSQL database dump complete
--

