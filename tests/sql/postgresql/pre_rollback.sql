CREATE SEQUENCE api_keys_api_key_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;

CREATE TABLE api_keys (
    secret character varying NOT NULL,
    key character varying NOT NULL,
    active boolean NOT NULL,
    user_id integer NOT NULL,
    api_key_id integer DEFAULT nextval('api_keys_api_key_id_seq'::regclass) NOT NULL
);

CREATE SEQUENCE audit_trail_audit_trail_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;

CREATE TABLE audit_trail (
    data text,
    type numeric NOT NULL,
    audit_date timestamp with time zone NOT NULL,
    description character varying NOT NULL,
    item_type character varying NOT NULL,
    item_id integer NOT NULL,
    user_id integer NOT NULL,
    audit_trail_id integer DEFAULT nextval('audit_trail_audit_trail_id_seq'::regclass) NOT NULL
);

CREATE SEQUENCE audit_trail_data_audit_trail_data_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;

CREATE TABLE audit_trail_data (
    data text,
    audit_trail_id integer NOT NULL,
    audit_trail_data_id integer DEFAULT nextval('audit_trail_data_audit_trail_data_id_seq'::regclass) NOT NULL
);

CREATE SEQUENCE bank_branches_bank_branch_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;

CREATE TABLE bank_branches (
    sort_code character varying NOT NULL,
    address character varying,
    branch_name character varying NOT NULL,
    bank_id integer NOT NULL,
    bank_branch_id integer DEFAULT nextval('bank_branches_bank_branch_id_seq'::regclass) NOT NULL
);

CREATE SEQUENCE banks_bank_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;

CREATE TABLE banks (
    swift_code character varying,
    bank_code character varying NOT NULL,
    bank_name character varying NOT NULL,
    bank_id integer DEFAULT nextval('banks_bank_id_seq'::regclass) NOT NULL
);

CREATE SEQUENCE binary_objects_object_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;

CREATE TABLE binary_objects (
    data bytea,
    object_id integer DEFAULT nextval('binary_objects_object_id_seq'::regclass) NOT NULL
);

CREATE SEQUENCE branches_branch_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;

CREATE TABLE branches (
    city_id integer,
    address text,
    branch_name character varying NOT NULL,
    branch_id integer DEFAULT nextval('branches_branch_id_seq'::regclass) NOT NULL
);

CREATE SEQUENCE cheque_formats_cheque_format_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;

CREATE TABLE cheque_formats (
    data text NOT NULL,
    description character varying NOT NULL,
    cheque_format_id integer DEFAULT nextval('cheque_formats_cheque_format_id_seq'::regclass) NOT NULL
);

CREATE SEQUENCE cities_city_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;

CREATE TABLE cities (
    region_id integer NOT NULL,
    city_name character varying NOT NULL,
    city_id integer DEFAULT nextval('cities_city_id_seq'::regclass) NOT NULL
);

CREATE SEQUENCE client_joint_accounts_joint_account_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;

CREATE TABLE client_joint_accounts (
    mobile character varying,
    id_issue_date date,
    id_issue_place character varying,
    id_number character varying,
    id_type_id integer,
    office_telephone character varying,
    telephone character varying,
    address character varying,
    previous_names character varying,
    other_names character varying,
    first_name character varying NOT NULL,
    surname character varying NOT NULL,
    title character varying,
    main_client_id integer NOT NULL,
    joint_account_id integer DEFAULT nextval('client_joint_accounts_joint_account_id_seq'::regclass) NOT NULL
);

CREATE SEQUENCE client_users_client_user_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;

CREATE TABLE client_users (
    status integer NOT NULL,
    user_name character varying NOT NULL,
    password character varying NOT NULL,
    membership_id integer,
    main_client_id integer,
    client_user_id integer DEFAULT nextval('client_users_client_user_id_seq'::regclass) NOT NULL
);

CREATE SEQUENCE clients_main_client_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;

