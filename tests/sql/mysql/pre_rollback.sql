
CREATE TABLE api_keys (
    secret varchar(255) NOT NULL,
    `key` varchar(255) NOT NULL,
    active boolean NOT NULL,
    user_id integer NOT NULL,
    api_key_id integer NOT NULL AUTO_INCREMENT PRIMARY KEY 
);


CREATE TABLE audit_trail (
    data text,
    type numeric NOT NULL,
    audit_date timestamp NOT NULL,
    description varchar(255) NOT NULL,
    item_type varchar(255) NOT NULL,
    item_id integer NOT NULL,
    user_id integer NOT NULL,
    audit_trail_id integer NOT NULL AUTO_INCREMENT PRIMARY KEY 
);


CREATE TABLE audit_trail_data (
    data text,
    audit_trail_id integer NOT NULL,
    audit_trail_data_id integer NOT NULL AUTO_INCREMENT PRIMARY KEY 
);


CREATE TABLE bank_branches (
    sort_code varchar(255) NOT NULL,
    address varchar(255),
    branch_name varchar(255) NOT NULL,
    bank_id integer NOT NULL,
    bank_branch_id integer NOT NULL AUTO_INCREMENT PRIMARY KEY 
);


CREATE TABLE banks (
    swift_code varchar(255),
    bank_code varchar(255) NOT NULL,
    bank_name varchar(255) NOT NULL,
    bank_id integer NOT NULL AUTO_INCREMENT PRIMARY KEY 
);


CREATE TABLE binary_objects (
    data blob,
    object_id integer NOT NULL AUTO_INCREMENT PRIMARY KEY 
);


CREATE TABLE branches (
    city_id integer,
    address text,
    branch_name varchar(255) NOT NULL,
    branch_id integer NOT NULL AUTO_INCREMENT PRIMARY KEY 
);


CREATE TABLE cheque_formats (
    data text NOT NULL,
    description varchar(255) NOT NULL,
    cheque_format_id integer NOT NULL AUTO_INCREMENT PRIMARY KEY 
);


CREATE TABLE cities (
    region_id integer NOT NULL,
    city_name varchar(255) NOT NULL,
    city_id integer NOT NULL AUTO_INCREMENT PRIMARY KEY 
);


CREATE TABLE client_joint_accounts (
    mobile varchar(255),
    id_issue_date date,
    id_issue_place varchar(255),
    id_number varchar(255),
    id_type_id integer,
    office_telephone varchar(255),
    telephone varchar(255),
    address varchar(255),
    previous_names varchar(255),
    other_names varchar(255),
    first_name varchar(255) NOT NULL,
    surname varchar(255) NOT NULL,
    title varchar(255),
    main_client_id integer NOT NULL,
    joint_account_id integer NOT NULL AUTO_INCREMENT PRIMARY KEY 
);


CREATE TABLE client_users (
    status integer NOT NULL,
    user_name varchar(255) NOT NULL,
    password varchar(255) NOT NULL,
    membership_id integer,
    main_client_id integer,
    client_user_id integer NOT NULL AUTO_INCREMENT PRIMARY KEY 
);


CREATE TABLE clients (
    branch_id integer NOT NULL,
    marital_status numeric,
    gender numeric,
    nationality_id integer,
    company_name varchar(255),
    contact_person varchar(255),
    in_trust_for varchar(255),
    country_id integer,
    id_issue_date date,
    id_issue_place varchar(255),
    signature varchar(255),
    scanned_id varchar(255),
    picture varchar(255),
    occupation varchar(255),
    residential_status numeric,
    id_number varchar(255) NOT NULL,
    id_type_id integer NOT NULL,
    city_id integer,
    birth_date date,
    email varchar(255),
    fax varchar(255),
    mobile varchar(255),
    contact_tel varchar(255),
    residential_address varchar(255),
    mailing_address varchar(255) NOT NULL,
    previous_names varchar(255),
    other_names varchar(255),
    first_name varchar(255),
    surname varchar(255),
    title varchar(255),
    account_type numeric NOT NULL,
    main_client_id integer NOT NULL AUTO_INCREMENT PRIMARY KEY 
);


CREATE TABLE configurations (
    value varchar(255),
    `key` varchar(255),
    configuration_id integer NOT NULL AUTO_INCREMENT PRIMARY KEY 
);


CREATE TABLE countries (
    currency_symbol varchar(255),
    currency varchar(255),
    nationality varchar(255),
    country_code varchar(255),
    country_name varchar(255) NOT NULL,
    country_id integer NOT NULL AUTO_INCREMENT PRIMARY KEY 
);


CREATE TABLE departments (
    department_name varchar(255) NOT NULL,
    department_id integer NOT NULL AUTO_INCREMENT PRIMARY KEY 
);


CREATE TABLE holidays (
    holiday_date date NOT NULL,
    name varchar(255) NOT NULL,
    holiday_id integer NOT NULL AUTO_INCREMENT PRIMARY KEY 
);


CREATE TABLE identification_types (
    id_name varchar(255) NOT NULL,
    id_type_id integer NOT NULL AUTO_INCREMENT PRIMARY KEY 
);


CREATE TABLE locations (
    address varchar(255),
    name varchar(255),
    location_id integer NOT NULL AUTO_INCREMENT PRIMARY KEY 
);


CREATE TABLE note_attachments (
    object_id integer,
    note_id integer,
    description varchar(255),
    note_attachment_id integer NOT NULL AUTO_INCREMENT PRIMARY KEY 
);


CREATE TABLE notes (
    user_id integer NOT NULL,
    item_type varchar(255) NOT NULL,
    item_id integer NOT NULL,
    note_time timestamp NOT NULL,
    note varchar(255),
    note_id integer NOT NULL AUTO_INCREMENT PRIMARY KEY 
);


CREATE TABLE notifications (
    subject varchar(255),
    email text,
    sms text,
    tag varchar(255),
    notification_id integer NOT NULL AUTO_INCREMENT PRIMARY KEY 
);


CREATE TABLE permissions (
    module varchar(255),
    value numeric NOT NULL,
    permission varchar(255),
    role_id integer NOT NULL,
    permission_id integer NOT NULL AUTO_INCREMENT PRIMARY KEY 
);


CREATE TABLE regions (
    country_id integer NOT NULL,
    name varchar(255) NOT NULL,
    region_code varchar(255) NOT NULL,
    region_id integer NOT NULL AUTO_INCREMENT PRIMARY KEY 
);


