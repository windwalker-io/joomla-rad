<?php
/**
 * Part of Windwalker project. 
 *
 * @copyright  Copyright (C) 2016 LYRASOFT. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */

namespace Windwalker\Model\Filter;

/**
 * The filter helper interface.
 *
 * @since 2.0
 */
interface FilterHelperInterface
{
	/**
	 * Set filter handler. Can be a callback or closure.
	 *
	 * Example:
	 * ``` php
	 * function(\JDatabaseQuery $query, $field, $value)
	 * {
	 *     return $query->where($field . ' <= ' . $value);
	 * }
	 * ```
	 *
	 * @param string   $name    The handler name.
	 * @param callback $handler Handler callback.
	 *
	 * @return  FilterHelperInterface Return self to support chaining.
	 */
	public function setHandler($name, $handler);

	/**
	 * Execute the filter and add in query object.
	 *
	 * @param \JDatabaseQuery $query Db query object.
	 * @param array           $data  The data from request.
	 *
	 * @return  \JDatabaseQuery Return the query object.
	 */
	public function execute(\JDatabaseQuery $query, $data = array());
}
