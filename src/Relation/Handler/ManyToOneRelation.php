<?php
/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2016 LYRASOFT. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */

namespace Windwalker\Relation\Handler;

use Windwalker\Relation\Action;
use Windwalker\Table\Table;

/**
 * The ManyToOneRelation class.
 * 
 * @since  2.1
 */
class ManyToOneRelation extends AbstractRelationHandler
{
	/**
	 * Class init.
	 *
	 * @param Table   $parent    The parent table od this relation.
	 * @param string  $field     Field of parent table to store children.
	 * @param \JTable $table     The Table object of this relation child.
	 * @param array   $fks       Foreign key mapping.
	 * @param string  $onUpdate  The action of ON UPDATE operation.
	 * @param string  $onDelete  The action of ON DELETE operation.
	 * @param array   $options   Some options to configure this relation.
	 */
	public function __construct($parent, $field = null, $table = null, $fks = array(), $onUpdate = Action::NO_ACTION, $onDelete = Action::NO_ACTION,
		$options = array())
	{
		$onUpdate = $onUpdate ? : Action::NO_ACTION;
		$onDelete = $onDelete ? : Action::NO_ACTION;

		parent::__construct($parent, $field, $table, $fks, $onUpdate, $onDelete, $options);
	}

	/**
	 * Load all relative children data.
	 *
	 * @return  void
	 */
	public function load()
	{
		$item = $this->db->setQuery($this->buildLoadQuery())->loadObject();

		$this->setParentFieldValue($this->convertToData($item));
	}

	/**
	 * Store all relative children data.
	 *
	 * The onUpdate option will work in this method.
	 *
	 * @return  void
	 */
	public function store()
	{
		// Many to one relation do not support store now.
	}

	/**
	 * Delete all relative children data.
	 *
	 * The onDelete option will work in this method.
	 *
	 * @return  void
	 */
	public function delete()
	{
		// Many to one relation do not support delete now.
	}
}
