SET client_encoding = 'UTF8';

CREATE SEQUENCE categories_category_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;

SET default_with_oids = false;

CREATE TABLE categories (
    category_id integer DEFAULT nextval('categories_category_id_seq'::regclass) NOT NULL,
    category_name character varying NOT NULL,
    category_code character varying
);

CREATE SEQUENCE departments_department_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;

CREATE TABLE departments (
    department_id integer DEFAULT nextval('departments_department_id_seq'::regclass) NOT NULL,
    department_code character varying,
    department_name character varying NOT NULL
);

CREATE SEQUENCE employees_employee_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;

CREATE TABLE employees (
    employee_id integer DEFAULT nextval('employees_employee_id_seq'::regclass) NOT NULL,
    code character varying,
    title character varying,
    firstname character varying NOT NULL,
    lastname character varying NOT NULL,
    othernames character varying,
    date_of_birth date,
    marital_status character varying,
    basic_salary numeric,
    number_of_dependent_children integer,
    number_of_dependent_relatives integer,
    physically_challenged boolean,
    under_training boolean,
    department_id integer,
    category_id integer,
    job_location_id integer,
    job_title_id integer,
    social_security_number character varying,
    tax_identification_number character varying,
    mailing_address text,
    residential_address text,
    telephone character varying,
    email character varying,
    bank_branch_id integer,
    bank_account_number character varying,
    disabled boolean NOT NULL
);

CREATE VIEW employees_view AS
 SELECT employees.employee_id,
    employees.code,
    btrim((((((COALESCE(employees.title, ''::character varying))::text || ' '::text) || (COALESCE(employees.lastname, ''::character varying))::text) || ' '::text) || (COALESCE(employees.firstname, ''::character varying))::text)) AS employee
   FROM employees
  WHERE (employees.disabled <> true);

CREATE SEQUENCE job_locations_job_location_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;

CREATE TABLE job_locations (
    job_location_id integer DEFAULT nextval('job_locations_job_location_id_seq'::regclass) NOT NULL,
    job_location_name character varying NOT NULL,
    job_location_code character varying
);

CREATE SEQUENCE job_titles_job_title_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;

CREATE TABLE job_titles (
    job_title_id integer DEFAULT nextval('job_titles_job_title_id_seq'::regclass) NOT NULL,
    job_title_name character varying NOT NULL,
    job_title_code character varying
);

CREATE SEQUENCE bank_branches_bank_branch_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;

CREATE TABLE bank_branches (
    bank_branch_id integer DEFAULT nextval('bank_branches_bank_branch_id_seq'::regclass) NOT NULL,
    bank_id integer NOT NULL,
    name character varying NOT NULL,
    address character varying,
    sort_code character varying NOT NULL
);

CREATE SEQUENCE banks_bank_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;

CREATE TABLE banks (
    bank_id integer DEFAULT nextval('banks_bank_id_seq'::regclass) NOT NULL,
    bank_name character varying NOT NULL
);

CREATE VIEW disabled_employees_view AS
 SELECT employees.employee_id,
    employees.code,
    btrim((((((COALESCE(employees.title, ''::character varying))::text || ' '::text) || (COALESCE(employees.lastname, ''::character varying))::text) || ' '::text) || (COALESCE(employees.firstname, ''::character varying))::text)) AS employee
   FROM employees
  WHERE (employees.disabled = true);

CREATE SEQUENCE employee_code_seq
    START WITH 615
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;

ALTER TABLE ONLY bank_branches
    ADD CONSTRAINT bank_branch_id_pk PRIMARY KEY (bank_branch_id);

ALTER TABLE ONLY banks
    ADD CONSTRAINT bank_id_pk PRIMARY KEY (bank_id);

ALTER TABLE ONLY banks
    ADD CONSTRAINT bank_name_uk UNIQUE (bank_name);

ALTER TABLE ONLY categories
    ADD CONSTRAINT categories_category_name_key UNIQUE (category_name);

ALTER TABLE ONLY categories
    ADD CONSTRAINT categories_code_key UNIQUE (category_code);

ALTER TABLE ONLY categories
    ADD CONSTRAINT categories_pkey PRIMARY KEY (category_id);

ALTER TABLE ONLY departments
    ADD CONSTRAINT departments_code_key UNIQUE (department_code);

ALTER TABLE ONLY departments
    ADD CONSTRAINT departments_department_name_key UNIQUE (department_name);

ALTER TABLE ONLY departments
    ADD CONSTRAINT departments_pkey PRIMARY KEY (department_id);

ALTER TABLE ONLY employees
    ADD CONSTRAINT employees_code_key UNIQUE (code);

ALTER TABLE ONLY employees
    ADD CONSTRAINT employees_pkey PRIMARY KEY (employee_id);

ALTER TABLE ONLY job_locations
    ADD CONSTRAINT job_locations_code_key UNIQUE (job_location_code);

ALTER TABLE ONLY job_locations
    ADD CONSTRAINT job_locations_job_location_name_key UNIQUE (job_location_name);

ALTER TABLE ONLY job_locations
    ADD CONSTRAINT job_locations_pkey PRIMARY KEY (job_location_id);

ALTER TABLE ONLY job_titles
    ADD CONSTRAINT job_titles_code_key UNIQUE (job_title_code);

ALTER TABLE ONLY job_titles
    ADD CONSTRAINT job_titles_job_title_name_key UNIQUE (job_title_name);

ALTER TABLE ONLY job_titles
    ADD CONSTRAINT job_titles_pkey PRIMARY KEY (job_title_id);

ALTER TABLE ONLY bank_branches
    ADD CONSTRAINT branch_bank_id_fk FOREIGN KEY (bank_id) REFERENCES banks(bank_id) MATCH FULL;

ALTER TABLE ONLY employees
    ADD CONSTRAINT employees_bank_branch_id_fkey FOREIGN KEY (bank_branch_id) REFERENCES bank_branches(bank_branch_id) MATCH FULL;

ALTER TABLE ONLY employees
    ADD CONSTRAINT employees_category_id_categories_category_id_fk FOREIGN KEY (category_id) REFERENCES categories(category_id) MATCH FULL ON UPDATE SET NULL ON DELETE SET NULL;

ALTER TABLE ONLY employees
    ADD CONSTRAINT employees_department_id_departments_department_id_fk FOREIGN KEY (department_id) REFERENCES departments(department_id) MATCH FULL ON UPDATE SET NULL ON DELETE SET NULL;

ALTER TABLE ONLY employees
    ADD CONSTRAINT employees_job_location_id_fkey FOREIGN KEY (job_location_id) REFERENCES job_locations(job_location_id) MATCH FULL;

ALTER TABLE ONLY employees
    ADD CONSTRAINT employees_job_title_id_fkey FOREIGN KEY (job_title_id) REFERENCES job_titles(job_title_id) MATCH FULL;

