<?php
/**
 * Part of Windwalker project. 
 *
 * @copyright  Copyright (C) 2016 LYRASOFT. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */

namespace Windwalker\Relation;

use Windwalker\Relation\Handler\RelationHandlerInterface;

/**
 * The RelationContainer class.
 * 
 * @since  2.1
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
	 * Get relation handler.
	 *
	 * @param  string  $name  Relation field name.
	 *
	 * @return  Relation  Return relation handler.
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
	 * Ser relation handler.
	 *
	 * @param  string                    $name      Field of this relation.
	 * @param  RelationHandlerInterface  $relation  Relation handler object.
	 *
	 * @return  static  Return self to support chaining.
	 */
	public function setRelation($name, RelationHandlerInterface $relation)
	{
		$this->relations[$name] = $relation;

		return $this;
	}

	/**
	 * Method to get property Relation handlers.
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
	 * @param   Relation[]  $relations  Relation handlers.
	 *
	 * @return  static  Return self to support chaining.
	 */
	public function setRelations($relations)
	{
		$this->relations = $relations;

		return $this;
	}
}
