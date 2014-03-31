# Using Model

Windwalker has many Model classes and each one do different things.

## Model

The basic model, it can fetch Table and provides a Registry interface to setting state, just same as Joomla Model.

### ListModel

Using for list page, you can write a query in it then this model will helper you get list from database and handle
 search, filter, sorting and pagination etc. See [ListModel Section](model-list.md)

### ItemModel

A simple Model to get one item record.

### CrudModel & AdminModel

CrudModel provides some methods help you do CRUD to a table. The AdminModel is extends CrudModel, provides some advanced
 functions when you do CRUD in admin.
