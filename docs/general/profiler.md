# Profiler

Joomla! core has a `JProfiler` help us mark some information in system process:

``` php
// Application is main namespace of Joomla core
$profiler = JProfiler::getInstance('Application');

$profiler->mark('afterRender');
```

But sometimes we need more flexible on adding other profiler namespace and print it. Windwalker provides a helper to do this.

## ProfilerHelper

### Using main namespace

Using this code, same as first code above:

``` php
ProfilerHelper::mark('my data', 'Application');
```

This data will appear in Joomla Debug Console.

### Using my namespace

``` php
ProfilerHelper::mark('my data', 'MyNS');
```

But we need to print it:

``` php
ProfilerHelper::render('MyNS');
```

### Using Windwalker Debug Console

``` php
ProfilerHelper::mark('FOO', 'Windwalker');
```

Then this data will appear in component bottom:

![img](https://cloud.githubusercontent.com/assets/1639206/2787872/e9ab12de-cb91-11e3-9350-49ae7bc7dc3c.png)
