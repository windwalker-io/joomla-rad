# Getting Started

## Installation via Composer

We use composer to install Windwalker RAD.

``` bash
cd /your/joomla/dir
composer create-project windwalker/joomla-rad libraries/windwalker dev-staging -s dev
```

## Setting PHP CLI

Windwalker generator need PHP CLI, that you can use command line to operate it. If you are in Windows, please make sure
 the Environment Variable of `php.exe` has set.

## Generator Commands

Now, chdir to your Joomla path, type:

``` bash
$ php bin/windwalker generator
```

And you will see:

```
Windwalker Console - version: 2.0
------------------------------------------------------------

[generator Help]

Extension generator.

Usage:
  generator <command> [option]


Options:

  -c | --client     Site or administrator (admin)
  -t | --tmpl       Using template.
  -h | --help       Display this help message.
  -q | --quiet      Do not output any message.
  -v | --verbose    Increase the verbosity of messages.
  --no-ansi         Suppress ANSI colors on unsupported terminals.

Available commands:

  add        Add new controller view model system classes(only component).

    item       Add a singular MVC group for item CRUD.

    list       Add a plural controller to show list page.

    subsystem  Sub system contains item and list two controller to support CRUD a table.


  convert    Convert an extension back to a template.

  init       Init a new extension.
```

### Generate Extensions

Here is some example of how to generate extensions:

#### Init Component

Create a component named `com_flower` and with two MVCs `sakura` and `sakuras` in both site and admin.

``` bash
$ php bin/windwalker generator init com_flower sakura.sakuras
```

Create a component in site or admin.

``` bash
$ php bin/windwalker generator init com_flower sakura.sakuras -c admin (site)
```

Create a component and use other sub template `foo`, default is `default`.

``` bash
$ php bin/windwalker generator init com_flower sakura.sakuras -t foo
```

#### Add two MVC groups

Add a singular and a plural MVC group to a exists component.

``` bash
$ php bin/windwalker generator add subsystem com_flower rose.roses
```

#### Module

Create a module named `mod_flower` in front end.

``` bash
$ php bin/windwalker generator init mod_flower -c site
```

#### Plugin

Create a module named `plg_flower` in 'system' group.

``` bash
$ php bin/windwalker generator init plg_system_flower
```

