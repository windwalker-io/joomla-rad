<?php
/**
 * Part of windwalker-joomla-rad-test project.
 *
 * @copyright  Copyright (C) 2011 - 2015 SMS Taiwan, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Windwalker\Test\Bootstrap;

use Windwalker\Bootstrap\Dropdown;

/**
 * Test class of Windwalker\Bootstrap\Dropdown
 */
class DropdownTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * Property refDropDownList.
	 *
	 * @var \ReflectionProperty
	 */
	protected $refDropDownList;

	/**
	 * Sets up the fixture, for example, open a network connection.
	 *
	 * This method is called before a test is executed.
	 *
	 * @return void
	 */
	public function setUp()
	{
		$this->refDropDownList = new \ReflectionProperty('Windwalker\\Bootstrap\\Dropdown', 'dropDownList');

		$this->refDropDownList->setAccessible(true);
	}

	/**
	 * Method to test clean().
	 *
	 * @return void
	 *
	 * @covers \Windwalker\Bootstrap\Dropdown::clean
	 */
	public function testClean()
	{
		Dropdown::addCustomItem('label', 'copy', 'id', 'task');
		Dropdown::addCustomItem('label', 'copy', 'id', 'task2');

		Dropdown::clean();

		$this->assertEquals(array(), $this->refDropDownList->getValue());
	}

	/**
	 * Method to test duplicate().
	 *
	 * @return void
	 *
	 * @covers \Windwalker\Bootstrap\Dropdown::addCustomItem
	 */
	public function testAddCustomItem()
	{
		// Clean dropDownList property
		$this->refDropDownList->setValue(array());

		$label = 'label';
		$icon = 'copy';
		$id = 168;
		$task = 'task';

		Dropdown::addCustomItem($label, $icon, $id, $task);

		$this->assertEquals(
			array(
				'<li>'
				. '<a href = "javascript://" onclick="listItemTask(\'cb' . $id . '\', \'' . $task . '\')">'
				. ($icon ? '<span class="icon-' . $icon . '"></span> ' : '')
				. $label
				. '</a>'
				. '</li>'
			),
			$this->refDropDownList->getValue()
		);
	}

	/**
	 * Method to test duplicate().
	 *
	 * @param string $label
	 * @param string $icon
	 * @param string $id
	 * @param string $prefix
	 * @param string $task
	 *
	 * @covers       \Windwalker\Bootstrap\Dropdown::duplicate
	 * @dataProvider duplicateProvider
	 */
	public function testDuplicate($label, $icon, $id, $prefix, $task)
	{
		// Clean dropDownList property
		$this->refDropDownList->setValue(array());

		Dropdown::duplicate($id, $prefix);

		$this->assertEquals(
			array(
				'<li>'
				. '<a href = "javascript://" onclick="listItemTask(\'cb' . $id . '\', \'' . $task . '\')">'
				. ($icon ? '<span class="icon-' . $icon . '"></span> ' : '')
				. $label
				. '</a>'
				. '</li>'
			),
			$this->refDropDownList->getValue()
		);
	}

	/**
	 * duplicateProvider
	 *
	 * @return  array
	 */
	public function duplicateProvider()
	{
		return array(
			array(
				\JText::_('JTOOLBAR_DUPLICATE'),    // label
				'copy',                             // icon
				168,                                // id
				'prefix',                           // prefix
				'prefix.copy',                      // task
			),
			array(
				\JText::_('JTOOLBAR_DUPLICATE'),    // label
				'copy',                             // icon
				168,                                // id
				'',                                 // prefix
				'copy',                             // task
			),
		);
	}
}
