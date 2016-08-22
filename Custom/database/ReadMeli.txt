to backup:
mysqldump -u root -p wiki_literatureeditor > wiki_literatureeditor.sql

to restore:
mysql -u root -p wiki_literatureeditor < wiki_literatureeditor.sql
mysql -u root -p wiki_literatureeditor_archive < 2016-08-20_wiki_lit.sql

------------------

TO LOGIN: sudo mysql -u root -p
CREATE DATABASE wiki_literatureeditor;
CREATE DATABASE wiki_literatureeditor_archive;

SET sql_mode = ''; //set it to blank, to prevent errors like 

DROP DATABASE wiki_literatureeditor_archive; //to delete database

mysql -u root -p wiki_literatureeditor < wiki_literatureeditor.sql

SELECT @@sql_mode; //show sql_mode value
show variables; //list all variables like sql_mode;

---------------------------

steps: loading remote dbase to local:
1. using TextEdit, open [2016-08-20_wiki_lit.sql] then replace 'editors.eol.org' with 'editors.eol.localhost'
2. mysql -u root -p wiki_literatureeditor_archive < 2016-08-20_wiki_lit.sql
3. sudo mysqlcheck -u root -p --optimize --databases wiki_literatureeditor_archive