CREATE TABLE clients (
    branch_id integer NOT NULL,
    marital_status numeric,
    gender numeric,
    nationality_id integer,
    company_name character varying,
    contact_person character varying,
    in_trust_for character varying,
    country_id integer,
    id_issue_date date,
    id_issue_place character varying,
    signature character varying,
    scanned_id character varying,
    picture character varying,
    occupation character varying,
    residential_status numeric,
    id_number character varying NOT NULL,
    id_type_id integer NOT NULL,
    city_id integer,
    birth_date date,
    email character varying,
    fax character varying,
    mobile character varying,
    contact_tel character varying,
    residential_address character varying,
    mailing_address character varying NOT NULL,
    previous_names character varying,
    other_names character varying,
    first_name character varying,
    surname character varying,
    title character varying,
    account_type numeric NOT NULL,
    main_client_id integer DEFAULT nextval('clients_main_client_id_seq'::regclass) NOT NULL
);

CREATE SEQUENCE configurations_configuration_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;

CREATE TABLE configurations (
    value character varying,
    key character varying,
    configuration_id integer DEFAULT nextval('configurations_configuration_id_seq'::regclass) NOT NULL
);

CREATE SEQUENCE countries_country_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;

CREATE TABLE countries (
    currency_symbol character varying,
    currency character varying,
    nationality character varying,
    country_code character varying,
    country_name character varying NOT NULL,
    country_id integer DEFAULT nextval('countries_country_id_seq'::regclass) NOT NULL
);

CREATE SEQUENCE departments_department_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;

CREATE TABLE departments (
    department_name character varying NOT NULL,
    department_id integer DEFAULT nextval('departments_department_id_seq'::regclass) NOT NULL
);

CREATE SEQUENCE holidays_holiday_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;

CREATE TABLE holidays (
    holiday_date date NOT NULL,
    name character varying NOT NULL,
    holiday_id integer DEFAULT nextval('holidays_holiday_id_seq'::regclass) NOT NULL
);

CREATE SEQUENCE identification_types_id_type_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;

CREATE TABLE identification_types (
    id_name character varying NOT NULL,
    id_type_id integer DEFAULT nextval('identification_types_id_type_id_seq'::regclass) NOT NULL
);

CREATE SEQUENCE locations_location_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;

CREATE TABLE locations (
    address character varying,
    name character varying,
    location_id integer DEFAULT nextval('locations_location_id_seq'::regclass) NOT NULL
);

CREATE SEQUENCE note_attachments_note_attachment_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;

CREATE TABLE note_attachments (
    object_id integer,
    note_id integer,
    description character varying,
    note_attachment_id integer DEFAULT nextval('note_attachments_note_attachment_id_seq'::regclass) NOT NULL
);

CREATE SEQUENCE notes_note_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;

CREATE TABLE notes (
    user_id integer NOT NULL,
    item_type character varying NOT NULL,
    item_id integer NOT NULL,
    note_time timestamp with time zone NOT NULL,
    note character varying,
    note_id integer DEFAULT nextval('notes_note_id_seq'::regclass) NOT NULL
);

CREATE SEQUENCE notifications_notification_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;

CREATE TABLE notifications (
    subject character varying,
    email text,
    sms text,
    tag character varying,
    notification_id integer DEFAULT nextval('notifications_notification_id_seq'::regclass) NOT NULL
);

CREATE SEQUENCE permissions_permission_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;

CREATE TABLE permissions (
    module character varying,
    value numeric NOT NULL,
    permission character varying,
    role_id integer NOT NULL,
    permission_id integer DEFAULT nextval('permissions_permission_id_seq'::regclass) NOT NULL
);

CREATE SEQUENCE regions_region_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;

CREATE TABLE regions (
    country_id integer NOT NULL,
    name character varying NOT NULL,
    region_code character varying NOT NULL,
    region_id integer DEFAULT nextval('regions_region_id_seq'::regclass) NOT NULL
);

CREATE SEQUENCE relationships_relationship_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;

CREATE TABLE relationships (
    relationship_name character varying NOT NULL,
    relationship_id integer DEFAULT nextval('relationships_relationship_id_seq'::regclass) NOT NULL
);

CREATE SEQUENCE roles_role_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;

CREATE TABLE roles (
    role_name character varying,
    role_id integer DEFAULT nextval('roles_role_id_seq'::regclass) NOT NULL
);

CREATE SEQUENCE suppliers_supplier_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;

