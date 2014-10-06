CREATE TABLE categories (
    category_id integer not null auto_increment primary key,
    category_name varchar(255) NOT NULL,
    category_code varchar(255)
);

CREATE TABLE departments (
    department_id integer not null auto_increment primary key,
    department_code varchar(255),
    department_name varchar(255) NOT NULL
);

CREATE TABLE employees (
    employee_id integer not null auto_increment primary key,
    code varchar(255),
    title varchar(255),
    firstname varchar(255) NOT NULL,
    lastname varchar(255) NOT NULL,
    othernames varchar(255),
    date_of_birth date,
    marital_status varchar(255),
    basic_salary numeric,
    number_of_dependent_children integer,
    number_of_dependent_relatives integer,
    physically_challenged boolean,
    under_training boolean,
    department_id integer,
    category_id integer,
    job_location_id integer,
    job_title_id integer,
    social_security_number varchar(255),
    tax_identification_number varchar(255),
    mailing_address text,
    residential_address text,
    telephone varchar(255),
    email varchar(255),
    bank_branch_id integer,
    bank_account_number varchar(255),
    disabled boolean NOT NULL
);

CREATE VIEW employees_view AS
 SELECT employees.employee_id,
    employees.code,
    trim((((((COALESCE(employees.title, '')) || ' ') || (COALESCE(employees.lastname, ''))) || ' ') || (COALESCE(employees.firstname, '')))) AS employee
   FROM employees
  WHERE (employees.disabled <> true);

CREATE TABLE job_locations (
    job_location_id integer not null auto_increment primary key,
    job_location_name varchar(255) NOT NULL,
    job_location_code varchar(255)
);

CREATE TABLE job_titles (
    job_title_id integer not null auto_increment primary key,
    job_title_name varchar(255) NOT NULL,
    job_title_code varchar(255)
);

CREATE TABLE bank_branches (
    bank_branch_id integer not null auto_increment primary key,
    bank_id integer NOT NULL,
    name varchar(255) NOT NULL,
    address varchar(255),
    sort_code varchar(255) NOT NULL
);

CREATE TABLE banks (
    bank_id integer not null auto_increment primary key,
    bank_name varchar(255) NOT NULL
);

CREATE VIEW disabled_employees_view AS
 SELECT employees.employee_id,
    employees.code,
    trim((((((COALESCE(employees.title, '')) || ' ') || (COALESCE(employees.lastname, ''))) || ' ') || (COALESCE(employees.firstname, '')))) AS employee
   FROM employees
  WHERE (employees.disabled = true);

ALTER TABLE banks
    ADD CONSTRAINT bank_name_uk UNIQUE (bank_name);

ALTER TABLE categories
    ADD CONSTRAINT categories_category_name_key UNIQUE (category_name);

ALTER TABLE categories
    ADD CONSTRAINT categories_code_key UNIQUE (category_code);

ALTER TABLE departments
    ADD CONSTRAINT departments_code_key UNIQUE (department_code);

ALTER TABLE departments
    ADD CONSTRAINT departments_department_name_key UNIQUE (department_name);

ALTER TABLE employees
    ADD CONSTRAINT employees_code_key UNIQUE (code);

ALTER TABLE job_locations
    ADD CONSTRAINT job_locations_code_key UNIQUE (job_location_code);

ALTER TABLE job_locations
    ADD CONSTRAINT job_locations_job_location_name_key UNIQUE (job_location_name);

ALTER TABLE job_titles
    ADD CONSTRAINT job_titles_code_key UNIQUE (job_title_code);

ALTER TABLE job_titles
    ADD CONSTRAINT job_titles_job_title_name_key UNIQUE (job_title_name);

ALTER TABLE bank_branches
    ADD CONSTRAINT branch_bank_id_fk FOREIGN KEY (bank_id) REFERENCES banks(bank_id) MATCH FULL;

ALTER TABLE employees
    ADD CONSTRAINT employees_bank_branch_id_fkey FOREIGN KEY (bank_branch_id) REFERENCES bank_branches(bank_branch_id) MATCH FULL;

ALTER TABLE employees
    ADD CONSTRAINT employees_category_id_categories_category_id_fk FOREIGN KEY (category_id) REFERENCES categories(category_id) MATCH FULL ON UPDATE SET NULL ON DELETE SET NULL;

ALTER TABLE employees
    ADD CONSTRAINT employees_department_id_departments_department_id_fk FOREIGN KEY (department_id) REFERENCES departments(department_id) MATCH FULL ON UPDATE SET NULL ON DELETE SET NULL;

ALTER TABLE employees
    ADD CONSTRAINT employees_job_location_id_fkey FOREIGN KEY (job_location_id) REFERENCES job_locations(job_location_id) MATCH FULL;

ALTER TABLE employees
    ADD CONSTRAINT employees_job_title_id_fkey FOREIGN KEY (job_title_id) REFERENCES job_titles(job_title_id) MATCH FULL;

