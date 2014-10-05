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
    user_status numeric DEFAULT 2,
    email character varying(64) NOT NULL
);

CREATE SEQUENCE users_user_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;

ALTER SEQUENCE users_user_id_seq OWNED BY users.user_id;

ALTER TABLE ONLY api_keys ALTER COLUMN api_key_id SET DEFAULT nextval('api_keys_api_key_id_seq'::regclass);

ALTER TABLE ONLY audit_trail ALTER COLUMN audit_trail_id SET DEFAULT nextval('audit_trail_audit_trail_id_seq'::regclass);

ALTER TABLE ONLY audit_trail_data ALTER COLUMN audit_trail_data_id SET DEFAULT nextval('audit_trail_data_audit_trail_data_id_seq'::regclass);

ALTER TABLE ONLY keystore ALTER COLUMN keystore_id SET DEFAULT nextval('keystore_keystore_id_seq'::regclass);

ALTER TABLE ONLY permissions ALTER COLUMN permission_id SET DEFAULT nextval('permissions_permission_id_seq'::regclass);

ALTER TABLE ONLY roles ALTER COLUMN role_id SET DEFAULT nextval('roles_role_id_seq'::regclass);

ALTER TABLE ONLY users ALTER COLUMN user_id SET DEFAULT nextval('users_user_id_seq'::regclass);

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

