# Batch

Batch is an important function help us process multiple items. However, Joomla has only support `Access`, `Language`, `Category`
 and `User` these few fields. This is `com_content` batch box below.

![batch](https://cloud.githubusercontent.com/assets/1639206/2601602/7ebc23c6-bb12-11e3-8513-86f088c01907.png)

In Joomla, the `JModelAdmin` will approach these batch, but it's hard to add new fields to batch.
 So in Windwalker, we use `Controller\Batch\MoveController` and `Controller\Batch\CopyController` to approach it.
 And we can writting xml file to set batch fields.

## Add New Batch Field

By the default, Windwalker provide us some basic batch fields:

![flower batch](https://cloud.githubusercontent.com/assets/1639206/2601643/55af147e-bb13-11e3-8a58-b4902ddf1074.png)

Then we add a `state` field to let user batch change published state.

Open `model/form/sakuras/batch.xml`, you will see this code:

``` xml
<?xml version="1.0" encoding="utf-8"?>
<form>
	<!-- Batch -->
	<fields name="batch">
		<field name="access"
			type="accesslevel"
			label="JLIB_HTML_BATCH_ACCESS_LABEL"
			description="JLIB_HTML_BATCH_ACCESS_LABEL_DESC"
			labelclass="control-label"
			class="input-xlarge inputbox"
			>
			<option>JLIB_HTML_BATCH_NOCHANGE</option>
		</field>

		<field name="language"
			type="contentlanguage"
			label="JLIB_HTML_BATCH_LANGUAGE_LABEL"
			description="JLIB_HTML_BATCH_LANGUAGE_LABEL_DESC"
			labelclass="control-label"
			class="input-xlarge inputbox"
			>
			<option>JLIB_HTML_BATCH_LANGUAGE_NOCHANGE</option>
		</field>

		<field name="created_by"
			type="winduser"
			label="JAUTHOR"
			description="JLIB_HTML_BATCH_USER_LABEL_DESC"
			labelclass="control-label"
			class="input-xlarge inputbox"
			>
			<option>JSELECT</option>
		</field>

		<field name="catid"
			type="category"
			label="JLIB_HTML_BATCH_MENU_LABEL"
			description=""
			extension="com_flower"
			labelclass="control-label"
			class="input-xlarge inputbox"
			action="true"
			>
			<option>JOPTION_SELECT_CATEGORY</option>
		</field>

		<field name="task"
			type="radio"
			label="JLIB_RULES_ACTION"
			description=""
			labelclass="control-label"
			class="combo btn-group"
			default="sakuras.batch.move"
			>
			<option value="sakuras.batch.move">JLIB_HTML_BATCH_MOVE</option>
			<option value="sakuras.batch.copy">JLIB_HTML_BATCH_COPY</option>
		</field>
	</fields>
</form>
```

A `<field>` is a batch field, if you want to add a `state` field, please write this code before `task` field:

``` xml
<field name="state"
    type="list"
    label="State"
    description="Change State"
    labelclass="control-label"
    class="input-xlarge inputbox"
>
    <option>JOPTION_SELECT_PUBLISHED</option>
    <option value="0">Unpublished</option>
    <option value="1">Published</option>
    <option value="-1">Trashed</option>
</field>
```

Go to Joomla admin, choose all items and click `Batch` button, note there is many published items now.

![batch-img](https://cloud.githubusercontent.com/assets/1639206/2601703/79b321f2-bb14-11e3-8bf6-47354df516fd.png)

Select the `Unpublished` option and click `Process`. The value of Unpublished is `0` so every state of items will change to `0`.

![batch-img](https://cloud.githubusercontent.com/assets/1639206/2601713/8e9aedc0-bb14-11e3-8aa0-bfb8514dfe7a.png)

All state changed to Unpublished.

![batch-img](https://cloud.githubusercontent.com/assets/1639206/2601719/ad5e3eec-bb14-11e3-8cd5-7df336dd3db8.png)

We can try `Copy` function.

![batch-img](https://cloud.githubusercontent.com/assets/1639206/2601726/cc4f9af8-bb14-11e3-95b8-b4ddeb90afa5.png)

Copy successful.

![batch-img](https://cloud.githubusercontent.com/assets/1639206/2601732/dd86870a-bb14-11e3-8eef-a8535df43b2f.png)
