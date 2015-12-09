Getting Started with Yentu
==========================
There are tow major ways of integrating yentu into your project.
Depending on how advanced in time, your project is, you may either have to write 
a new set of migrations or import an existing schema from a database as your 
initial migration. 

Import a Schema
---------------
Assuming your project already has a schema, you can integrate yentu by importing
that schema as an initial migration. To do this, change directory to the root of
your project and execute:

    $ php vendor/bin/yentu import -i

 