CREATE TABLE relationships (
    relationship_name varchar(255) NOT NULL,
    relationship_id integer NOT NULL AUTO_INCREMENT PRIMARY KEY 
);


CREATE TABLE roles (
    role_name varchar(255),
    role_id integer NOT NULL AUTO_INCREMENT PRIMARY KEY 
);


CREATE TABLE suppliers (
    user_id integer,
    telephone varchar(255),
    address varchar(255),
    supplier_name varchar(255) NOT NULL,
    supplier_id integer NOT NULL AUTO_INCREMENT PRIMARY KEY 
);


CREATE TABLE temporary_roles (
    tag varchar(255),
    original_role_id integer,
    active boolean,
    expires timestamp,
    created timestamp,
    user_id integer,
    new_role_id integer,
    temporary_role_id integer NOT NULL AUTO_INCREMENT PRIMARY KEY 
);


CREATE TABLE users (
    branch_id integer,
    picture_id integer,
    department_id integer,
    phone varchar(255),
    email varchar(255) NOT NULL,
    user_status numeric,
    other_names varchar(255),
    last_name varchar(255) NOT NULL,
    first_name varchar(255) NOT NULL,
    role_id integer,
    password varchar(255) NOT NULL,
    user_name varchar(255) NOT NULL,
    user_id integer NOT NULL AUTO_INCREMENT PRIMARY KEY 
);


CREATE TABLE yentu_history (
    session varchar(255),
    version varchar(255),
    method varchar(255),
    arguments text,
    migration varchar(255),
    default_schema varchar(255),
    id integer NOT NULL AUTO_INCREMENT PRIMARY KEY 
);

INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addTable', '[{"name":"users","schema":false}]', 'import', 1);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addColumn', '[{"name":"branch_id","type":"integer","table":"users","schema":false,"nulls":true}]', 'import', 2);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addColumn', '[{"name":"picture_id","type":"integer","table":"users","schema":false,"nulls":true}]', 'import', 3);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addColumn', '[{"name":"department_id","type":"integer","table":"users","schema":false,"nulls":true}]', 'import', 4);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addColumn', '[{"name":"phone","type":"string","table":"users","schema":false,"nulls":true}]', 'import', 5);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addColumn', '[{"name":"email","type":"string","table":"users","schema":false,"nulls":false}]', 'import', 6);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addColumn', '[{"name":"user_status","type":"double","table":"users","schema":false,"nulls":true}]', 'import', 7);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addColumn', '[{"name":"other_names","type":"string","table":"users","schema":false,"nulls":true}]', 'import', 8);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addColumn', '[{"name":"last_name","type":"string","table":"users","schema":false,"nulls":false}]', 'import', 9);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addColumn', '[{"name":"first_name","type":"string","table":"users","schema":false,"nulls":false}]', 'import', 10);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addColumn', '[{"name":"role_id","type":"integer","table":"users","schema":false,"nulls":true}]', 'import', 11);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addColumn', '[{"name":"password","type":"string","table":"users","schema":false,"nulls":false}]', 'import', 12);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addColumn', '[{"name":"user_name","type":"string","table":"users","schema":false,"nulls":false}]', 'import', 13);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addColumn', '[{"name":"user_id","type":"integer","table":"users","schema":false,"nulls":false}]', 'import', 14);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addAutoPrimaryKey', '[{"table":"users","schema":false,"column":"user_id"}]', 'import', 15);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addPrimaryKey', '[{"table":"users","schema":false,"columns":["user_id"],"name":"user_id_pk"}]', 'import', 16);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addUniqueKey', '[{"table":"users","schema":false,"columns":["user_name"],"name":"user_name_uk"}]', 'import', 17);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addTable', '[{"name":"roles","schema":false}]', 'import', 18);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addColumn', '[{"name":"role_name","type":"string","table":"roles","schema":false,"nulls":true}]', 'import', 19);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addColumn', '[{"name":"role_id","type":"integer","table":"roles","schema":false,"nulls":false}]', 'import', 20);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addAutoPrimaryKey', '[{"table":"roles","schema":false,"column":"role_id"}]', 'import', 21);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addPrimaryKey', '[{"table":"roles","schema":false,"columns":["role_id"],"name":"role_id_pk"}]', 'import', 22);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addTable', '[{"name":"departments","schema":false}]', 'import', 23);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addColumn', '[{"name":"department_name","type":"string","table":"departments","schema":false,"nulls":false}]', 'import', 24);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addColumn', '[{"name":"department_id","type":"integer","table":"departments","schema":false,"nulls":false}]', 'import', 25);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addAutoPrimaryKey', '[{"table":"departments","schema":false,"column":"department_id"}]', 'import', 26);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addPrimaryKey', '[{"table":"departments","schema":false,"columns":["department_id"],"name":"department_id_pk"}]', 'import', 27);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addTable', '[{"name":"temporary_roles","schema":false}]', 'import', 28);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addColumn', '[{"name":"tag","type":"string","table":"temporary_roles","schema":false,"nulls":true}]', 'import', 29);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addColumn', '[{"name":"original_role_id","type":"integer","table":"temporary_roles","schema":false,"nulls":true}]', 'import', 30);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addColumn', '[{"name":"active","type":"boolean","table":"temporary_roles","schema":false,"nulls":true}]', 'import', 31);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addColumn', '[{"name":"expires","type":"timestamp","table":"temporary_roles","schema":false,"nulls":true}]', 'import', 32);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addColumn', '[{"name":"created","type":"timestamp","table":"temporary_roles","schema":false,"nulls":true}]', 'import', 33);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addColumn', '[{"name":"user_id","type":"integer","table":"temporary_roles","schema":false,"nulls":true}]', 'import', 34);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addColumn', '[{"name":"new_role_id","type":"integer","table":"temporary_roles","schema":false,"nulls":true}]', 'import', 35);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addColumn', '[{"name":"temporary_role_id","type":"integer","table":"temporary_roles","schema":false,"nulls":false}]', 'import', 36);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addAutoPrimaryKey', '[{"table":"temporary_roles","schema":false,"column":"temporary_role_id"}]', 'import', 37);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addPrimaryKey', '[{"table":"temporary_roles","schema":false,"columns":["temporary_role_id"],"name":"temporary_role_id_pk"}]', 'import', 38);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addTable', '[{"name":"suppliers","schema":false}]', 'import', 39);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addColumn', '[{"name":"user_id","type":"integer","table":"suppliers","schema":false,"nulls":true}]', 'import', 40);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addColumn', '[{"name":"telephone","type":"string","table":"suppliers","schema":false,"nulls":true}]', 'import', 41);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addColumn', '[{"name":"address","type":"string","table":"suppliers","schema":false,"nulls":true}]', 'import', 42);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addColumn', '[{"name":"supplier_name","type":"string","table":"suppliers","schema":false,"nulls":false}]', 'import', 43);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addColumn', '[{"name":"supplier_id","type":"integer","table":"suppliers","schema":false,"nulls":false}]', 'import', 44);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addAutoPrimaryKey', '[{"table":"suppliers","schema":false,"column":"supplier_id"}]', 'import', 45);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addPrimaryKey', '[{"table":"suppliers","schema":false,"columns":["supplier_id"],"name":"supplier_id_pk"}]', 'import', 46);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addTable', '[{"name":"countries","schema":false}]', 'import', 47);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addColumn', '[{"name":"currency_symbol","type":"string","table":"countries","schema":false,"nulls":true}]', 'import', 48);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addColumn', '[{"name":"currency","type":"string","table":"countries","schema":false,"nulls":true}]', 'import', 49);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addColumn', '[{"name":"nationality","type":"string","table":"countries","schema":false,"nulls":true}]', 'import', 50);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addColumn', '[{"name":"country_code","type":"string","table":"countries","schema":false,"nulls":true}]', 'import', 51);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addColumn', '[{"name":"country_name","type":"string","table":"countries","schema":false,"nulls":false}]', 'import', 52);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addColumn', '[{"name":"country_id","type":"integer","table":"countries","schema":false,"nulls":false}]', 'import', 53);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addAutoPrimaryKey', '[{"table":"countries","schema":false,"column":"country_id"}]', 'import', 54);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addPrimaryKey', '[{"table":"countries","schema":false,"columns":["country_id"],"name":"country_id_pk"}]', 'import', 55);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addUniqueKey', '[{"table":"countries","schema":false,"columns":["country_name"],"name":"country_name_uk"}]', 'import', 56);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addTable', '[{"name":"permissions","schema":false}]', 'import', 57);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addColumn', '[{"name":"module","type":"string","table":"permissions","schema":false,"nulls":true}]', 'import', 58);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addColumn', '[{"name":"value","type":"double","table":"permissions","schema":false,"nulls":false}]', 'import', 59);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addColumn', '[{"name":"permission","type":"string","table":"permissions","schema":false,"nulls":true}]', 'import', 60);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addColumn', '[{"name":"role_id","type":"integer","table":"permissions","schema":false,"nulls":false}]', 'import', 61);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addColumn', '[{"name":"permission_id","type":"integer","table":"permissions","schema":false,"nulls":false}]', 'import', 62);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addAutoPrimaryKey', '[{"table":"permissions","schema":false,"column":"permission_id"}]', 'import', 63);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addPrimaryKey', '[{"table":"permissions","schema":false,"columns":["permission_id"],"name":"perm_id_pk"}]', 'import', 64);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addTable', '[{"name":"notes","schema":false}]', 'import', 65);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addColumn', '[{"name":"user_id","type":"integer","table":"notes","schema":false,"nulls":false}]', 'import', 66);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addColumn', '[{"name":"item_type","type":"string","table":"notes","schema":false,"nulls":false}]', 'import', 67);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addColumn', '[{"name":"item_id","type":"integer","table":"notes","schema":false,"nulls":false}]', 'import', 68);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addColumn', '[{"name":"note_time","type":"timestamp","table":"notes","schema":false,"nulls":false}]', 'import', 69);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addColumn', '[{"name":"note","type":"string","table":"notes","schema":false,"nulls":true}]', 'import', 70);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addColumn', '[{"name":"note_id","type":"integer","table":"notes","schema":false,"nulls":false}]', 'import', 71);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addAutoPrimaryKey', '[{"table":"notes","schema":false,"column":"note_id"}]', 'import', 72);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addPrimaryKey', '[{"table":"notes","schema":false,"columns":["note_id"],"name":"notes_note_id_pk"}]', 'import', 73);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addTable', '[{"name":"regions","schema":false}]', 'import', 74);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addColumn', '[{"name":"country_id","type":"integer","table":"regions","schema":false,"nulls":false}]', 'import', 75);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addColumn', '[{"name":"name","type":"string","table":"regions","schema":false,"nulls":false}]', 'import', 76);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addColumn', '[{"name":"region_code","type":"string","table":"regions","schema":false,"nulls":false}]', 'import', 77);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addColumn', '[{"name":"region_id","type":"integer","table":"regions","schema":false,"nulls":false}]', 'import', 78);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addAutoPrimaryKey', '[{"table":"regions","schema":false,"column":"region_id"}]', 'import', 79);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addPrimaryKey', '[{"table":"regions","schema":false,"columns":["region_id"],"name":"region_id_pk"}]', 'import', 80);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addUniqueKey', '[{"table":"regions","schema":false,"columns":["name"],"name":"region_name_uk"}]', 'import', 81);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addTable', '[{"name":"note_attachments","schema":false}]', 'import', 82);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addColumn', '[{"name":"object_id","type":"integer","table":"note_attachments","schema":false,"nulls":true}]', 'import', 83);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addColumn', '[{"name":"note_id","type":"integer","table":"note_attachments","schema":false,"nulls":true}]', 'import', 84);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addColumn', '[{"name":"description","type":"string","table":"note_attachments","schema":false,"nulls":true}]', 'import', 85);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addColumn', '[{"name":"note_attachment_id","type":"integer","table":"note_attachments","schema":false,"nulls":false}]', 'import', 86);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addAutoPrimaryKey', '[{"table":"note_attachments","schema":false,"column":"note_attachment_id"}]', 'import', 87);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addPrimaryKey', '[{"table":"note_attachments","schema":false,"columns":["note_attachment_id"],"name":"note_attachments_pkey"}]', 'import', 88);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addTable', '[{"name":"branches","schema":false}]', 'import', 89);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addColumn', '[{"name":"city_id","type":"integer","table":"branches","schema":false,"nulls":true}]', 'import', 90);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addColumn', '[{"name":"address","type":"text","table":"branches","schema":false,"nulls":true}]', 'import', 91);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addColumn', '[{"name":"branch_name","type":"string","table":"branches","schema":false,"nulls":false}]', 'import', 92);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addColumn', '[{"name":"branch_id","type":"integer","table":"branches","schema":false,"nulls":false}]', 'import', 93);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addAutoPrimaryKey', '[{"table":"branches","schema":false,"column":"branch_id"}]', 'import', 94);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addPrimaryKey', '[{"table":"branches","schema":false,"columns":["branch_id"],"name":"branches_pkey"}]', 'import', 95);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addTable', '[{"name":"clients","schema":false}]', 'import', 96);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addColumn', '[{"name":"branch_id","type":"integer","table":"clients","schema":false,"nulls":false}]', 'import', 97);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addColumn', '[{"name":"marital_status","type":"double","table":"clients","schema":false,"nulls":true}]', 'import', 98);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addColumn', '[{"name":"gender","type":"double","table":"clients","schema":false,"nulls":true}]', 'import', 99);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addColumn', '[{"name":"nationality_id","type":"integer","table":"clients","schema":false,"nulls":true}]', 'import', 100);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addColumn', '[{"name":"company_name","type":"string","table":"clients","schema":false,"nulls":true}]', 'import', 101);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addColumn', '[{"name":"contact_person","type":"string","table":"clients","schema":false,"nulls":true}]', 'import', 102);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addColumn', '[{"name":"in_trust_for","type":"string","table":"clients","schema":false,"nulls":true}]', 'import', 103);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addColumn', '[{"name":"country_id","type":"integer","table":"clients","schema":false,"nulls":true}]', 'import', 104);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addColumn', '[{"name":"id_issue_date","type":"date","table":"clients","schema":false,"nulls":true}]', 'import', 105);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addColumn', '[{"name":"id_issue_place","type":"string","table":"clients","schema":false,"nulls":true}]', 'import', 106);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addColumn', '[{"name":"signature","type":"string","table":"clients","schema":false,"nulls":true}]', 'import', 107);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addColumn', '[{"name":"scanned_id","type":"string","table":"clients","schema":false,"nulls":true}]', 'import', 108);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addColumn', '[{"name":"picture","type":"string","table":"clients","schema":false,"nulls":true}]', 'import', 109);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addColumn', '[{"name":"occupation","type":"string","table":"clients","schema":false,"nulls":true}]', 'import', 110);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addColumn', '[{"name":"residential_status","type":"double","table":"clients","schema":false,"nulls":true}]', 'import', 111);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addColumn', '[{"name":"id_number","type":"string","table":"clients","schema":false,"nulls":false}]', 'import', 112);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addColumn', '[{"name":"id_type_id","type":"integer","table":"clients","schema":false,"nulls":false}]', 'import', 113);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addColumn', '[{"name":"city_id","type":"integer","table":"clients","schema":false,"nulls":true}]', 'import', 114);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addColumn', '[{"name":"birth_date","type":"date","table":"clients","schema":false,"nulls":true}]', 'import', 115);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addColumn', '[{"name":"email","type":"string","table":"clients","schema":false,"nulls":true}]', 'import', 116);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addColumn', '[{"name":"fax","type":"string","table":"clients","schema":false,"nulls":true}]', 'import', 117);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addColumn', '[{"name":"mobile","type":"string","table":"clients","schema":false,"nulls":true}]', 'import', 118);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addColumn', '[{"name":"contact_tel","type":"string","table":"clients","schema":false,"nulls":true}]', 'import', 119);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addColumn', '[{"name":"residential_address","type":"string","table":"clients","schema":false,"nulls":true}]', 'import', 120);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addColumn', '[{"name":"mailing_address","type":"string","table":"clients","schema":false,"nulls":false}]', 'import', 121);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addColumn', '[{"name":"previous_names","type":"string","table":"clients","schema":false,"nulls":true}]', 'import', 122);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addColumn', '[{"name":"other_names","type":"string","table":"clients","schema":false,"nulls":true}]', 'import', 123);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addColumn', '[{"name":"first_name","type":"string","table":"clients","schema":false,"nulls":true}]', 'import', 124);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addColumn', '[{"name":"surname","type":"string","table":"clients","schema":false,"nulls":true}]', 'import', 125);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addColumn', '[{"name":"title","type":"string","table":"clients","schema":false,"nulls":true}]', 'import', 126);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addColumn', '[{"name":"account_type","type":"double","table":"clients","schema":false,"nulls":false}]', 'import', 127);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addColumn', '[{"name":"main_client_id","type":"integer","table":"clients","schema":false,"nulls":false}]', 'import', 128);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addAutoPrimaryKey', '[{"table":"clients","schema":false,"column":"main_client_id"}]', 'import', 129);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addPrimaryKey', '[{"table":"clients","schema":false,"columns":["main_client_id"],"name":"clients_main_client_id_pk"}]', 'import', 130);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addTable', '[{"name":"identification_types","schema":false}]', 'import', 131);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addColumn', '[{"name":"id_name","type":"string","table":"identification_types","schema":false,"nulls":false}]', 'import', 132);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addColumn', '[{"name":"id_type_id","type":"integer","table":"identification_types","schema":false,"nulls":false}]', 'import', 133);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addAutoPrimaryKey', '[{"table":"identification_types","schema":false,"column":"id_type_id"}]', 'import', 134);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addPrimaryKey', '[{"table":"identification_types","schema":false,"columns":["id_type_id"],"name":"id_type_id_pk"}]', 'import', 135);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addTable', '[{"name":"client_users","schema":false}]', 'import', 136);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addColumn', '[{"name":"status","type":"integer","table":"client_users","schema":false,"nulls":false}]', 'import', 137);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addColumn', '[{"name":"user_name","type":"string","table":"client_users","schema":false,"nulls":false}]', 'import', 138);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addColumn', '[{"name":"password","type":"string","table":"client_users","schema":false,"nulls":false}]', 'import', 139);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addColumn', '[{"name":"membership_id","type":"integer","table":"client_users","schema":false,"nulls":true}]', 'import', 140);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addColumn', '[{"name":"main_client_id","type":"integer","table":"client_users","schema":false,"nulls":true}]', 'import', 141);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addColumn', '[{"name":"client_user_id","type":"integer","table":"client_users","schema":false,"nulls":false}]', 'import', 142);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addAutoPrimaryKey', '[{"table":"client_users","schema":false,"column":"client_user_id"}]', 'import', 143);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addPrimaryKey', '[{"table":"client_users","schema":false,"columns":["client_user_id"],"name":"client_users_client_user_id_pk"}]', 'import', 144);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addTable', '[{"name":"client_joint_accounts","schema":false}]', 'import', 145);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addColumn', '[{"name":"mobile","type":"string","table":"client_joint_accounts","schema":false,"nulls":true}]', 'import', 146);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addColumn', '[{"name":"id_issue_date","type":"date","table":"client_joint_accounts","schema":false,"nulls":true}]', 'import', 147);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addColumn', '[{"name":"id_issue_place","type":"string","table":"client_joint_accounts","schema":false,"nulls":true}]', 'import', 148);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addColumn', '[{"name":"id_number","type":"string","table":"client_joint_accounts","schema":false,"nulls":true}]', 'import', 149);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addColumn', '[{"name":"id_type_id","type":"integer","table":"client_joint_accounts","schema":false,"nulls":true}]', 'import', 150);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addColumn', '[{"name":"office_telephone","type":"string","table":"client_joint_accounts","schema":false,"nulls":true}]', 'import', 151);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addColumn', '[{"name":"telephone","type":"string","table":"client_joint_accounts","schema":false,"nulls":true}]', 'import', 152);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addColumn', '[{"name":"address","type":"string","table":"client_joint_accounts","schema":false,"nulls":true}]', 'import', 153);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addColumn', '[{"name":"previous_names","type":"string","table":"client_joint_accounts","schema":false,"nulls":true}]', 'import', 154);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addColumn', '[{"name":"other_names","type":"string","table":"client_joint_accounts","schema":false,"nulls":true}]', 'import', 155);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addColumn', '[{"name":"first_name","type":"string","table":"client_joint_accounts","schema":false,"nulls":false}]', 'import', 156);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addColumn', '[{"name":"surname","type":"string","table":"client_joint_accounts","schema":false,"nulls":false}]', 'import', 157);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addColumn', '[{"name":"title","type":"string","table":"client_joint_accounts","schema":false,"nulls":true}]', 'import', 158);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addColumn', '[{"name":"main_client_id","type":"integer","table":"client_joint_accounts","schema":false,"nulls":false}]', 'import', 159);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addColumn', '[{"name":"joint_account_id","type":"integer","table":"client_joint_accounts","schema":false,"nulls":false}]', 'import', 160);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addAutoPrimaryKey', '[{"table":"client_joint_accounts","schema":false,"column":"joint_account_id"}]', 'import', 161);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addPrimaryKey', '[{"table":"client_joint_accounts","schema":false,"columns":["joint_account_id"],"name":"joint_account_id_pk"}]', 'import', 162);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addTable', '[{"name":"cities","schema":false}]', 'import', 163);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addColumn', '[{"name":"region_id","type":"integer","table":"cities","schema":false,"nulls":false}]', 'import', 164);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addColumn', '[{"name":"city_name","type":"string","table":"cities","schema":false,"nulls":false}]', 'import', 165);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addColumn', '[{"name":"city_id","type":"integer","table":"cities","schema":false,"nulls":false}]', 'import', 166);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addAutoPrimaryKey', '[{"table":"cities","schema":false,"column":"city_id"}]', 'import', 167);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addPrimaryKey', '[{"table":"cities","schema":false,"columns":["city_id"],"name":"city_id_pk"}]', 'import', 168);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addUniqueKey', '[{"table":"cities","schema":false,"columns":["city_name"],"name":"city_name_uk"}]', 'import', 169);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addTable', '[{"name":"bank_branches","schema":false}]', 'import', 170);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addColumn', '[{"name":"sort_code","type":"string","table":"bank_branches","schema":false,"nulls":false}]', 'import', 171);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addColumn', '[{"name":"address","type":"string","table":"bank_branches","schema":false,"nulls":true}]', 'import', 172);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addColumn', '[{"name":"branch_name","type":"string","table":"bank_branches","schema":false,"nulls":false}]', 'import', 173);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addColumn', '[{"name":"bank_id","type":"integer","table":"bank_branches","schema":false,"nulls":false}]', 'import', 174);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addColumn', '[{"name":"bank_branch_id","type":"integer","table":"bank_branches","schema":false,"nulls":false}]', 'import', 175);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addAutoPrimaryKey', '[{"table":"bank_branches","schema":false,"column":"bank_branch_id"}]', 'import', 176);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addPrimaryKey', '[{"table":"bank_branches","schema":false,"columns":["bank_branch_id"],"name":"bank_branch_id_pk"}]', 'import', 177);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addTable', '[{"name":"banks","schema":false}]', 'import', 178);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addColumn', '[{"name":"swift_code","type":"string","table":"banks","schema":false,"nulls":true}]', 'import', 179);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addColumn', '[{"name":"bank_code","type":"string","table":"banks","schema":false,"nulls":false}]', 'import', 180);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addColumn', '[{"name":"bank_name","type":"string","table":"banks","schema":false,"nulls":false}]', 'import', 181);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addColumn', '[{"name":"bank_id","type":"integer","table":"banks","schema":false,"nulls":false}]', 'import', 182);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addAutoPrimaryKey', '[{"table":"banks","schema":false,"column":"bank_id"}]', 'import', 183);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addPrimaryKey', '[{"table":"banks","schema":false,"columns":["bank_id"],"name":"bank_id_pk"}]', 'import', 184);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addUniqueKey', '[{"table":"banks","schema":false,"columns":["bank_name"],"name":"bank_name_uk"}]', 'import', 185);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addTable', '[{"name":"api_keys","schema":false}]', 'import', 186);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addColumn', '[{"name":"secret","type":"string","table":"api_keys","schema":false,"nulls":false}]', 'import', 187);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addColumn', '[{"name":"key","type":"string","table":"api_keys","schema":false,"nulls":false}]', 'import', 188);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addColumn', '[{"name":"active","type":"boolean","table":"api_keys","schema":false,"nulls":false}]', 'import', 189);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addColumn', '[{"name":"user_id","type":"integer","table":"api_keys","schema":false,"nulls":false}]', 'import', 190);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addColumn', '[{"name":"api_key_id","type":"integer","table":"api_keys","schema":false,"nulls":false}]', 'import', 191);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addAutoPrimaryKey', '[{"table":"api_keys","schema":false,"column":"api_key_id"}]', 'import', 192);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addPrimaryKey', '[{"table":"api_keys","schema":false,"columns":["api_key_id"],"name":"api_keys_pkey"}]', 'import', 193);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addTable', '[{"name":"relationships","schema":false}]', 'import', 194);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addColumn', '[{"name":"relationship_name","type":"string","table":"relationships","schema":false,"nulls":false}]', 'import', 195);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addColumn', '[{"name":"relationship_id","type":"integer","table":"relationships","schema":false,"nulls":false}]', 'import', 196);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addAutoPrimaryKey', '[{"table":"relationships","schema":false,"column":"relationship_id"}]', 'import', 197);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addPrimaryKey', '[{"table":"relationships","schema":false,"columns":["relationship_id"],"name":"relationship_id_pk"}]', 'import', 198);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addTable', '[{"name":"notifications","schema":false}]', 'import', 199);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addColumn', '[{"name":"subject","type":"string","table":"notifications","schema":false,"nulls":true}]', 'import', 200);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addColumn', '[{"name":"email","type":"text","table":"notifications","schema":false,"nulls":true}]', 'import', 201);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addColumn', '[{"name":"sms","type":"text","table":"notifications","schema":false,"nulls":true}]', 'import', 202);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addColumn', '[{"name":"tag","type":"string","table":"notifications","schema":false,"nulls":true}]', 'import', 203);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addColumn', '[{"name":"notification_id","type":"integer","table":"notifications","schema":false,"nulls":false}]', 'import', 204);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addAutoPrimaryKey', '[{"table":"notifications","schema":false,"column":"notification_id"}]', 'import', 205);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addPrimaryKey', '[{"table":"notifications","schema":false,"columns":["notification_id"],"name":"notifications_pkey"}]', 'import', 206);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addTable', '[{"name":"holidays","schema":false}]', 'import', 207);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addColumn', '[{"name":"holiday_date","type":"date","table":"holidays","schema":false,"nulls":false}]', 'import', 208);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addColumn', '[{"name":"name","type":"string","table":"holidays","schema":false,"nulls":false}]', 'import', 209);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addColumn', '[{"name":"holiday_id","type":"integer","table":"holidays","schema":false,"nulls":false}]', 'import', 210);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addAutoPrimaryKey', '[{"table":"holidays","schema":false,"column":"holiday_id"}]', 'import', 211);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addPrimaryKey', '[{"table":"holidays","schema":false,"columns":["holiday_id"],"name":"holidays_holiday_id_pk"}]', 'import', 212);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addTable', '[{"name":"configurations","schema":false}]', 'import', 213);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addColumn', '[{"name":"value","type":"string","table":"configurations","schema":false,"nulls":true}]', 'import', 214);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addColumn', '[{"name":"key","type":"string","table":"configurations","schema":false,"nulls":true}]', 'import', 215);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addColumn', '[{"name":"configuration_id","type":"integer","table":"configurations","schema":false,"nulls":false}]', 'import', 216);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addAutoPrimaryKey', '[{"table":"configurations","schema":false,"column":"configuration_id"}]', 'import', 217);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addPrimaryKey', '[{"table":"configurations","schema":false,"columns":["configuration_id"],"name":"configuration_id_pk"}]', 'import', 218);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addUniqueKey', '[{"table":"configurations","schema":false,"columns":["key"],"name":"key_uk"}]', 'import', 219);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addTable', '[{"name":"cheque_formats","schema":false}]', 'import', 220);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addColumn', '[{"name":"data","type":"text","table":"cheque_formats","schema":false,"nulls":false}]', 'import', 221);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addColumn', '[{"name":"description","type":"string","table":"cheque_formats","schema":false,"nulls":false}]', 'import', 222);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addColumn', '[{"name":"cheque_format_id","type":"integer","table":"cheque_formats","schema":false,"nulls":false}]', 'import', 223);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addAutoPrimaryKey', '[{"table":"cheque_formats","schema":false,"column":"cheque_format_id"}]', 'import', 224);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addPrimaryKey', '[{"table":"cheque_formats","schema":false,"columns":["cheque_format_id"],"name":"cheque_formats_cheque_format_id_pk"}]', 'import', 225);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addTable', '[{"name":"binary_objects","schema":false}]', 'import', 226);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addColumn', '[{"name":"data","type":"blob","table":"binary_objects","schema":false,"nulls":true}]', 'import', 227);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addColumn', '[{"name":"object_id","type":"integer","table":"binary_objects","schema":false,"nulls":false}]', 'import', 228);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addAutoPrimaryKey', '[{"table":"binary_objects","schema":false,"column":"object_id"}]', 'import', 229);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addPrimaryKey', '[{"table":"binary_objects","schema":false,"columns":["object_id"],"name":"binary_objects_pkey"}]', 'import', 230);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addTable', '[{"name":"audit_trail_data","schema":false}]', 'import', 231);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addColumn', '[{"name":"data","type":"text","table":"audit_trail_data","schema":false,"nulls":true}]', 'import', 232);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addColumn', '[{"name":"audit_trail_id","type":"integer","table":"audit_trail_data","schema":false,"nulls":false}]', 'import', 233);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addColumn', '[{"name":"audit_trail_data_id","type":"integer","table":"audit_trail_data","schema":false,"nulls":false}]', 'import', 234);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addAutoPrimaryKey', '[{"table":"audit_trail_data","schema":false,"column":"audit_trail_data_id"}]', 'import', 235);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addPrimaryKey', '[{"table":"audit_trail_data","schema":false,"columns":["audit_trail_data_id"],"name":"audit_trail_data_id_pk"}]', 'import', 236);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addIndex', '[{"table":"audit_trail_data","schema":false,"columns":["audit_trail_id"],"name":"audit_trail_id_idx"}]', 'import', 237);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addTable', '[{"name":"audit_trail","schema":false}]', 'import', 238);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addColumn', '[{"name":"data","type":"text","table":"audit_trail","schema":false,"nulls":true}]', 'import', 239);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addColumn', '[{"name":"type","type":"double","table":"audit_trail","schema":false,"nulls":false}]', 'import', 240);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addColumn', '[{"name":"audit_date","type":"timestamp","table":"audit_trail","schema":false,"nulls":false}]', 'import', 241);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addColumn', '[{"name":"description","type":"string","table":"audit_trail","schema":false,"nulls":false}]', 'import', 242);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addColumn', '[{"name":"item_type","type":"string","table":"audit_trail","schema":false,"nulls":false}]', 'import', 243);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addColumn', '[{"name":"item_id","type":"integer","table":"audit_trail","schema":false,"nulls":false}]', 'import', 244);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addColumn', '[{"name":"user_id","type":"integer","table":"audit_trail","schema":false,"nulls":false}]', 'import', 245);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addColumn', '[{"name":"audit_trail_id","type":"integer","table":"audit_trail","schema":false,"nulls":false}]', 'import', 246);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addAutoPrimaryKey', '[{"table":"audit_trail","schema":false,"column":"audit_trail_id"}]', 'import', 247);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addPrimaryKey', '[{"table":"audit_trail","schema":false,"columns":["audit_trail_id"],"name":"audit_trail_audit_id_pk"}]', 'import', 248);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addIndex', '[{"table":"audit_trail","schema":false,"columns":["item_id"],"name":"audit_trail_item_id_idx"}]', 'import', 249);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addTable', '[{"name":"locations","schema":false}]', 'import', 250);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addColumn', '[{"name":"address","type":"string","table":"locations","schema":false,"nulls":true}]', 'import', 251);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addColumn', '[{"name":"name","type":"string","table":"locations","schema":false,"nulls":true}]', 'import', 252);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addColumn', '[{"name":"location_id","type":"integer","table":"locations","schema":false,"nulls":false}]', 'import', 253);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addAutoPrimaryKey', '[{"table":"locations","schema":false,"column":"location_id"}]', 'import', 254);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addPrimaryKey', '[{"table":"locations","schema":false,"columns":["location_id"],"name":"location_id_pk"}]', 'import', 255);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addForeignKey', '[{"columns":["branch_id"],"table":"users","schema":false,"foreign_columns":["branch_id"],"foreign_table":"branches","foreign_schema":false,"name":"users_branch_id_fk","on_delete":"NO ACTION","on_update":"NO ACTION"}]', 'import', 256);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addForeignKey', '[{"columns":["department_id"],"table":"users","schema":false,"foreign_columns":["department_id"],"foreign_table":"departments","foreign_schema":false,"name":"users_dept_id_fk","on_delete":"NO ACTION","on_update":"NO ACTION"}]', 'import', 257);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addForeignKey', '[{"columns":["role_id"],"table":"users","schema":false,"foreign_columns":["role_id"],"foreign_table":"roles","foreign_schema":false,"name":"users_role_id_fk","on_delete":"NO ACTION","on_update":"NO ACTION"}]', 'import', 258);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addForeignKey', '[{"columns":["new_role_id"],"table":"temporary_roles","schema":false,"foreign_columns":["role_id"],"foreign_table":"roles","foreign_schema":false,"name":"temporary_rol_new_role_id_fk","on_delete":"NO ACTION","on_update":"NO ACTION"}]', 'import', 259);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addForeignKey', '[{"columns":["original_role_id"],"table":"temporary_roles","schema":false,"foreign_columns":["role_id"],"foreign_table":"roles","foreign_schema":false,"name":"temporary_rol_orig_role_id_fk","on_delete":"NO ACTION","on_update":"NO ACTION"}]', 'import', 260);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addForeignKey', '[{"columns":["user_id"],"table":"temporary_roles","schema":false,"foreign_columns":["user_id"],"foreign_table":"users","foreign_schema":false,"name":"temporary_roles_user_id_fk","on_delete":"NO ACTION","on_update":"NO ACTION"}]', 'import', 261);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addForeignKey', '[{"columns":["user_id"],"table":"suppliers","schema":false,"foreign_columns":["user_id"],"foreign_table":"users","foreign_schema":false,"name":"suppliers_user_id_fk","on_delete":"NO ACTION","on_update":"NO ACTION"}]', 'import', 262);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addForeignKey', '[{"columns":["role_id"],"table":"permissions","schema":false,"foreign_columns":["role_id"],"foreign_table":"roles","foreign_schema":false,"name":"permissios_role_id_fk","on_delete":"NO ACTION","on_update":"NO ACTION"}]', 'import', 263);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addForeignKey', '[{"columns":["user_id"],"table":"notes","schema":false,"foreign_columns":["user_id"],"foreign_table":"users","foreign_schema":false,"name":"notes_user_id_fk","on_delete":"NO ACTION","on_update":"NO ACTION"}]', 'import', 264);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addForeignKey', '[{"columns":["country_id"],"table":"regions","schema":false,"foreign_columns":["country_id"],"foreign_table":"countries","foreign_schema":false,"name":"regions_country_id_fk","on_delete":"NO ACTION","on_update":"NO ACTION"}]', 'import', 265);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addForeignKey', '[{"columns":["note_id"],"table":"note_attachments","schema":false,"foreign_columns":["note_id"],"foreign_table":"notes","foreign_schema":false,"name":"note_attachments_note_id_fkey","on_delete":"NO ACTION","on_update":"NO ACTION"}]', 'import', 266);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addForeignKey', '[{"columns":["branch_id"],"table":"clients","schema":false,"foreign_columns":["branch_id"],"foreign_table":"branches","foreign_schema":false,"name":"clients_branch_id_fkey","on_delete":"NO ACTION","on_update":"NO ACTION"}]', 'import', 267);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addForeignKey', '[{"columns":["city_id"],"table":"clients","schema":false,"foreign_columns":["city_id"],"foreign_table":"cities","foreign_schema":false,"name":"clients_city_id_fk","on_delete":"NO ACTION","on_update":"NO ACTION"}]', 'import', 268);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addForeignKey', '[{"columns":["country_id"],"table":"clients","schema":false,"foreign_columns":["country_id"],"foreign_table":"countries","foreign_schema":false,"name":"clients_country_id_fk","on_delete":"NO ACTION","on_update":"NO ACTION"}]', 'import', 269);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addForeignKey', '[{"columns":["id_type_id"],"table":"clients","schema":false,"foreign_columns":["id_type_id"],"foreign_table":"identification_types","foreign_schema":false,"name":"clients_id_type_id_fk","on_delete":"NO ACTION","on_update":"NO ACTION"}]', 'import', 270);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addForeignKey', '[{"columns":["nationality_id"],"table":"clients","schema":false,"foreign_columns":["country_id"],"foreign_table":"countries","foreign_schema":false,"name":"clients_nationality_id_fk","on_delete":"NO ACTION","on_update":"NO ACTION"}]', 'import', 271);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addForeignKey', '[{"columns":["main_client_id"],"table":"client_users","schema":false,"foreign_columns":["main_client_id"],"foreign_table":"clients","foreign_schema":false,"name":"client_users_main_client_id_fk","on_delete":"NO ACTION","on_update":"NO ACTION"}]', 'import', 272);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addForeignKey', '[{"columns":["id_type_id"],"table":"client_joint_accounts","schema":false,"foreign_columns":["id_type_id"],"foreign_table":"identification_types","foreign_schema":false,"name":"client_joint_id_type_id_fk","on_delete":"NO ACTION","on_update":"NO ACTION"}]', 'import', 273);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addForeignKey', '[{"columns":["main_client_id"],"table":"client_joint_accounts","schema":false,"foreign_columns":["main_client_id"],"foreign_table":"clients","foreign_schema":false,"name":"client_joint_main_client_id_fk","on_delete":"NO ACTION","on_update":"NO ACTION"}]', 'import', 274);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addForeignKey', '[{"columns":["region_id"],"table":"cities","schema":false,"foreign_columns":["region_id"],"foreign_table":"regions","foreign_schema":false,"name":"cities_region_id_fk","on_delete":"NO ACTION","on_update":"NO ACTION"}]', 'import', 275);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addForeignKey', '[{"columns":["bank_id"],"table":"bank_branches","schema":false,"foreign_columns":["bank_id"],"foreign_table":"banks","foreign_schema":false,"name":"branch_bank_id_fk","on_delete":"NO ACTION","on_update":"NO ACTION"}]', 'import', 276);
INSERT INTO yentu_history (session, version, method, arguments, migration, id) VALUES ('8ee169e5ba552e40f3b6744f2a269ff8a2f224f8', '12345678901234', 'addForeignKey', '[{"columns":["user_id"],"table":"api_keys","schema":false,"foreign_columns":["user_id"],"foreign_table":"users","foreign_schema":false,"name":"api_keys_user_id_fkey","on_delete":"NO ACTION","on_update":"NO ACTION"}]', 'import', 277);


