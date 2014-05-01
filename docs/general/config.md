# Config

## Config.json file

There is a `config.json` in `/etc` folder, we can set some config in this:

``` json
{
	"system" : {
		"debug" : true,
		"development" : true
	}
}
```

### How to get Config data

Now we set a new config in it:

``` json
{
	"system" : {
		"debug" : true,
		"development" : true
	},

	"foo" {
	    "bar" : {
	        "yoo" : "Sunflower"
	    }
	}
}
```

Then we just use this code to get our config data:

``` php
$yoo = \Flower\Config\Config::get('foo.bar.yoo', 'default value');
```

### Using other format

If we want to use other format as config, for example, the `YAML` format, just change property in `\Flower\Config\Config::$filetype`

``` php
abstract class Config extends AbstractConfig
{
    // Change this to yaml
	protected static $type = 'yaml';

    // ...
}
```

Now `etc/config.yml` will be our main config file.
