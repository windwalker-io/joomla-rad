# How to Test Windwalker

## Test Environment

Joomla test bootstrap
https://github.com/joomla/joomla-cms/blob/staging/tests/unit/bootstrap.php

## Clone a Test Windwalker package

``` bash
$ composer create-project windwalker/joomla-rad libraries/windwalker dev-staging
...
Do you want to remove the existing VCS (.git, .svn..) history? [Y,n]? n

$ cd libraries/windwalker
$ git remote add {your_remote} git@github.com:smstw/windwalker-joomla-rad.git
$ git fetch {your_remote} staging:staging
$ git checkout staging
$ composer install
```

## Test Class

Use this command to geneate Test Classes

``` bash
php bin/windwalker generator test <Folder> <Class> 
```

For example

``` bash
php bin/windwalker generator test Helper ModalHelper
```

It will generate a test class in `test/Helper/ModalHelper`
