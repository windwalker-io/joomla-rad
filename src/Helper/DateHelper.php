<?php
/**
 * Part of Windwalker project. 
 *
 * @copyright  Copyright (C) 2016 LYRASOFT. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */

namespace Windwalker\Helper;

use Windwalker\DI\Container;

define('WINDWALKER_DATE_FORMAT_SQL', \JFactory::getDbo()->getDateFormat());

/**
 * The Date Helper
 *
 * @since 2.0
 */
abstract class DateHelper
{
	const FORMAT_STANDARD = 'Y-m-d H:i:s';
	const FORMAT_YMD      = 'Y-m-d';
	const FORMAT_YMD_HI   = 'Y-m-d H:i';
	const FORMAT_YMD_HIS  = 'Y-m-d H:i:s';
	const FORMAT_SQL      = WINDWALKER_DATE_FORMAT_SQL;

	/**
	 * Property offset.
	 *
	 * @var  string
	 */
	protected static $tzOffset;

	/**
	 * Return the {@link \JDate} object
	 *
	 * @param   mixed  $time      The initial time for the JDate object
	 * @param   mixed  $tzOffset  The timezone offset.
	 *
	 * @return  \JDate object
	 */
	public static function getDate($time = 'now', $tzOffset = null)
	{
		if (!$tzOffset)
		{
			$config = Container::getInstance()->get('joomla.config');

			$tzOffset = $config->get('offset');
		}

		return \JFactory::getDate($time, $tzOffset);
	}

//	public static function toUTCTime($date, $format = null, $from = null)
//	{
//		if ($currentTimezone)
//		{
//			if (!$currentTimezone instanceof \DateTimeZone)
//			{
//				$currentTimezone = new \DateTimeZone($currentTimezone);
//			}
//		}
//
//		$from = $from ? : Container::getInstance()->get('joomla.config')->get('offset');
//	}

	/**
	 * Convert a date string to another timezone.
	 *
	 * @param string $date
	 * @param string $from
	 * @param string $to
	 * @param string $format
	 *
	 * @return  string
	 */
	public static function convert($date, $from = 'UTC', $to = 'UTC', $format = null)
	{
		if (!$format)
		{
			/** @var \JDatabaseDriver $db */
			$db = Container::getInstance()->get('db');

			$format = $db->getDateFormat();
		}

		$from = $from instanceof \DateTimeZone ? $from : new \DateTimeZone($from);
		$to   = $to   instanceof \DateTimeZone ? $to   : new \DateTimeZone($to);

		$date = new \JDate($date, $from);

		$date->setTimezone($to);

		return $date->format($format, true);
	}

	/**
	 * Convert a date string to local time.
	 *
	 * @param   string  $date    Date string.
	 * @param   string  $format  The converted format, keep NULL will use default SQL format.
	 * @param   string  $to      If keep NULL, will auto get target timezone from Joomla config.
	 *
	 * @return  string  The converted date string.
	 */
	public static function toLocalTime($date, $format = null, $to = null)
	{
		$to = $to ? : static::getTZOffset();

		return static::convert($date, 'UTC', $to, $format);
	}

	/**
	 * Convert a date string to server time.
	 *
	 * @param   string  $date    Date string.
	 * @param   string  $format  The converted format, keep NULL will use default SQL format.
	 * @param   string  $from    If keep NULL, will auto get timezone from Joomla config.
	 *
	 * @return  string  The converted date string.
	 */
	public static function toServerTime($date, $format = null, $from = null)
	{
		$from = $from ? : static::getTZOffset();

		return static::convert($date, $from, 'UTC', $format);
	}

	/**
	 * Get Timezone offset from config.
	 *
	 * @return  string  Timezone string.
	 */
	public static function getTZOffset()
	{
		if (!static::$tzOffset)
		{
			$config = Container::getInstance()->get('joomla.config');

			static::$tzOffset = $config->get('offset');
		}

		return static::$tzOffset;
	}

	/**
	 * Convert some common fields to local timezone.
	 *
	 * @param   object  $item    The item data object.
	 * @param   array   $fields  The fields we want to convert.
	 *
	 * @return  object  Return converted data.
	 */
	public static function itemDatesToLocal($item, array $fields = null)
	{
		if (!is_object($item))
		{
			throw new \InvalidArgumentException('Item should be object.');
		}

		if (!$fields)
		{
			$fields = array(
				'created',
				'publish_up',
				'publish_down',
				'modified'
			);
		}

		foreach ($fields as $field)
		{
			if (property_exists($item, $field))
			{
				$item->$field = DateHelper::toLocalTime($item->$field);
			}
		}
	}

	/**
	 * Convert some common fields to server timezone.
	 *
	 * @param   object  $item    The item data object.
	 * @param   array   $fields  The fields we want to convert.
	 *
	 * @return  object  Return converted data.
	 */
	public static function itemDatesToServer($item, array $fields = null)
	{
		if (!is_object($item))
		{
			throw new \InvalidArgumentException('Item should be object.');
		}

		if (!$fields)
		{
			$fields = array(
				'created',
				'publish_up',
				'publish_down',
				'modified'
			);
		}

		foreach ($fields as $field)
		{
			if (property_exists($item, $field))
			{
				$item->$field = DateHelper::toServerTime($item->$field);
			}
		}

		return $item;
	}
}