CREATE TABLE suppliers (
    user_id integer,
    telephone character varying,
    address character varying,
    supplier_name character varying NOT NULL,
    supplier_id integer DEFAULT nextval('suppliers_supplier_id_seq'::regclass) NOT NULL
);

CREATE SEQUENCE temporary_roles_temporary_role_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;

CREATE TABLE temporary_roles (
    tag character varying,
    original_role_id integer,
    active boolean,
    expires timestamp with time zone,
    created timestamp with time zone,
    user_id integer,
    new_role_id integer,
    temporary_role_id integer DEFAULT nextval('temporary_roles_temporary_role_id_seq'::regclass) NOT NULL
);

CREATE SEQUENCE users_user_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;

CREATE TABLE users (
    branch_id integer,
    picture_id integer,
    department_id integer,
    phone character varying,
    email character varying NOT NULL,
    user_status numeric,
    other_names character varying,
    last_name character varying NOT NULL,
    first_name character varying NOT NULL,
    role_id integer,
    password character varying NOT NULL,
    user_name character varying NOT NULL,
    user_id integer DEFAULT nextval('users_user_id_seq'::regclass) NOT NULL
);

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
    default_schema character varying,
    id integer DEFAULT nextval('yentu_history_id_seq'::regclass) NOT NULL
);

SELECT pg_catalog.setval('api_keys_api_key_id_seq', 1, false);

SELECT pg_catalog.setval('audit_trail_audit_trail_id_seq', 1, false);

SELECT pg_catalog.setval('audit_trail_data_audit_trail_data_id_seq', 1, false);

SELECT pg_catalog.setval('bank_branches_bank_branch_id_seq', 1, false);

SELECT pg_catalog.setval('banks_bank_id_seq', 1, false);

SELECT pg_catalog.setval('binary_objects_object_id_seq', 1, false);

SELECT pg_catalog.setval('branches_branch_id_seq', 1, false);

SELECT pg_catalog.setval('cheque_formats_cheque_format_id_seq', 1, false);

SELECT pg_catalog.setval('cities_city_id_seq', 1, false);

SELECT pg_catalog.setval('client_joint_accounts_joint_account_id_seq', 1, false);

SELECT pg_catalog.setval('client_users_client_user_id_seq', 1, false);

SELECT pg_catalog.setval('clients_main_client_id_seq', 1, false);

SELECT pg_catalog.setval('configurations_configuration_id_seq', 1, false);

SELECT pg_catalog.setval('countries_country_id_seq', 1, false);

SELECT pg_catalog.setval('departments_department_id_seq', 1, false);

SELECT pg_catalog.setval('holidays_holiday_id_seq', 1, false);

SELECT pg_catalog.setval('identification_types_id_type_id_seq', 1, false);

SELECT pg_catalog.setval('locations_location_id_seq', 1, false);

SELECT pg_catalog.setval('note_attachments_note_attachment_id_seq', 1, false);

SELECT pg_catalog.setval('notes_note_id_seq', 1, false);

SELECT pg_catalog.setval('notifications_notification_id_seq', 1, false);

SELECT pg_catalog.setval('permissions_permission_id_seq', 1, false);

SELECT pg_catalog.setval('regions_region_id_seq', 1, false);

SELECT pg_catalog.setval('relationships_relationship_id_seq', 1, false);

SELECT pg_catalog.setval('roles_role_id_seq', 1, false);

SELECT pg_catalog.setval('suppliers_supplier_id_seq', 1, false);

SELECT pg_catalog.setval('temporary_roles_temporary_role_id_seq', 1, false);

SELECT pg_catalog.setval('users_user_id_seq', 1, false);

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

SELECT pg_catalog.setval('yentu_history_id_seq', 277, true);

ALTER TABLE ONLY api_keys
    ADD CONSTRAINT api_keys_pkey PRIMARY KEY (api_key_id);

ALTER TABLE ONLY audit_trail
    ADD CONSTRAINT audit_trail_audit_id_pk PRIMARY KEY (audit_trail_id);

ALTER TABLE ONLY audit_trail_data
    ADD CONSTRAINT audit_trail_data_id_pk PRIMARY KEY (audit_trail_data_id);

ALTER TABLE ONLY bank_branches
    ADD CONSTRAINT bank_branch_id_pk PRIMARY KEY (bank_branch_id);

