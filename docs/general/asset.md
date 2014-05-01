# Asset Helper

`AssetHelper` is a wrap of `JDocument` helpe us add JS and CSS to html.

## Get AssetHelper

``` php
$asset = Container::getInstance('com_flower')->get('helper.asset');
```

Every sub container has their own asset helper, we have to choose sub container first.
 `AssetHelper` will auto scan component folders to find css & js files.

## Add CSS

``` php
$asset->addCss('main.css');
```

Asset Helper will scan these folders:

- (1) `templates/[template]/css/com_flower/`
- (2) `templates/[template]/css/`
- (3) `components/com_flower/asset/css/`
- (4) `components/com_flower/asset/`
- (5) `media/com_flower/css/`
- (6) `media/com_flower/`
- (7) `media/windwalker/css/`
- (8) `media/windwalker/`
- (9) `libraries/windwalker/Resource/asset/css/` (For legacy)
- (10) `libraries/windwalker/assets/` (For legacy)
- (11) `libraries/windwalker/assets/` (For legacy)

And add first found `main.css`.

Also, we can add sub folders:

``` php
$asset->addCss('foo/bar/rose.css');
```

### Add Internal CSS

``` php
$asset->internalCss('body {color: #666;}');
```

This css code will add in `<style>` of `<head>`.

## Add JS

Same as CSS

``` php
$asset->addJs('main.js');
```

Asset Helper will scan these folders:

- (1) `templates/[template]/js/com_flower/`
- (2) `templates/[template]/js/`
- (3) `components/com_flower/asset/js/`
- (4) `components/com_flower/asset/`
- (5) `media/com_flower/js/`
- (6) `media/com_flower/`
- (7) `media/windwalker/js/`
- (8) `media/windwalker/`
- (9) `libraries/windwalker/Resource/asset/js/` (For legacy)
- (10) `libraries/windwalker/assets/` (For legacy)
- (11) `libraries/windwalker/assets/` (For legacy)

### Add Internal JS

``` php
$asset->internalJS('console.log('foo')');
```

This js code will add in `<head>`.

## MD5SUM

We will need add md5 suffix after asset to clean cache:

``` html
<link href="css/bootstrap.css?1ed040df04b9d6caf47ba55057bed72d" rel='stylesheet' type='text/css'>
```

So, for every css or js file, we can add a md5 sum file in it's folder:

```
css/main.css
css/main.css.sum
```

We can use some tools to generate md5sum in `main.css.sum` file, just one line:

```
1ed040df04b9d6caf47ba55057bed72d
```

Now AssetHelper will auto fetch this md5 sum as url suffix.

Note: the suffix will be random in Joomla Debug mode.
