# Using Controller

## Single Action

Windwalker controller is follow [Single Responsibility Principle](http://en.wikipedia.org/wiki/Single_responsibility_principle)
, every controller only have one action. For example:

``` php
<?php
// controller/sakura/display.php

use Windwalker\Controller\DisplayController;

class FlowerControllerSakuraDisplay extends DisplayController
{
    protected function doExecute()
    {
        $view = $this->getView();

        return $view->render();
    }
}
```

The `FlowerControllerSakuraDisplay` controller only do one thing, just render this page.
Note you have to return rendered string for component to echo it.

## Task Routing and Default Controller

Add this task in url to fetch controller what you want:

```
index.php?option=com_flower&task=sakuras.state.publish
```

This task will get `FlowerControllerSakurasStatePublish` controller in `controller/sakuras/state/publish.php`,
if this controller not exists, Windwalker will use default `Windwalker\Controller\State\PublishController` instead.

## Executed Hooks

Every Controller have two hooks to let you inject your logic, `prepareExecute()` and `postExecute()`.

See this example:

``` php
<?php
// controller/sakura/edit/save.php

use Windwalker\Controller\Edit\SaveController;

class FlowerControllerSakuraEditSave extends SaveController
{
    protected function prepareExecute()
    {
        // Set something.
        $this->data = $this->input->post;
    }

    // We don't need doExecute() because parent will do it.

    protected function postExecute($data = null)
    {
        // Do some stuff like redirect or session.
        $this->redirect('...');

        return $data;
    }
}
```

## Advanced Functions

### Redirect

#### Basic Redirect

``` php
$this->redirect($url, 'Mssage');
```

#### Redirect to Item or List

You have to extend `AbstractRedirectController` to use these features.

``` php
$this->redirectToItem($id, 'id', 'Mssage');
```

``` php
$this->redirectToList('Mssage');
```

You can use `getRedirectItemUrl()`, `getRedirectListUrl`, `getRedirectItemAppend()` and `getRedirectListAppend()`
to setting some redirect details.

### Setting Config

If you want to set some config to multiple controller, you have to use `Delegator`.

Every controllers group will have a delegator, for example, `sakuras` controllers will have a `delegator.php` in `controller/sakuras`.
You can set some config to or alias it:

``` php
<?php
// controller/sakuras/delegator.php

use Windwalker\Controller\Resolver\ControllerDelegator;

class FlowerControllerSakurasDelegator extends ControllerDelegator
{
	protected function registerAliases()
	{
	    // Set alias here using $this->addAlias($class, $alias);
	}

	protected function createController($class)
	{
	    $this->config['allow_redirect_params'] = array('...');

		return parent::createController($class);
	}
}

```

The alias will get other controller if we set it, and the config will push to every controllers of `sakuras`.

### HMVC

If you want to use other Controller to do something, please using `fetch()` in controller.

This will call save controller and return Boolean, SaveController will not redirect page because it is in HMVC mode.

``` php
$this->fetch('con_flower', 'sakura.edit.save', array('data' => $data));
```

And this can render other view for use:

``` php
$block = $this->fetch('con_flower', 'rose.display', array('layout' => 'foo'));

echo $block;
```

Do not show messages, add `quiet` params:

``` php
$this->fetch('con_flower', 'sakura.edit.save', array('quiet' => true, 'data' => $data));
```



