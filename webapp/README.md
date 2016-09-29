/*
 * Created by Heet on 04/18/2016
 * README.md
 */


INTRODUCTION
------------

This file illustrates the use-case of Document Search Engine Project.



LIBRARIES 
-----------

* Slim v2.3.5 - REST API library
* AngularJS
* PHP v5.6
* MySQL v5.6
* Postman - testing REST APIs



FOLDER INFO
------------

 * AngularJS files with controller (app)
 * Views (views)
 * Document destination (docs)
 * Thumbnail destination (images)
 * APIs (.php files)
 	* document related APIs - doc_management.php
 	* user related APIs - user_management.php



MAJOR API
-----------
/addDocument (add entry in table)
/uploadDocument (upload doc in destination folder)
/uploadThumbnail (upload thumbnail in destination folder)
/updateDocument (update doc entry in table)
/deleteDocument (delete doc entry in table)
/search



HOW TO SEARCH
--------------

 * User may choose more than one search Query from keywords, title, caption.
 * If none is selected then searched within keywords.
 * Search syntax: (word1) (word2)
 	* word1: searchkey
 	* word2: document type




ADMINISTRATOR INFO
-------------------

URI- "/admin"
username- "admin"
password- "123"

