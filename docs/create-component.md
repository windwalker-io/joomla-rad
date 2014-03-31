# Create Component

Now we'll use a component named `com_flower` with `sakura` and `sakuras` controllers to tutorial.

## Using Generator

Please type:

``` bash
$ php bin/windwalker init com_flower sakura.sakuras
```

Here is the success message.

![success](http://cl.ly/Uj9X/generate-success.jpg)

## Discover Component

Go to Joomla admin and discover this component.

![img](http://cl.ly/Ujhc/140331-0003.jpg)

Install it.

![img](http://cl.ly/Uk73/140331-0004.jpg)

Then, click image into component view.

![img](http://cl.ly/UjXp/140331-0006.jpg)

The database has auto created:

![img](http://cl.ly/UkIX/130512-0015.jpg)

## Add Subsystem

This command can add two controllers, item edit and list:

``` bash
$ php bin/windwalker add subsystem com_flower rose.roses
```

We call these two MVC groups an subsystem.

## Folder Structure of Component

### Admin

![img](http://cl.ly/UjT3/140331-0009.jpg)

| Folder | Desc   |
|--------|--------|
| asset  | CSS & JS etc. |
| etc    | Config files  |
| controller             | Controllers |
| **helper** / helpers   | Helper classes. Plural folder is for legacy use. |
| images        | Images |
| language      | Languages, site language is in admin together. |
| model         | Models |
| sql           | The SQL needed when component install |
| src           | Some useful classes, using PSR-0 autoloading here. |
| table         | Tables, Active Record. |
| view          | Views |

### Site

| Folder | Desc   |
|--------|--------|
| asset        | CSS & JS files |
| controller   | Controllers |
| helper       | Helpers. If you want to use some classes both in site and admin, put it in `src` |
| images       | Images      |
| model        | Models      |
| sql          | The SQL needed when component install |
| view         | Views       |

## Include Component Library in Other Extensions

There is an important file in admin `src/init.php`.

If you include this file, it will load component required classes and windwalker.