ALTER TABLE banks
    ADD CONSTRAINT bank_name_uk UNIQUE (bank_name);

ALTER TABLE cities
    ADD CONSTRAINT city_name_uk UNIQUE (city_name);


ALTER TABLE countries
    ADD CONSTRAINT country_name_uk UNIQUE (country_name);

ALTER TABLE configurations
    ADD CONSTRAINT key_uk UNIQUE (`key`);

ALTER TABLE regions
    ADD CONSTRAINT region_name_uk UNIQUE (name);

ALTER TABLE users
    ADD CONSTRAINT user_name_uk UNIQUE (user_name);

CREATE INDEX audit_trail_id_idx ON audit_trail_data  (audit_trail_id);

CREATE INDEX audit_trail_item_id_idx ON audit_trail  (item_id);

ALTER TABLE api_keys
    ADD CONSTRAINT api_keys_user_id_fkey FOREIGN KEY (user_id) REFERENCES users(user_id) MATCH FULL;

ALTER TABLE bank_branches
    ADD CONSTRAINT branch_bank_id_fk FOREIGN KEY (bank_id) REFERENCES banks(bank_id) MATCH FULL;

ALTER TABLE cities
    ADD CONSTRAINT cities_region_id_fk FOREIGN KEY (region_id) REFERENCES regions(region_id) MATCH FULL;

