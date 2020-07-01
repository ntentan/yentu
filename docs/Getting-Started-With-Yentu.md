Getting Started with Yentu
==========================
There are two major ways of getting your project started on yentu. If you have an existing project with an established data schema, you can import its current schema as an initial Yentu migration. On the other hand, if you are starting a new project, you can just go ahead and initialize your code directory for yentu, and continue working as usual. In both cases, a directory named `yentu`, which contains all the configurations and migrations will be created in the root of your project's directory. The following sections will walk you through the steps required for both strategies.

Starting from Scratch
---------------------
To start using yentu for your migrations you need to initialize the `yentu` directory, and the history database table for your project. Assuming you installed yentu into your project with composer, when the following command is run, it will initialize the yentu installation:

    $ php vendor/bin/yentu init -i
    
While this command is running, you will be expected to provide the details of your database connection. Prompts will be provided for the following:

- The type of database, with postgresql, mysql and sqlite as options.
- The hostname of the database
- The port on which the database server is running. This optional prompt can be skipped with an empty value, so the default port of your particular server can be used.
- The name of the database on the server.
- The username and password for connecting to the database server.

More on setting up
------------------
In cases where an interractive command line interface is not required (such as with automated build scripts), 
you can pass the required parameters to the init command and ignore the interractive (`-i`) switch.



 


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

