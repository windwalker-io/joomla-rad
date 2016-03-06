<?php
/**
 * @package        {ORGANIZATION}.Module
 * @subpackage     {{extension.element.lower}}
 * @copyright      Copyright (C) 2016 {ORGANIZATION}, Inc. All rights reserved.
 * @license        GNU General Public License version 2 or later.
 */

// No direct access
defined('_JEXEC') or die;

// Include the syndicate functions only once
JLoader::registerPrefix('Mod{{extension.name.cap}}', __DIR__);

$model    = new Mod{{extension.name.cap}}Model($params);
$items    = $model->getItems($params);
$classSfx = Mod{{extension.name.cap}}Helper::escape($params->get('moduleclass_sfx'));

require JModuleHelper::getLayoutPath('{{extension.element.lower}}', $params->get('layout', 'default'));