ALTER TABLE client_joint_accounts
    ADD CONSTRAINT client_joint_id_type_id_fk FOREIGN KEY (id_type_id) REFERENCES identification_types(id_type_id) MATCH FULL;

ALTER TABLE client_joint_accounts
    ADD CONSTRAINT client_joint_main_client_id_fk FOREIGN KEY (main_client_id) REFERENCES clients(main_client_id) MATCH FULL;

ALTER TABLE client_users
    ADD CONSTRAINT client_users_main_client_id_fk FOREIGN KEY (main_client_id) REFERENCES clients(main_client_id) MATCH FULL;

ALTER TABLE clients
    ADD CONSTRAINT clients_branch_id_fkey FOREIGN KEY (branch_id) REFERENCES branches(branch_id) MATCH FULL;

ALTER TABLE clients
    ADD CONSTRAINT clients_city_id_fk FOREIGN KEY (city_id) REFERENCES cities(city_id) MATCH FULL;

ALTER TABLE clients
    ADD CONSTRAINT clients_country_id_fk FOREIGN KEY (country_id) REFERENCES countries(country_id) MATCH FULL;

ALTER TABLE clients
    ADD CONSTRAINT clients_id_type_id_fk FOREIGN KEY (id_type_id) REFERENCES identification_types(id_type_id) MATCH FULL;

