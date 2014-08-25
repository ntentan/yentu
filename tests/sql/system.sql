CREATE TABLE api_keys (
    api_key_id integer NOT NULL,
    user_id integer NOT NULL,
    active boolean NOT NULL,
    key character varying(512) NOT NULL,
    secret character varying(512) NOT NULL
);


CREATE SEQUENCE api_keys_api_key_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;

ALTER SEQUENCE api_keys_api_key_id_seq OWNED BY api_keys.api_key_id;

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
-- TOC entry 245 (class 1259 OID 54920)
-- Name: audit_trail_audit_trail_id_seq; Type: SEQUENCE; Schema: system; Owner: -
--

CREATE SEQUENCE audit_trail_audit_trail_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- TOC entry 3131 (class 0 OID 0)
-- Dependencies: 245
-- Name: audit_trail_audit_trail_id_seq; Type: SEQUENCE OWNED BY; Schema: system; Owner: -
--

ALTER SEQUENCE audit_trail_audit_trail_id_seq OWNED BY audit_trail.audit_trail_id;


--
-- TOC entry 246 (class 1259 OID 54922)
-- Name: audit_trail_data; Type: TABLE; Schema: system; Owner: -
--

CREATE TABLE audit_trail_data (
    audit_trail_data_id integer NOT NULL,
    audit_trail_id integer NOT NULL,
    data text
);


--
-- TOC entry 247 (class 1259 OID 54928)
-- Name: audit_trail_data_audit_trail_data_id_seq; Type: SEQUENCE; Schema: system; Owner: -
--

CREATE SEQUENCE audit_trail_data_audit_trail_data_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- TOC entry 3132 (class 0 OID 0)
-- Dependencies: 247
-- Name: audit_trail_data_audit_trail_data_id_seq; Type: SEQUENCE OWNED BY; Schema: system; Owner: -
--

ALTER SEQUENCE audit_trail_data_audit_trail_data_id_seq OWNED BY audit_trail_data.audit_trail_data_id;


--
-- TOC entry 248 (class 1259 OID 54930)
-- Name: keystore; Type: TABLE; Schema: system; Owner: -
--

CREATE TABLE keystore (
    keystore_id integer NOT NULL,
    key character varying(255) NOT NULL,
    value text
);


--
-- TOC entry 249 (class 1259 OID 54936)
-- Name: keystore_keystore_id_seq; Type: SEQUENCE; Schema: system; Owner: -
--

CREATE SEQUENCE keystore_keystore_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- TOC entry 3133 (class 0 OID 0)
-- Dependencies: 249
-- Name: keystore_keystore_id_seq; Type: SEQUENCE OWNED BY; Schema: system; Owner: -
--

ALTER SEQUENCE keystore_keystore_id_seq OWNED BY keystore.keystore_id;


--
-- TOC entry 250 (class 1259 OID 54938)
-- Name: permissions; Type: TABLE; Schema: system; Owner: -
--

CREATE TABLE permissions (
    permission_id integer NOT NULL,
    role_id integer NOT NULL,
    permission character varying(4000),
    value numeric NOT NULL,
    module character varying(4000)
);


--
-- TOC entry 251 (class 1259 OID 54944)
-- Name: permissions_permission_id_seq; Type: SEQUENCE; Schema: system; Owner: -
--

CREATE SEQUENCE permissions_permission_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- TOC entry 3134 (class 0 OID 0)
-- Dependencies: 251
-- Name: permissions_permission_id_seq; Type: SEQUENCE OWNED BY; Schema: system; Owner: -
--

ALTER SEQUENCE permissions_permission_id_seq OWNED BY permissions.permission_id;


--
-- TOC entry 252 (class 1259 OID 54946)
-- Name: roles; Type: TABLE; Schema: system; Owner: -
--

CREATE TABLE roles (
    role_id integer NOT NULL,
    role_name character varying(64)
);


--
-- TOC entry 253 (class 1259 OID 54949)
-- Name: roles_role_id_seq; Type: SEQUENCE; Schema: system; Owner: -
--

CREATE SEQUENCE roles_role_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- TOC entry 3135 (class 0 OID 0)
-- Dependencies: 253
-- Name: roles_role_id_seq; Type: SEQUENCE OWNED BY; Schema: system; Owner: -
--

ALTER SEQUENCE roles_role_id_seq OWNED BY roles.role_id;


--
-- TOC entry 254 (class 1259 OID 54951)
-- Name: users; Type: TABLE; Schema: system; Owner: -
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
-- TOC entry 255 (class 1259 OID 54954)
-- Name: users_user_id_seq; Type: SEQUENCE; Schema: system; Owner: -
--

CREATE SEQUENCE users_user_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- TOC entry 3136 (class 0 OID 0)
-- Dependencies: 255
-- Name: users_user_id_seq; Type: SEQUENCE OWNED BY; Schema: system; Owner: -
--

ALTER SEQUENCE users_user_id_seq OWNED BY users.user_id;


--
-- TOC entry 2988 (class 2604 OID 54988)
-- Name: api_key_id; Type: DEFAULT; Schema: system; Owner: -
--

ALTER TABLE ONLY api_keys ALTER COLUMN api_key_id SET DEFAULT nextval('api_keys_api_key_id_seq'::regclass);


--
-- TOC entry 2989 (class 2604 OID 54989)
-- Name: audit_trail_id; Type: DEFAULT; Schema: system; Owner: -
--

ALTER TABLE ONLY audit_trail ALTER COLUMN audit_trail_id SET DEFAULT nextval('audit_trail_audit_trail_id_seq'::regclass);


