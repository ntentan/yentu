CREATE TABLE categories (
    category_id integer primary key autoincrement,
    category_name text(255) unique NOT NULL,
    category_code text(255) unique
);

CREATE TABLE departments (
    department_id integer primary key autoincrement,
    department_code text(255) unique,
    department_name text(255) unique NOT NULL
);

CREATE TABLE employees (
    employee_id integer primary key autoincrement,
    code text(255) unique not null,
    title text(255),
    firstname text(255) NOT NULL,
    lastname text(255) NOT NULL,
    othernames text(255),
    date_of_birth text,
    marital_status text(255),
    basic_salary integer,
    number_of_dependent_children integer,
    number_of_dependent_relatives integer,
    physically_challenged integer,
    under_training integer,
    department_id integer,
    category_id integer,
    job_location_id integer,
    job_title_id integer,
    social_security_number text(255),
    tax_identification_number text(255),
    mailing_address text,
    residential_address text,
    telephone text(255),
    email text(255),
    bank_branch_id integer,
    bank_account_number text(255),
    disabled integer NOT NULL,
    CONSTRAINT employees_bank_branch_id_fkey FOREIGN KEY (bank_branch_id) REFERENCES bank_branches(bank_branch_id),
    CONSTRAINT employees_category_id_categories_category_id_fk FOREIGN KEY (category_id) REFERENCES categories(category_id) ON UPDATE SET NULL ON DELETE SET NULL,
    CONSTRAINT employees_department_id_departments_department_id_fk FOREIGN KEY (department_id) REFERENCES departments(department_id) ON UPDATE SET NULL ON DELETE SET NULL,
    CONSTRAINT employees_job_location_id_fkey FOREIGN KEY (job_location_id) REFERENCES job_locations(job_location_id),
    CONSTRAINT employees_job_title_id_fkey FOREIGN KEY (job_title_id) REFERENCES job_titles(job_title_id)
);

CREATE VIEW employees_view AS
 SELECT employees.employee_id,
    employees.code,
    trim((((((COALESCE(employees.title, '')) || ' ') || (COALESCE(employees.lastname, ''))) || ' ') || (COALESCE(employees.firstname, '')))) AS employee
   FROM employees
  WHERE (employees.disabled <> 1);

CREATE TABLE job_locations (
    job_location_id integer primary key autoincrement,
    job_location_name text(255) unique NOT NULL,
    job_location_code text(255) unique
);

CREATE TABLE job_titles (
    job_title_id integer primary key autoincrement,
    job_title_name text(255) unique NOT NULL,
    job_title_code text(255) unique
);

CREATE TABLE bank_branches (
    bank_branch_id integer primary key autoincrement,
    bank_id integer NOT NULL,
    name text(255) unique NOT NULL,
    address text(255),
    sort_code text(255) NOT NULL,
    CONSTRAINT branch_bank_id_fk FOREIGN KEY (bank_id) REFERENCES banks(bank_id)
);

CREATE TABLE banks (
    bank_id integer primary key autoincrement,
    bank_name text(255) NOT NULL
);

CREATE VIEW disabled_employees_view AS
 SELECT employees.employee_id,
    employees.code,
    trim((((((COALESCE(employees.title, '')) || ' ') || (COALESCE(employees.lastname, ''))) || ' ') || (COALESCE(employees.firstname, '')))) AS employee
   FROM employees
  WHERE (employees.disabled = 1);