ALTER TABLE ONLY banks
    ADD CONSTRAINT bank_id_pk PRIMARY KEY (bank_id);

ALTER TABLE ONLY banks
    ADD CONSTRAINT bank_name_uk UNIQUE (bank_name);

ALTER TABLE ONLY binary_objects
    ADD CONSTRAINT binary_objects_pkey PRIMARY KEY (object_id);

ALTER TABLE ONLY branches
    ADD CONSTRAINT branches_pkey PRIMARY KEY (branch_id);

ALTER TABLE ONLY cheque_formats
    ADD CONSTRAINT cheque_formats_cheque_format_id_pk PRIMARY KEY (cheque_format_id);

ALTER TABLE ONLY cities
    ADD CONSTRAINT city_id_pk PRIMARY KEY (city_id);

ALTER TABLE ONLY cities
    ADD CONSTRAINT city_name_uk UNIQUE (city_name);

ALTER TABLE ONLY client_users
    ADD CONSTRAINT client_users_client_user_id_pk PRIMARY KEY (client_user_id);

ALTER TABLE ONLY clients
    ADD CONSTRAINT clients_main_client_id_pk PRIMARY KEY (main_client_id);

ALTER TABLE ONLY configurations
    ADD CONSTRAINT configuration_id_pk PRIMARY KEY (configuration_id);

ALTER TABLE ONLY countries
    ADD CONSTRAINT country_id_pk PRIMARY KEY (country_id);

ALTER TABLE ONLY countries
    ADD CONSTRAINT country_name_uk UNIQUE (country_name);

ALTER TABLE ONLY departments
    ADD CONSTRAINT department_id_pk PRIMARY KEY (department_id);

ALTER TABLE ONLY holidays
    ADD CONSTRAINT holidays_holiday_id_pk PRIMARY KEY (holiday_id);

ALTER TABLE ONLY identification_types
    ADD CONSTRAINT id_type_id_pk PRIMARY KEY (id_type_id);

ALTER TABLE ONLY client_joint_accounts
    ADD CONSTRAINT joint_account_id_pk PRIMARY KEY (joint_account_id);

ALTER TABLE ONLY configurations
    ADD CONSTRAINT key_uk UNIQUE (key);

ALTER TABLE ONLY locations
    ADD CONSTRAINT location_id_pk PRIMARY KEY (location_id);

ALTER TABLE ONLY note_attachments
    ADD CONSTRAINT note_attachments_pkey PRIMARY KEY (note_attachment_id);

ALTER TABLE ONLY notes
    ADD CONSTRAINT notes_note_id_pk PRIMARY KEY (note_id);

ALTER TABLE ONLY notifications
    ADD CONSTRAINT notifications_pkey PRIMARY KEY (notification_id);

ALTER TABLE ONLY permissions
    ADD CONSTRAINT perm_id_pk PRIMARY KEY (permission_id);

ALTER TABLE ONLY regions
    ADD CONSTRAINT region_id_pk PRIMARY KEY (region_id);

ALTER TABLE ONLY regions
    ADD CONSTRAINT region_name_uk UNIQUE (name);

ALTER TABLE ONLY relationships
    ADD CONSTRAINT relationship_id_pk PRIMARY KEY (relationship_id);

ALTER TABLE ONLY roles
    ADD CONSTRAINT role_id_pk PRIMARY KEY (role_id);

ALTER TABLE ONLY suppliers
    ADD CONSTRAINT supplier_id_pk PRIMARY KEY (supplier_id);

ALTER TABLE ONLY temporary_roles
    ADD CONSTRAINT temporary_role_id_pk PRIMARY KEY (temporary_role_id);

ALTER TABLE ONLY users
    ADD CONSTRAINT user_id_pk PRIMARY KEY (user_id);

ALTER TABLE ONLY users
    ADD CONSTRAINT user_name_uk UNIQUE (user_name);

ALTER TABLE ONLY yentu_history
    ADD CONSTRAINT yentu_history_pk PRIMARY KEY (id);

CREATE INDEX audit_trail_id_idx ON audit_trail_data USING btree (audit_trail_id);

CREATE INDEX audit_trail_item_id_idx ON audit_trail USING btree (item_id);

