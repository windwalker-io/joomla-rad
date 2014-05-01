# List & Modal formfield

Sometimes, we hope to get A items list as selector in B edit interface. In the past, we have to use sql to query
 this list and build as a HTML `<select>`. In windwalker, every MVC have their own list and modal formfield. We can
 add them in XML anytime.

## Basic List Modal

We liook `sakura` first, there is two files in:

- `model/field/sakura/list.php`
- `model/field/sakura/modal.php`

### List

Now we can use `sakura_list` to fetch sakura items as a select list.

``` xml
<field name="sakura_id"
    type="sakura_list"
    label="Select Sakura"
    description="COM_FLOWER_SELECT_SAKURA_DESC"
>
    <option>JOPTION_DO_NOT_USE</option>
</field>
```

In `config.xml`, we need include fields:

``` xml
<fieldset
    name="component"
    addfieldpath="administrator/components/com_flower/model/field"
>
```

Result

![img](https://cloud.githubusercontent.com/assets/1639206/2786610/1bf0bd38-cb7a-11e3-8d2b-a6207a1c2b77.png)

### Modal

Same as list, we use `sakura_modal` to select sakuras:

``` xml
<field name="sakura_id"
    type="sakura_modal"
    label="Select Sakura"
    description="COM_FLOWER_SELECT_SAKURA_DESC"
/>
```

![img](https://cloud.githubusercontent.com/assets/1639206/2786639/8bb76f5e-cb7a-11e3-8a83-1acecf7c9c2f.png)

![img](https://cloud.githubusercontent.com/assets/1639206/2786642/a0fbdd32-cb7a-11e3-80e0-5682cc7d26d6.png)

## Using some attributes to target other extensions or table

### List

We can using `itemlist` to choose table items from other extension, Foe example, if we want to get city list in
 `com_address`, so we can use this xml:

``` xml
<field name="city_id"
    type="itemlist"
    label="Select City"

    extension="com_address"
    view_list="cities"
    view_item="city"
>
    <option>JOPTION_DO_NOT_USE</option>
</field>
```

The `Itemlist` formfield will get `#__addresses_cities` table as list source.

### Modal

Same as itemlist.

``` xml
<field name="city_id"
    type="modal"
    label="Select City"

    extension="com_address"
    view_list="cities"
    view_item="city"
>
    <option>JOPTION_DO_NOT_USE</option>
</field>
```

Note: Our view need `modal.php` in view tmpl folder taht we can get this modal work.

## Extends ItemList and Modal formfield

We can see `Sakura_list` first:

``` php
// model/field/sakura/list.php

include_once JPATH_LIBRARIES . '/windwalker/src/init.php';
JForm::addFieldPath( AKPATH_FORM.'/fields');
JFormHelper::loadFieldClass('itemlist');

class JFormFieldSakura_List extends JFormFieldItemlist
{
    public $type = 'Sakura_List';

    public $value = null;

    public $name = null;

    protected $view_list = 'sakuras' ;

    protected $view_item = 'sakura' ;

    protected $extension = 'com_flower' ;
}
```

It's very simple, this class extends from `JFormFieldItemlist`, and add `$view_list`, `$view_item` , `$extension`
 three properties then formfield will auto fetch lst we want.

If you need more functions, please override `getOptions()` ans `setElement()`.

## Quick Add

Add these attributes in xml:

``` xml
<field name="sakura_list"
     type="sakura_list"
     label="Select Sakura"

     quickadd="true"
>
    <option>JOPTION_DO_NOT_USE</option>
 </field>
```

Now we'll see `quickadd` button.

![img](https://cloud.githubusercontent.com/assets/1639206/2787202/97c1c220-cb86-11e3-967c-6ffb1e0494a8.png)

Click and enter input.

![img](https://cloud.githubusercontent.com/assets/1639206/2787207/c680082e-cb86-11e3-8cea-2b37f2e6fd24.png)

Now quickadd will use ajax to add new item.

![img](https://cloud.githubusercontent.com/assets/1639206/2787205/b3968332-cb86-11e3-95b0-9f01b7344ef8.png)

### Customize QuickAdd fields

In `model/form/sakura.xml`, we'll see this fields:

``` xml
<!-- For Quick Ajax AddNew -->
<fieldset name="quickadd">
    <field name="title"
        type="text"
        label="JGLOBAL_TITLE"
        description="JFIELD_TITLE_DESC"
        required="true"
        size="50"
        class="input-xlarge"
        />

    <field name="catid"
        type="category"
        label="JCATEGORY"
        description="JFIELD_CATEGORY_DESC"
        extension="com_flower"
        >
        <option value="0">COM_FLOWER_TITLE_UNCATEGORISED</option>
    </field>

    <field name="state"
        type="list"
        label="State"
        >
        <option value="0">Unpublished</option>
        <option value="1">Published</option>
    </field>
</fieldset>
```

Just add field here, fieldname should same as sql column. The new field will appear in modal box.

![img](https://cloud.githubusercontent.com/assets/1639206/2787468/977b40b6-cb8b-11e3-912e-3613e9a422bb.png)

### QuickAdd attributes

| Attribute      | Description |
| -------------- | ----------- |
|  `task`        | Using which task (controller) to handle quickadd ajax.|
|  `quickadd`    | Need TRUE to enable this function.|
|  `table`       | Database table name, default is `#__{component}_{view_list}`|
|  `key_field`   | Select option value.|
|  `value_field` | Select option text.|
|  `formpath`    | Quickadd form xml path. Default is `model/form/{view_item}.xml`|
|  `quickadd_handler` | Which component get our ajax to handle quickadd, need windwalker component.|
|  `title`       | Quickadd modal box and button title.|