--
-- TOC entry 2990 (class 2604 OID 54990)
-- Name: audit_trail_data_id; Type: DEFAULT; Schema: system; Owner: -
--

ALTER TABLE ONLY audit_trail_data ALTER COLUMN audit_trail_data_id SET DEFAULT nextval('audit_trail_data_audit_trail_data_id_seq'::regclass);


--
-- TOC entry 2991 (class 2604 OID 54991)
-- Name: keystore_id; Type: DEFAULT; Schema: system; Owner: -
--

ALTER TABLE ONLY keystore ALTER COLUMN keystore_id SET DEFAULT nextval('keystore_keystore_id_seq'::regclass);


--
-- TOC entry 2992 (class 2604 OID 54992)
-- Name: permission_id; Type: DEFAULT; Schema: system; Owner: -
--

ALTER TABLE ONLY permissions ALTER COLUMN permission_id SET DEFAULT nextval('permissions_permission_id_seq'::regclass);


--
-- TOC entry 2993 (class 2604 OID 54993)
-- Name: role_id; Type: DEFAULT; Schema: system; Owner: -
--

ALTER TABLE ONLY roles ALTER COLUMN role_id SET DEFAULT nextval('roles_role_id_seq'::regclass);


--
-- TOC entry 2994 (class 2604 OID 54994)
-- Name: user_id; Type: DEFAULT; Schema: system; Owner: -
--

ALTER TABLE ONLY users ALTER COLUMN user_id SET DEFAULT nextval('users_user_id_seq'::regclass);


--
-- TOC entry 2996 (class 2606 OID 55088)
-- Name: api_keys_pkey; Type: CONSTRAINT; Schema: system; Owner: -
--

ALTER TABLE ONLY api_keys
    ADD CONSTRAINT api_keys_pkey PRIMARY KEY (api_key_id);


--
-- TOC entry 2998 (class 2606 OID 55090)
-- Name: audit_trail_audit_id_pk; Type: CONSTRAINT; Schema: system; Owner: -
--

ALTER TABLE ONLY audit_trail
    ADD CONSTRAINT audit_trail_audit_id_pk PRIMARY KEY (audit_trail_id);


--
-- TOC entry 3000 (class 2606 OID 55092)
-- Name: audit_trail_data_id_pk; Type: CONSTRAINT; Schema: system; Owner: -
--

ALTER TABLE ONLY audit_trail_data
    ADD CONSTRAINT audit_trail_data_id_pk PRIMARY KEY (audit_trail_data_id);


--
-- TOC entry 3002 (class 2606 OID 55094)
-- Name: keystore_key_key; Type: CONSTRAINT; Schema: system; Owner: -
--

ALTER TABLE ONLY keystore
    ADD CONSTRAINT keystore_key_key UNIQUE (key);


--
-- TOC entry 3004 (class 2606 OID 55096)
-- Name: keystore_pkey; Type: CONSTRAINT; Schema: system; Owner: -
--

ALTER TABLE ONLY keystore
    ADD CONSTRAINT keystore_pkey PRIMARY KEY (keystore_id);


--
-- TOC entry 3006 (class 2606 OID 55098)
-- Name: perm_id_pk; Type: CONSTRAINT; Schema: system; Owner: -
--

ALTER TABLE ONLY permissions
    ADD CONSTRAINT perm_id_pk PRIMARY KEY (permission_id);


--
-- TOC entry 3008 (class 2606 OID 55100)
-- Name: role_id_pk; Type: CONSTRAINT; Schema: system; Owner: -
--

ALTER TABLE ONLY roles
    ADD CONSTRAINT role_id_pk PRIMARY KEY (role_id);


--
-- TOC entry 3010 (class 2606 OID 55102)
-- Name: user_id_pk; Type: CONSTRAINT; Schema: system; Owner: -
--

ALTER TABLE ONLY users
    ADD CONSTRAINT user_id_pk PRIMARY KEY (user_id);


--
-- TOC entry 3012 (class 2606 OID 55104)
-- Name: user_name_uk; Type: CONSTRAINT; Schema: system; Owner: -
--

ALTER TABLE ONLY users
    ADD CONSTRAINT user_name_uk UNIQUE (user_name);


--
-- TOC entry 3013 (class 2606 OID 55266)
-- Name: api_keys_user_id_fkey; Type: FK CONSTRAINT; Schema: system; Owner: -
--

ALTER TABLE ONLY api_keys
    ADD CONSTRAINT api_keys_user_id_fkey FOREIGN KEY (user_id) REFERENCES users(user_id);


--
-- TOC entry 3014 (class 2606 OID 55271)
-- Name: audit_trail_user_id_fk; Type: FK CONSTRAINT; Schema: system; Owner: -
--

ALTER TABLE ONLY audit_trail
    ADD CONSTRAINT audit_trail_user_id_fk FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE SET NULL;


--
-- TOC entry 3015 (class 2606 OID 55276)
-- Name: permissios_role_id_fk; Type: FK CONSTRAINT; Schema: system; Owner: -
--

ALTER TABLE ONLY permissions
    ADD CONSTRAINT permissios_role_id_fk FOREIGN KEY (role_id) REFERENCES roles(role_id) ON DELETE CASCADE;


--
-- TOC entry 3016 (class 2606 OID 55281)
-- Name: users_role_id_fk; Type: FK CONSTRAINT; Schema: system; Owner: -
--

ALTER TABLE ONLY users
    ADD CONSTRAINT users_role_id_fk FOREIGN KEY (role_id) REFERENCES roles(role_id) ON DELETE SET NULL;


-- Completed on 2014-08-25 11:02:53 GMT

--
-- PostgreSQL database dump complete
--