ALTER TABLE clients
    ADD CONSTRAINT clients_nationality_id_fk FOREIGN KEY (nationality_id) REFERENCES countries(country_id) MATCH FULL;

ALTER TABLE note_attachments
    ADD CONSTRAINT note_attachments_note_id_fkey FOREIGN KEY (note_id) REFERENCES notes(note_id) MATCH FULL;

ALTER TABLE notes
    ADD CONSTRAINT notes_user_id_fk FOREIGN KEY (user_id) REFERENCES users(user_id) MATCH FULL;

ALTER TABLE permissions
    ADD CONSTRAINT permissios_role_id_fk FOREIGN KEY (role_id) REFERENCES roles(role_id) MATCH FULL;

ALTER TABLE regions
    ADD CONSTRAINT regions_country_id_fk FOREIGN KEY (country_id) REFERENCES countries(country_id) MATCH FULL;

ALTER TABLE suppliers
    ADD CONSTRAINT suppliers_user_id_fk FOREIGN KEY (user_id) REFERENCES users(user_id) MATCH FULL;

ALTER TABLE temporary_roles
    ADD CONSTRAINT temporary_rol_new_role_id_fk FOREIGN KEY (new_role_id) REFERENCES roles(role_id) MATCH FULL;

ALTER TABLE temporary_roles
    ADD CONSTRAINT temporary_rol_orig_role_id_fk FOREIGN KEY (original_role_id) REFERENCES roles(role_id) MATCH FULL;

ALTER TABLE temporary_roles
    ADD CONSTRAINT temporary_roles_user_id_fk FOREIGN KEY (user_id) REFERENCES users(user_id) MATCH FULL;

ALTER TABLE users
    ADD CONSTRAINT users_branch_id_fk FOREIGN KEY (branch_id) REFERENCES branches(branch_id) MATCH FULL;

ALTER TABLE users
    ADD CONSTRAINT users_dept_id_fk FOREIGN KEY (department_id) REFERENCES departments(department_id) MATCH FULL;

ALTER TABLE users
    ADD CONSTRAINT users_role_id_fk FOREIGN KEY (role_id) REFERENCES roles(role_id) MATCH FULL;

