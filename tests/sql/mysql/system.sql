CREATE TABLE api_keys (
    api_key_id integer not null auto_increment primary key,
    user_id integer NOT NULL,
    active boolean NOT NULL,
    `key` varchar(512) NOT NULL,
    secret varchar(512) NOT NULL
);

CREATE TABLE audit_trail (
    audit_trail_id integer not null auto_increment primary key,
    user_id integer NOT NULL,
    item_id integer NOT NULL,
    item_type varchar(64) NOT NULL,
    description varchar(4000) NOT NULL,
    audit_date timestamp NOT NULL,
    `type` numeric NOT NULL,
    data text
);

CREATE TABLE audit_trail_data (
    audit_trail_data_id integer not null auto_increment primary key,
    audit_trail_id integer NOT NULL,
    data text
);

CREATE TABLE keystore (
    keystore_id integer not null auto_increment primary key,
    `key` varchar(255) NOT NULL,
    value text
);

CREATE TABLE permissions (
    permission_id integer not null auto_increment primary key,
    role_id integer NOT NULL,
    permission varchar(4000),
    value numeric NOT NULL,
    module varchar(4000)
);

CREATE TABLE roles (
    role_id integer not null auto_increment primary key,
    role_name varchar(64)
);

CREATE TABLE users (
    user_id integer not null auto_increment primary key,
    user_name varchar(64) NOT NULL,
    password varchar(64) NOT NULL,
    role_id integer,
    first_name varchar(64) NOT NULL,
    last_name varchar(64) NOT NULL,
    other_names varchar(64),
    user_status numeric DEFAULT 2,
    email varchar(64) NOT NULL
);

ALTER TABLE keystore
    ADD CONSTRAINT keystore_key_key UNIQUE (`key`);

ALTER TABLE users
    ADD CONSTRAINT user_name_uk UNIQUE (user_name);

CREATE INDEX audit_trail_item_id_idx ON audit_trail (item_id);

CREATE INDEX audit_trail_item_type_idx ON audit_trail (item_type);

ALTER TABLE api_keys
    ADD CONSTRAINT api_keys_user_id_fkey FOREIGN KEY (user_id) REFERENCES users(user_id);

ALTER TABLE audit_trail
    ADD CONSTRAINT audit_trail_user_id_fk FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE;

ALTER TABLE permissions
    ADD CONSTRAINT permissios_role_id_fk FOREIGN KEY (role_id) REFERENCES roles(role_id) ON DELETE CASCADE;

ALTER TABLE users
    ADD CONSTRAINT users_role_id_fk FOREIGN KEY (role_id) REFERENCES roles(role_id) ON DELETE SET NULL;
