to backup:
mysqldump -u root -p wiki_literatureeditor > wiki_literatureeditor.sql

to restore:
mysql -u root -p wiki_literatureeditor < wiki_literatureeditor.sql