ALTER TABLE ONLY api_keys
    ADD CONSTRAINT api_keys_user_id_fkey FOREIGN KEY (user_id) REFERENCES users(user_id) MATCH FULL;

ALTER TABLE ONLY bank_branches
    ADD CONSTRAINT branch_bank_id_fk FOREIGN KEY (bank_id) REFERENCES banks(bank_id) MATCH FULL;

ALTER TABLE ONLY cities
    ADD CONSTRAINT cities_region_id_fk FOREIGN KEY (region_id) REFERENCES regions(region_id) MATCH FULL;

ALTER TABLE ONLY client_joint_accounts
    ADD CONSTRAINT client_joint_id_type_id_fk FOREIGN KEY (id_type_id) REFERENCES identification_types(id_type_id) MATCH FULL;

ALTER TABLE ONLY client_joint_accounts
    ADD CONSTRAINT client_joint_main_client_id_fk FOREIGN KEY (main_client_id) REFERENCES clients(main_client_id) MATCH FULL;

ALTER TABLE ONLY client_users
    ADD CONSTRAINT client_users_main_client_id_fk FOREIGN KEY (main_client_id) REFERENCES clients(main_client_id) MATCH FULL;

ALTER TABLE ONLY clients
    ADD CONSTRAINT clients_branch_id_fkey FOREIGN KEY (branch_id) REFERENCES branches(branch_id) MATCH FULL;

ALTER TABLE ONLY clients
    ADD CONSTRAINT clients_city_id_fk FOREIGN KEY (city_id) REFERENCES cities(city_id) MATCH FULL;

ALTER TABLE ONLY clients
    ADD CONSTRAINT clients_country_id_fk FOREIGN KEY (country_id) REFERENCES countries(country_id) MATCH FULL;

ALTER TABLE ONLY clients
    ADD CONSTRAINT clients_id_type_id_fk FOREIGN KEY (id_type_id) REFERENCES identification_types(id_type_id) MATCH FULL;

ALTER TABLE ONLY clients
    ADD CONSTRAINT clients_nationality_id_fk FOREIGN KEY (nationality_id) REFERENCES countries(country_id) MATCH FULL;

ALTER TABLE ONLY note_attachments
    ADD CONSTRAINT note_attachments_note_id_fkey FOREIGN KEY (note_id) REFERENCES notes(note_id) MATCH FULL;

ALTER TABLE ONLY notes
    ADD CONSTRAINT notes_user_id_fk FOREIGN KEY (user_id) REFERENCES users(user_id) MATCH FULL;

ALTER TABLE ONLY permissions
    ADD CONSTRAINT permissios_role_id_fk FOREIGN KEY (role_id) REFERENCES roles(role_id) MATCH FULL;

ALTER TABLE ONLY regions
    ADD CONSTRAINT regions_country_id_fk FOREIGN KEY (country_id) REFERENCES countries(country_id) MATCH FULL;

ALTER TABLE ONLY suppliers
    ADD CONSTRAINT suppliers_user_id_fk FOREIGN KEY (user_id) REFERENCES users(user_id) MATCH FULL;

ALTER TABLE ONLY temporary_roles
    ADD CONSTRAINT temporary_rol_new_role_id_fk FOREIGN KEY (new_role_id) REFERENCES roles(role_id) MATCH FULL;

ALTER TABLE ONLY temporary_roles
    ADD CONSTRAINT temporary_rol_orig_role_id_fk FOREIGN KEY (original_role_id) REFERENCES roles(role_id) MATCH FULL;

ALTER TABLE ONLY temporary_roles
    ADD CONSTRAINT temporary_roles_user_id_fk FOREIGN KEY (user_id) REFERENCES users(user_id) MATCH FULL;

ALTER TABLE ONLY users
    ADD CONSTRAINT users_branch_id_fk FOREIGN KEY (branch_id) REFERENCES branches(branch_id) MATCH FULL;

ALTER TABLE ONLY users
    ADD CONSTRAINT users_dept_id_fk FOREIGN KEY (department_id) REFERENCES departments(department_id) MATCH FULL;

ALTER TABLE ONLY users
    ADD CONSTRAINT users_role_id_fk FOREIGN KEY (role_id) REFERENCES roles(role_id) MATCH FULL;

