Getting Started with Yentu
==========================
Yentu manipulates databases through migration scripts. Migrations are simple PHP scripts
which contain instructions on what to do when database objects are created or removed.
For yentu to work in your project, you need a place to store database configuration
files and the migration scripts.

There are two major ways of getting your project to work with yentu. You can either 
write a new set of migrations or import an existing schema from an existing database 
for your initial migration. The approach you choose depends on how far you have
gone with your project. Projects which already have an existing schema can import
their schema into a new migration other projects can just start writing migrations
straight away.

Importing a Schema
------------------
Assuming your project already has a schema, you can integrate yentu by importing
that schema as an initial migration. Execute the following command in the root 
directory of your project:

    $ php vendor/bin/yentu import -i

This will start an interractive command line session, which will ask you for details
about your target database. Depending on the database you will be required to provide
hosts, usernames, passwords or database files (whichever are necessary).

After the import, a `yentu` directory will be created in the root directory of your
project. This directory will contain a `migrations` directory as well as a `config`
directory. The `config` directory will contain the `default.conf.php` configuration
file generated for your database connection. The `migrations` directory on the other
hand will contain the initial migration file `XXXXXXXXXXXX_import.php` (where 
`XXXXXXXXXXXX` represents the timestamp).

To test this migration, you can point the database configuration to a new database
connection and execute:

    $ php vendor/bin/yentu migrate

This will reproduce your current database schema on the new connection. 

Create a new set of Migrations
------------------------------
To create a new set of migrations you need to initialize the yentu directory and
history table for your project. The following command, when ran in the root directory
of your project, will initialize the project for yentu:

    $ php vendor/bin/yentu init -i
    
Just as it is with the `import` command, this will create the `yentu` directory which
will contain the `migrations` and `config` directories. The `config` directory will
contain the `default.conf.php` configuration file which contains the parameters you
provided. This time however, the migrations directory will remain empty. 

More on setting up
------------------
In cases where an interractive command line interface is not required (such as with automated build scripts), 
you can pass the required parameters to the init command and ignore the interractive (`-i`) switch.



 
