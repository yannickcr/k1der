INVISION POWER BOARD 1.3 -> 2.0.0 MANUAL UPGRADE
------------------------------------------------

This file is intended to allow you to upgrade your Invision Power Board manually via shell which
is ideal for those with large boards which may require several slow queries.

-----------------------------------------------------------------------------------------------------
IMPORTANT: It is assumed that you are comfortable with SQL and shell and understand the basic concepts
of paths, files and source SQL files.
-----------------------------------------------------------------------------------------------------

It is assumed that you have read the upgrade text and you have backed up your database and files before
proceeding. If you haven't backed up - read the file "Documents/upgradefromipb_1x.html" from the download
zip.

-----------------------------------------------------------------------------------------------------
IMPORTANT: PERFORM A BACKUP OF YOUR DATABASE BEFORE USING THESE INSTRUCTIONS.
-----------------------------------------------------------------------------------------------------

To perform the update, we'll need to use a mixture of command line, PHP scripting and SQL source files.

Firstly, upload sql_1.sql and sql_2.sql into the root IPB folder (the one with index.php in it)

-----------------------------------------------------------------------------------------------------
IMPORTANT: sql_1.sql and sql_2.sql have queries with the default 'ibf_' table prefix. If your table
prefix is different, do a search and replace on those files to change 'ibf_' to your database prefix
-----------------------------------------------------------------------------------------------------

The first step is to log onto mysql from shell.

------------------------------------------------
When you have a new terminal window open, type:

mysql -u{username} -p{password}

(Substituting {username} for your SQL username and {password} for you SQL password)
------------------------------------------------
Then type:

use {database}

(Substituting {database} for your SQL database)
------------------------------------------------
You should see a message like so:

> Reading table information for completion of table and column names
> You can turn off this feature to get a quicker startup with -A
>
> Database changed
------------------------------------------------
Now we'll run the first source file. Type:

source /home/website/public_html/forums/sql_1.sql

(Substituting /home/website/public_html/forums/ with your real path to this file)
------------------------------------------------
Now we'll need to run the installer - but we only want a few steps to run, so copy the URL below carefully
substituting {yourdomain.com/forum} with your real URL to your board:

Go to: {yourdomain.com/forum}/upgrade/upgrade.php?act=work&version=103&step=7&dieafterstep=13

This may take a while for some large boards.
------------------------------------------------
Now we'll run the second source file. Type:

source /home/website/public_html/forums/sql_2.sql

(Substituting /home/website/public_html/forums/ with your real path to this file)
------------------------------------------------
Now we'll need to run the installer again - but we only want a few steps to run, so copy the URL below carefully
substituting {yourdomain.com/forum} with your real URL to your board:

Go to: {yourdomain.com/forum}/upgrade/upgrade.php?act=work&version=103&step=22

This may take a while for some large boards.
This will insert your templates and settings and completes the upgrade
------------------------------------------------

That's it, you should now be running IPB 2!


