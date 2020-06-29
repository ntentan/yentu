Introduction
============

Like any other database migration tool, Yentu allows developers to track changes made to their database schema specifications. By writing scripts that define incremental database changes, the actual evolution of the database is captured along with the rest of the codebase. The word "Yentu" is an Akan word which means "let's migrate". 

Yentu is framework agnostic, and although it has primarily been built for use in the Ntentan framework, it can be used in just about any PHP project. The core of its operations are performed through a command line interface, so you do not have to integrate it in any way with your actual code. Your build systems will however love that.

Unlike most other migration systems, migration for are designed to be as descriptive as possible, 
without the extra overhead of classes and other boilerplate code. Developers
would generally specify the operations that Yentu should perform when the 
migration is run and Yentu would automatically determine what to do when the 
migration is reversed. Yentu also provides an interface which allows developers
to perform custom reverse operations in cases where necessary.

Yentu currently supports MySQL, PostgreSQL and SQLite. With portablility in mind,
a yentu migration written on a MySQL server could be run on a PostgreSQL server
without any problems.