# Using ListModel

In Joomla legacy model, we writing query in `getListQuery()` and `getItems()` will help us fetch list from database.

In windwalker, we don't need to write whole `getListQuery()`, we can write what we actually want to do, and others will auto
done by model.

## Configure Tables

### Select & Join

Writing table config in `configureTables()`:

``` php
protected function configureTables()
{
    $queryHelper = $this->getContainer()->get('model.sakuras.helper.query', Container::FORCE_NEW);

    $queryHelper->addTable('sakura', '#__flower_sakuras')
        ->addTable('category',  '#__categories', 'sakura.catid      = category.id')
        ->addTable('user',      '#__users',      'sakura.created_by = user.id')
        ->addTable('viewlevel', '#__viewlevels', 'sakura.access     = viewlevel.id')
        ->addTable('lang',      '#__languages',  'sakura.language   = lang.lang_code');

    // Merge custom filter fields with auto generated filter fields.
    $this->filterFields = array_merge($this->filterFields, $queryHelper->getFilterFields());
}
```

We set all table information into `QueryHelper` and this object will help us generate queries.
`QueryHelper` also generate filter fields by table you set into it.

### The Selected Fields

`QueryHelper` will auto generate select fields:

```
`sakura`.`id` AS `id`,
`sakura`.`id` AS `sakura_id`,
`sakura`.`asset_id` AS `asset_id`,
`sakura`.`asset_id` AS `sakura_asset_id`,
`sakura`.`catid` AS `catid`,
`sakura`.`catid` AS `sakura_catid`,
`sakura`.`title` AS `title`,
`sakura`.`title` AS `sakura_title`,
`sakura`.`alias` AS `alias`,
`sakura`.`alias` AS `sakura_alias`,
`sakura`.`url` AS `url`,
`sakura`.`url` AS `sakura_url`,
`sakura`.`introtext` AS `introtext`,
`sakura`.`introtext` AS `sakura_introtext`,
`sakura`.`fulltext` AS `fulltext`,
`sakura`.`fulltext` AS `sakura_fulltext`,
`sakura`.`images` AS `images`,
`sakura`.`images` AS `sakura_images`,
`sakura`.`version` AS `version`,
`sakura`.`version` AS `sakura_version`,
`sakura`.`created` AS `created`,
`sakura`.`created` AS `sakura_created`,
`sakura`.`created_by` AS `created_by`,
`sakura`.`created_by` AS `sakura_created_by`,
`sakura`.`modified` AS `modified`,
`sakura`.`modified` AS `sakura_modified`,
`sakura`.`modified_by` AS `modified_by`,
`sakura`.`modified_by` AS `sakura_modified_by`,
`sakura`.`ordering` AS `ordering`,
`sakura`.`ordering` AS `sakura_ordering`,
`sakura`.`state` AS `state`,
`sakura`.`state` AS `sakura_state`,
`sakura`.`publish_up` AS `publish_up`,
`sakura`.`publish_up` AS `sakura_publish_up`,
`sakura`.`publish_down` AS `publish_down`,
`sakura`.`publish_down` AS `sakura_publish_down`,
`sakura`.`checked_out` AS `checked_out`,
`sakura`.`checked_out` AS `sakura_checked_out`,
`sakura`.`checked_out_time` AS `checked_out_time`,
`sakura`.`checked_out_time` AS `sakura_checked_out_time`,
`sakura`.`access` AS `access`,
`sakura`.`access` AS `sakura_access`,
`sakura`.`language` AS `language`,
`sakura`.`language` AS `sakura_language`,
`sakura`.`params` AS `params`,
`sakura`.`params` AS `sakura_params`,
`category`.`id` AS `category_id`,
`category`.`asset_id` AS `category_asset_id`,
`category`.`parent_id` AS `category_parent_id`,
`category`.`lft` AS `category_lft`,
`category`.`rgt` AS `category_rgt`,
`category`.`level` AS `category_level`,
`category`.`path` AS `category_path`,
`category`.`extension` AS `category_extension`,
`category`.`title` AS `category_title`,
`category`.`alias` AS `category_alias`,
`category`.`note` AS `category_note`,
`category`.`description` AS `category_description`,
`category`.`published` AS `category_published`,
`category`.`checked_out` AS `category_checked_out`,
`category`.`checked_out_time` AS `category_checked_out_time`,
`category`.`access` AS `category_access`,
`category`.`params` AS `category_params`,
`category`.`metadesc` AS `category_metadesc`,
`category`.`metakey` AS `category_metakey`,
`category`.`metadata` AS `category_metadata`,
`category`.`created_user_id` AS `category_created_user_id`,
`category`.`created_time` AS `category_created_time`,
`category`.`modified_user_id` AS `category_modified_user_id`,
`category`.`modified_time` AS `category_modified_time`,
`category`.`hits` AS `category_hits`,
`category`.`language` AS `category_language`,
`category`.`version` AS `category_version`,
`user`.`id` AS `user_id`,
`user`.`name` AS `user_name`,
`user`.`username` AS `user_username`,
`user`.`email` AS `user_email`,
`user`.`password` AS `user_password`,
`user`.`block` AS `user_block`,
`user`.`sendEmail` AS `user_sendEmail`,
`user`.`registerDate` AS `user_registerDate`,
`user`.`lastvisitDate` AS `user_lastvisitDate`,
`user`.`activation` AS `user_activation`,
`user`.`params` AS `user_params`,
`user`.`lastResetTime` AS `user_lastResetTime`,
`user`.`resetCount` AS `user_resetCount`,
`user`.`otpKey` AS `user_otpKey`,
`user`.`otep` AS `user_otep`,
`viewlevel`.`id` AS `viewlevel_id`,
`viewlevel`.`title` AS `viewlevel_title`,
`viewlevel`.`ordering` AS `viewlevel_ordering`,
`viewlevel`.`rules` AS `viewlevel_rules`,
`lang`.`lang_id` AS `lang_lang_id`,
`lang`.`lang_code` AS `lang_lang_code`,
`lang`.`title` AS `lang_title`,
`lang`.`title_native` AS `lang_title_native`,
`lang`.`sef` AS `lang_sef`,
`lang`.`image` AS `lang_image`,
`lang`.`description` AS `lang_description`,
`lang`.`metakey` AS `lang_metakey`,
`lang`.`metadesc` AS `lang_metadesc`,
`lang`.`sitename` AS `lang_sitename`,
`lang`.`published` AS `lang_published`,
`lang`.`access` AS `lang_access`,
`lang`.`ordering` AS `lang_ordering`
```

