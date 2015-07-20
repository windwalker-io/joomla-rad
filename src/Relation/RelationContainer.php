<?php
/**
 * Part of Windwalker project. 
 *
 * @copyright  Copyright (C) 2015 {ORGANIZATION}. All rights reserved.
 * @license    GNU General Public License version 2 or later;
 */

namespace Windwalker\Relation;

use Windwalker\Relation\Handler\RelationHandlerInterface;

/**
 * The RelationContainer class.
 * 
 * @since  {DEPLOY_VERSION}
 */
class RelationContainer
{
	/**
	 * Property relations.
	 *
	 * @var  Relation[]
	 */
	protected $relations;

	/**
	 * getRelation
	 *
	 * @param string $name
	 *
	 * @return  Relation
	 */
	public function getRelation($name)
	{
		if (empty($this->relations[$name]))
		{
			$this->relations[$name] = new Relation;
		}

		return $this->relations[$name];
	}

	/**
	 * setRelation
	 *
	 * @param string                   $name
	 * @param RelationHandlerInterface $relation
	 *
	 * @return  $this
	 */
	public function setRelation($name, RelationHandlerInterface $relation)
	{
		$this->relations[$name] = $relation;

		return $this;
	}

	/**
	 * Method to get property Relations
	 *
	 * @return  Relation[]
	 */
	public function getRelations()
	{
		return $this->relations;
	}

	/**
	 * Method to set property relations
	 *
	 * @param   Relation[] $relations
	 *
	 * @return  static  Return self to support chaining.
	 */
	public function setRelations($relations)
	{
		$this->relations = $relations;

		return $this;
	}
}
