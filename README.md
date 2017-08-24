symfonyFlickr
=============
PHP + Flickr API sandbox.

Framework: Symfony 3
Stack: PHP7-FPM, NGINX, Docker

In order to run the code you'll need to have docker installed.
For Mac check: https://docs.docker.com/docker-for-mac/install/
For Windows check: https://docs.docker.com/docker-for-windows/install/


Installation
============
Clone the repo.
Using a terminal cd to the folder where the code has been cloned.
run `docker-compose -f docker-compose-dev.yml up -d`

This will create the containers.

The vhost is set as `local.dev.com.au`, make sure you add an entry for it
on the */etc/hosts* file on your computer

Implementation of the Flickr API to:
* Get 10 of the most recent photos  
* Search photos by keyword
* Access the details of the photos received.

@TODO: Pagination of results, tidy up and general styling

A Symfony project created on August 23, 2017, 3:01 am.
# symflickr
