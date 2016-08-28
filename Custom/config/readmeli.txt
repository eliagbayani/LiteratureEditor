http://stackoverflow.com/questions/5246114/php-mkdir-permission-denied-problem

in my apache2/httpd.conf, I entered this:

# orig value
# User _www
# Group _www

# entered by eli
User eliagbayani
Group _www

--------------------------------------

* navigation
** mainpage|mainpage-description
** http://editors.eol.localhost/LiteratureEditor/Custom/bhl_access/index.php|Search BHL
* Articles
** http://editors.eol.localhost/LiteratureEditor/Custom/bhl_access/index.php?search_type=articlelist&radio=draft|For Review (drafts)
** http://editors.eol.localhost/LiteratureEditor/Custom/bhl_access/index.php?search_type=articlelist&radio=approved|For EOL Harvesting
* Projects
** http://editors.eol.localhost/LiteratureEditor/Custom/bhl_access/index.php?search_type=projectsmenu&radio=proj_my|My projects
** http://editors.eol.localhost/LiteratureEditor/Custom/bhl_access/index.php?search_type=projectsmenu&radio=proj_active|Active projects
** http://editors.eol.localhost/LiteratureEditor/Custom/bhl_access/index.php?search_type=projectsmenu&radio=proj_comp|Completed projects
* Wiki
** Special:AllPages|For Review (drafts)
** http://editors.eol.localhost/LiteratureEditor/index.php?title=Special%3AAllPages&from=&to=&namespace=5000|For EOL Harvesting
** http://editors.eol.localhost/LiteratureEditor/index.php?title=Special%3AAllPages&from=&to=&namespace=5002|Active Projects
** http://editors.eol.localhost/LiteratureEditor/index.php?title=Special%3AAllPages&from=&to=&namespace=5004|Completed Projects
*
** recentchanges-url|recentchanges
** randompage-url|randompage
** helppage|help
* SEARCH
* TOOLBOX
* LANGUAGES

--------------------------------------

BHL Google Doc:
https://docs.google.com/drawings/d/1XN-U069GQ_TsYSBptcvYpgAmc-xoTq0NBUnp7V-vMO0/edit

--------------------------------------
