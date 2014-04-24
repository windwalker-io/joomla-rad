# CrudModel & AdminModel

## CrudModel

CrudModel is a model to do CRUD operation of table item. This class provides `getItem()`, `save()` and `delete()`
 methods to operate a record. Some of our controllers need this model to use.

## AdminModel

`AdminModel` extends `CrudModel`, provides some admin functions like `reorder()`, `checkout & in`, `sort` etc.
 Most of all we use `AdminModel` as basic model in our component.

## Some useful functions in AdminModel

### Reorder Conditions

Reorder conditions can help you setting ordering group.

``` php
// In AdminModel
protected function getReorderConditions($table)
{
    $condition[] = 'catid = ' . $table->catid;
}
```

Or

``` php
protected function populateState()
{
    $this->state->set('reorder.condition.fields', array('catid = 5'));
}
```

### Order Position of new item

If we want to st new item ordered as first or last title, just override `setOrderPosition` and set the default value:

``` php
// Set $position = 'first' OR 'last'
public function setOrderPosition($table, $position = 'first')
{
    return parent::setOrderPosition($table, $position);
}
```
