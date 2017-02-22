Symfony 2 API Sandbox
=========

[![Build Status](https://travis-ci.org/luchianenco/symfony-api-sandbox.svg?branch=master)](https://travis-ci.org/luchianenco/symfony-api-sandbox)

Configure DB settings in ´app/config/parameters.yml´ 

run below command to create db schema

    php app/console doctrine:schema:create

then run next command to create fixtures

    php app/console hautelook:fixtures:load
    
    