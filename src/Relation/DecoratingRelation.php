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
 * The DecoratingRelation class.
 * 
 * @since  {DEPLOY_VERSION}
 */
class DecoratingRelation implements RelationHandlerInterface
{
	/**
	 * Property relation.
	 *
	 * @var  RelationHandlerInterface
	 */
	protected $relation;

	/**
	 * Class init.
	 *
	 * @param RelationHandlerInterface $relation
	 */
	public function __construct(RelationHandlerInterface $relation)
	{
		$this->relation = $relation;
	}

	/**
	 * Load all relative children data.
	 *
	 * @return  void
	 */
	public function load()
	{
		$this->relation->load();
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
		$this->relation->store();
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
		$this->relation->delete();
	}

	/**
	 * __call
	 *
	 * @param string $name
	 * @param array  $args
	 *
	 * @return  mixed
	 */
	public function __call($name, $args)
	{
		return call_user_func(array($this->relation, $name()), $args);
	}
}
