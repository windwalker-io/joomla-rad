<?php
/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2011 - 2014 SMS Taiwan, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Windwalker\Router;

/**
 * Route class to handle single route pattern.
 *
 * @since 2.0
 */
class Route
{
	/**
	 * Build by resource.
	 *
	 * @param   string   $resource The resource key to find our route.
	 * @param   array    $data     The url query data.
	 * @param   boolean  $xhtml    Replace & by &amp; for XML compilance.
	 * @param   integer  $ssl      Secure state for the resolved URI.
	 *                             1: Make URI secure using global secure site URI.
	 *                             2: Make URI unsecure using the global unsecure site URI.
	 *
	 * @return  string Route url.
	 */
	public static function _($resource, $data = array(), $xhtml = true, $ssl = null)
	{
		$resource = explode('.', $resource, 2);

		if (count($resource) == 2)
		{
			$data['option']    = $resource[0];
			$data['_resource'] = $resource[1];
		}
		elseif (count($resource) == 1)
		{
			$data['option']    = $resource[0];
			$data['_resource'] = null;
		}

		$url = new \JUri;

		$url->setQuery($data);

		$url->setPath('index.php');

		return \JRoute::_((string) $url, $xhtml, $ssl);
	}

	/**
	 * Build route.
	 *
	 * @param   array  &$data The query data to build route.
	 *
	 * @return  string Route url.
	 */
	public static function build(&$data = array())
	{
		$menu = \JFactory::getApplication()->getMenu();

		$items = $menu->getMenu();

		$Itemid = null;

		$data['view'] = isset($data['view']) ? $data['view'] : null;

		// If itemid exists and view not, use itemid as menu item
		if (isset($data['Itemid']) && empty($data['view']))
		{
			if ($item = $menu->getItem($data['Itemid']))
			{
				$data['Itemid'] = $item->id;

				return $data;
			}
		}

		// Find option, view and id
		if (!empty($data['id']))
		{
			foreach ($items as $item)
			{
				$option = \JArrayHelper::getValue($item->query, 'option');
				$view   = \JArrayHelper::getValue($item->query, 'view');
				$id     = \JArrayHelper::getValue($item->query, 'id');

				if ($option == $data['option'] && $view == $data['view'] && $id == $data['id'])
				{
					$data['Itemid'] = $item->id;

					return $data;
				}
			}
		}

		// Find option and view
		if (!$Itemid && !empty($data['view']))
		{
			foreach ($items as $item)
			{
				$option = \JArrayHelper::getValue($item->query, 'option');
				$view   = \JArrayHelper::getValue($item->query, 'view');

				if ($option == $data['option'] && $view == $data['view'])
				{
					unset($data['view']);

					$data['Itemid'] = $item->id;

					return $data;
				}
			}
		}

		// Find option
		if (!$Itemid && empty($data['view']))
		{
			foreach ($items as $item)
			{
				$option = \JArrayHelper::getValue($item->query, 'option');

				if ($option == $data['option'])
				{
					$data['Itemid'] = $item->id;

					return $data;
				}
			}
		}

		return $data;
	}
}