It looks terrible but useful, if you join multiple tables, the field name will not conflict and more semantic,
we can use `$item->category_title` or `$item->user_id`, very clear.

If you want to override select fields, just set it in state:

``` php
$this->state->set('query.select', array('sakura.*', 'category.id AS cid'));
```

## Using Filters

### Filter Form XML

Open `model/form/sakuras/filter.xml`, we can write some filter fields here:

``` xml
<?xml version="1.0" encoding="utf-8"?>
<form>
	<fields name="search">
		<!-- Ignore -->
	</fields>

	<!-- Filter -->
	<fields name="filter">
		<!-- Ignore -->

		<field
            name="sakura.access"
            type="list"
            default=""
            label="Access"
            onchange="this.form.submit();"
        >
            <option></option>
            <option>JALL</option>
            <option value="1">Public</option>
            <option value="2">Register</option>
        </field>
	</fields>

	<fields name="list">
		<!-- Ignore -->
	</fields>
</form>
```

The `name` is same as what we want to filter in SQL column (eg: `sakura.state`), note you will need `onchange="this.form.submit();"` to submit form.

Back to admin, you'll see filter select appeared:

![img](http://cl.ly/Uk3p/140331-0012.jpg)

### Custom Filter Query

Sometimes we will need to filter a period, so we can't always use `=` to filter fields. So we can extend the filter rules.

First, add this field in filter XML:

``` xml
<field
    name="a.date"
    type="list"
    default=""
    onchange="this.form.submit();"
    class=""
>
    <option></option>
    <option>- Choose Time Period -</option>
    <option value="year">In 1 Year</option>
    <option value="month">In 1 Month</option>
    <option value="week">In 1 Week</option>
</field>
```

Write our logic in `configureFilters()`:

``` php
protected function configureFilters($filterHelper)
{
    $filterHelper->setHandler(
        'sakura.date',
        function($query, $field, $value)
        {
            $now = new DateTime;

            $now->modify('-1 ' . $value);

            $query->where($field . ' > ' . $now->format('Y-m-d'));
        }
    );
}
```

OK, we can filter time period in our ist.

## Search

### Fulltext Search

Search function is very similar to filter, open `model/form/sakuras/filter.xml`, you will see:

``` xml
<fields name="search">
    <field name="field"
        type="hidden"
        default="*"
        label="JSEARCH_FILTER_LABEL"
        labelclass="pull-left"
        class="input-small"
        >
        <option value="*">JALL</option>
        <option value="sakura.title">JGLOBAL_TITLE</option>
        <option value="category.title">JCATEGORY</option>
    </field>

    <field
        name="index"
        type="text"
        label="JSEARCH_FILTER_LABEL"
        hint="JSEARCH_FILTER"
        />
</fields>
```

The `name="field"` field is for setting our search fields, every option means one field, let us add a new field:

``` xml
<field name="field"
    type="hidden"
    default="*"
    label="JSEARCH_FILTER_LABEL"
    labelclass="pull-left"
    class="input-small"
    >
    <option value="*">JALL</option>
    <option value="sakura.title">JGLOBAL_TITLE</option>
    <option value="category.title">JCATEGORY</option>

    <option value="lang.title">Language</option>
</field>
```

OK, we search english, the language title can be searched:

![img](http://cl.ly/UjEb/140331-0014.jpg)

## Single Field Search

Change `field` field type to `list`, then you will able to choose field to search single column.

``` xml
<field name="field"
    type="list"
    default="*"
    label="JSEARCH_FILTER_LABEL"
    labelclass="pull-left"
    class="input-small"
    >
    <option value="*">JALL</option>
    <option value="sakura.title">JGLOBAL_TITLE</option>
    <option value="category.title">JCATEGORY</option>

    <option value="lang.title">Language</option>
</field>
```

![joomla323b_-_administration_-_sakura_list](https://cloud.githubusercontent.com/assets/1639206/2565963/c62520b2-b8bc-11e3-96ae-0087bb376abc.png)

## Multiple Search

Add a fieldset `multisearch`.

``` xml
<fields name="search">
    <field name="field"
        type="list"
        default="*"
        label="JSEARCH_FILTER_LABEL"
        labelclass="pull-left"
        class="input-small"
        >
        <option value="*">JALL</option>
        <option value="sakura.title">JGLOBAL_TITLE</option>
        <option value="category.title">JCATEGORY</option>

        <option value="lang.title">Language</option>
    </field>

    <field
        name="index"
        type="text"
        label="JSEARCH_FILTER_LABEL"
        hint="JSEARCH_FILTER"
        />

    <!-- For multiple search -->
    <fieldset name="multisearch">
        <field
            name="sakura.title"
            type="text"
            label="Title"
            hint="JSEARCH_FILTER"
            />

        <field
            name="category.title"
            type="text"
            label="Category"
            hint="JSEARCH_FILTER"
            />
    </fieldset>

</fields>
```

![140331-0016](https://cloud.githubusercontent.com/assets/1639206/2565802/ae00fb98-b8ba-11e3-9981-a69c203bcc29.jpg)

## Custom Search Query

We also use `SearchHelper` to set handler, same as `FilterHelper`:

``` php
/**
 * configureSearches
 *
 * @param \Windwalker\Model\Filter\SearchHelper $searchHelper
 *
 * @return  void
 */
protected function configureSearches($searchHelper)
{
    $searchHelper->setHandler(
        'sakura.title',
        function ($query, $filed, $value)
        {
            // Custom search query...
        }
    );
}
```
