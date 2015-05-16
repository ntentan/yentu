CREATE TABLE roles (
    role_id INTEGER PRIMARY KEY AUTOINCREMENT,
    role_name text(64)
);

CREATE TABLE users (
    user_id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_name text(64) UNIQUE NOT NULL,
    password text(64) NOT NULL,
    role_id integer,
    first_name text(64) NOT NULL,
    last_name text(64) NOT NULL,
    other_names text(64),
    user_status INTEGER DEFAULT 2,
    email text(64) NOT NULL,
    CONSTRAINT users_role_id_fk FOREIGN KEY (role_id) REFERENCES roles(role_id) ON DELETE SET NULL
);

CREATE TABLE api_keys (
    api_key_id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id integer NOT NULL,
    active INTEGER NOT NULL,
    `key` text(512) NOT NULL,
    secret text(512) NOT NULL,
    CONSTRAINT api_keys_user_id_fkey FOREIGN KEY (user_id) REFERENCES users(user_id)
);

CREATE TABLE audit_trail (
    audit_trail_id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id integer NOT NULL,
    item_id integer NOT NULL,
    item_type text(64) NOT NULL,
    description text(4000) NOT NULL,
    audit_date TEXT NOT NULL,
    `type` INTEGER NOT NULL,
    data text,
    CONSTRAINT audit_trail_user_id_fk FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
);

CREATE TABLE audit_trail_data (
    audit_trail_data_id INTEGER PRIMARY KEY AUTOINCREMENT,
    audit_trail_id integer NOT NULL,
    data text
);

CREATE TABLE keystore (
    keystore_id INTEGER PRIMARY KEY AUTOINCREMENT,
    `key` text(255) UNIQUE NOT NULL,
    value text
);

CREATE TABLE permissions (
    permission_id INTEGER PRIMARY KEY AUTOINCREMENT,
    role_id integer NOT NULL,
    permission text(4000),
    value INTEGER NOT NULL,
    module text(4000),
    CONSTRAINT permissios_role_id_fk FOREIGN KEY (role_id) REFERENCES roles(role_id) ON DELETE CASCADE
);


CREATE INDEX audit_trail_item_id_idx ON audit_trail (item_id);

CREATE INDEX audit_trail_item_type_idx ON audit_trail (item_type);
