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

CREATE SEQUENCE audit_trail_audit_trail_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;

ALTER SEQUENCE audit_trail_audit_trail_id_seq OWNED BY audit_trail.audit_trail_id;

CREATE TABLE audit_trail_data (
    audit_trail_data_id integer NOT NULL,
    audit_trail_id integer NOT NULL,
    data text
);

CREATE SEQUENCE audit_trail_data_audit_trail_data_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;

ALTER SEQUENCE audit_trail_data_audit_trail_data_id_seq OWNED BY audit_trail_data.audit_trail_data_id;

CREATE TABLE keystore (
    keystore_id integer NOT NULL,
    key character varying(255) NOT NULL,
    value text
);

CREATE SEQUENCE keystore_keystore_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;

ALTER SEQUENCE keystore_keystore_id_seq OWNED BY keystore.keystore_id;

CREATE TABLE permissions (
    permission_id integer NOT NULL,
    role_id integer NOT NULL,
    permission character varying(4000),
    value numeric NOT NULL,
    module character varying(4000)
);

CREATE SEQUENCE permissions_permission_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;

ALTER SEQUENCE permissions_permission_id_seq OWNED BY permissions.permission_id;

CREATE TABLE roles (
    role_id integer NOT NULL,
    role_name character varying(64)
);

CREATE SEQUENCE roles_role_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;

ALTER SEQUENCE roles_role_id_seq OWNED BY roles.role_id;

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

CREATE SEQUENCE users_user_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;

ALTER SEQUENCE users_user_id_seq OWNED BY users.user_id;

CREATE SEQUENCE yentu_history_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;

CREATE TABLE yentu_history (
    session character varying,
    version character varying,
    method character varying,
    arguments text,
    migration character varying,
    id integer DEFAULT nextval('yentu_history_id_seq'::regclass) NOT NULL
);

ALTER TABLE ONLY api_keys ALTER COLUMN api_key_id SET DEFAULT nextval('api_keys_api_key_id_seq'::regclass);

ALTER TABLE ONLY audit_trail ALTER COLUMN audit_trail_id SET DEFAULT nextval('audit_trail_audit_trail_id_seq'::regclass);

ALTER TABLE ONLY audit_trail_data ALTER COLUMN audit_trail_data_id SET DEFAULT nextval('audit_trail_data_audit_trail_data_id_seq'::regclass);

ALTER TABLE ONLY keystore ALTER COLUMN keystore_id SET DEFAULT nextval('keystore_keystore_id_seq'::regclass);

ALTER TABLE ONLY permissions ALTER COLUMN permission_id SET DEFAULT nextval('permissions_permission_id_seq'::regclass);

ALTER TABLE ONLY roles ALTER COLUMN role_id SET DEFAULT nextval('roles_role_id_seq'::regclass);

ALTER TABLE ONLY users ALTER COLUMN user_id SET DEFAULT nextval('users_user_id_seq'::regclass);

COPY api_keys (api_key_id, user_id, active, key, secret) FROM stdin;
\.

SELECT pg_catalog.setval('api_keys_api_key_id_seq', 1, false);

COPY audit_trail (audit_trail_id, user_id, item_id, item_type, description, audit_date, type, data) FROM stdin;
\.

SELECT pg_catalog.setval('audit_trail_audit_trail_id_seq', 1, false);

COPY audit_trail_data (audit_trail_data_id, audit_trail_id, data) FROM stdin;
\.

SELECT pg_catalog.setval('audit_trail_data_audit_trail_data_id_seq', 1, false);

COPY keystore (keystore_id, key, value) FROM stdin;
\.

SELECT pg_catalog.setval('keystore_keystore_id_seq', 1, false);

COPY permissions (permission_id, role_id, permission, value, module) FROM stdin;
\.

SELECT pg_catalog.setval('permissions_permission_id_seq', 1, false);

COPY roles (role_id, role_name) FROM stdin;
\.

SELECT pg_catalog.setval('roles_role_id_seq', 1, false);

COPY users (user_id, user_name, password, role_id, first_name, last_name, other_names, user_status, email) FROM stdin;
\.

SELECT pg_catalog.setval('users_user_id_seq', 1, false);

COPY yentu_history (session, version, method, arguments, migration, id) FROM stdin;
\N	20140826182510	\N	\N	\N	1
\.

SELECT pg_catalog.setval('yentu_history_id_seq', 1, true);

ALTER TABLE ONLY api_keys
    ADD CONSTRAINT api_keys_pkey PRIMARY KEY (api_key_id);

ALTER TABLE ONLY audit_trail
    ADD CONSTRAINT audit_trail_audit_id_pk PRIMARY KEY (audit_trail_id);

ALTER TABLE ONLY audit_trail_data
    ADD CONSTRAINT audit_trail_data_id_pk PRIMARY KEY (audit_trail_data_id);

ALTER TABLE ONLY keystore
    ADD CONSTRAINT keystore_key_key UNIQUE (key);

ALTER TABLE ONLY keystore
    ADD CONSTRAINT keystore_pkey PRIMARY KEY (keystore_id);

ALTER TABLE ONLY permissions
    ADD CONSTRAINT perm_id_pk PRIMARY KEY (permission_id);

ALTER TABLE ONLY roles
    ADD CONSTRAINT role_id_pk PRIMARY KEY (role_id);

ALTER TABLE ONLY users
    ADD CONSTRAINT user_id_pk PRIMARY KEY (user_id);

ALTER TABLE ONLY users
    ADD CONSTRAINT user_name_uk UNIQUE (user_name);

ALTER TABLE ONLY yentu_history
    ADD CONSTRAINT yentu_history_pk PRIMARY KEY (id);

CREATE INDEX audit_trail_item_id_idx ON audit_trail USING btree (item_id);

CREATE INDEX audit_trail_item_type_idx ON audit_trail USING btree (item_type);

ALTER TABLE ONLY api_keys
    ADD CONSTRAINT api_keys_user_id_fkey FOREIGN KEY (user_id) REFERENCES users(user_id);

ALTER TABLE ONLY audit_trail
    ADD CONSTRAINT audit_trail_user_id_fk FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE SET NULL;

ALTER TABLE ONLY permissions
    ADD CONSTRAINT permissios_role_id_fk FOREIGN KEY (role_id) REFERENCES roles(role_id) ON DELETE CASCADE;

ALTER TABLE ONLY users
    ADD CONSTRAINT users_role_id_fk FOREIGN KEY (role_id) REFERENCES roles(role_id) ON DELETE SET NULL;

