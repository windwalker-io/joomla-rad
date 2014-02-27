<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  {{extension.element.lower}}
 *
 * @copyright   Copyright (C) 2012 Asikart. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Generated by AKHelper - http://asikart.com
 */

// No direct access
defined('_JEXEC') or die;

JHtml::_('behavior.framework');
{{extension.name.cap}}Helper::_('include.bootstrap', true, true);

// Create shortcuts to some parameters.
$params     = $this->item->params;
$canEdit    = $this->item->params->get('access-edit');
$user       = JFactory::getUser();
$item       = $this->item ;
$uri        = JFactory::getURI() ;
?>

<script type="text/javascript">
    jQuery('.dropdown-toggle').dropdown();
</script>
<form action="<?php echo JRoute::_('index.php?option={{extension.element.lower}}&view={{controller.item.name.lower}}'); ?>" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">

    <div id="{{extension.name.cap}}" class="windwalker item container-fluid {{controller.item.name.lower}}<?php echo $this->get('pageclass_sfx');?>">
        <div id="{{extension.name.lower}}-wrap-inner">

            <div class="{{controller.item.name.lower}}-item item<?php if($item->published == 0) echo ' well well-small'; ?>">
                <div class="{{controller.item.name.lower}}-item-inner">


                    <?php if ($canEdit) : ?>
                    <!-- Edit -->
                    <!-- ============================================================================= -->
                    <div class="edit-icon btn-toolbar fltrt">
                        <div class="btn-group">
                            <?php echo JHtml::_('link', JRoute::_('index.php?option={{extension.element.lower}}&task={{controller.item.name.lower}}.edit&id='.$item->id.'&return=' . {{extension.name.cap}}Helper::_('uri.base64', 'encode', $uri->toString())) ,JText::_('JTOOLBAR_EDIT'), array( 'class' => 'btn btn-small')); ?>
                            <button class="btn btn-small dropdown-toggle" data-toggle="dropdown">
                                <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu">
                                <li>
                                    <a class="jgrid" href="javascript:void(0);" onclick="return listItemTask('cb<?php echo $item->id; ?>','{{controller.list.name.lower}}.publish')" title="<?php echo JText::_('JTOOLBAR_ENABLE'); ?>"><?php echo JText::_('JTOOLBAR_ENABLE'); ?></a>
                                </li>
                                <li>
                                    <a class="jgrid" href="javascript:void(0);" onclick="return listItemTask('cb<?php echo $item->id; ?>','{{controller.list.name.lower}}.unpublish')" title="<?php echo JText::_('JTOOLBAR_DISABLE'); ?>"><?php echo JText::_('JTOOLBAR_DISABLE'); ?></a>
                                </li>
                            </ul>
                          </div>

                    </div>
                    <div style="display: none;">
                        <?php echo JHtml::_('grid.id', $item->id, $item->id); ?>
                    </div>
                    <!-- ============================================================================= -->
                    <!-- Edit End -->
                    <?php endif; ?>



                    <!-- Heading -->
                    <!-- ============================================================================= -->
                    <div class="heading">
                        <h2><?php echo $params->get('link_titles', 1) ? JHtml::_('link', $item->link, $item->title) : $item->title ?></h2>
                    </div>
                    <!-- ============================================================================= -->
                    <!-- Heading -->



                    <!-- afterDisplayTitle -->
                    <!-- ============================================================================= -->
                    <?php echo $this->item->event->afterDisplayTitle; ?>
                    <!-- ============================================================================= -->
                    <!-- afterDisplayTitle -->


                    <!-- beforeDisplayContent -->
                    <!-- ============================================================================= -->
                    <?php echo $this->item->event->beforeDisplayContent; ?>
                    <!-- ============================================================================= -->
                    <!-- beforeDisplayContent -->


                    <!-- Info -->
                    <!-- ============================================================================= -->
                    <div class="info">
                        <div class="info-inner">
                            <?php echo $this->showInfo($item, 'cat_title',   'jcategory', null, JRoute::_('index.php?option={{extension.element.lower}}&view={{controller.list.name.lower}}&id='.$item->get('catid'))); ?>
                            <?php echo $this->showInfo($item, 'created',     '{{extension.element.lower}}_created', null); ?>
                            <?php echo $this->showInfo($item, 'modified',    '{{extension.element.lower}}_modified', null); ?>
                            <?php echo $this->showInfo($item, 'created_user','{{extension.element.lower}}_created_by', null); ?>
                        </div>
                    </div>

                    <hr class="info-separator" />
                    <!-- ============================================================================= -->
                    <!-- Info -->



                    <!-- Content -->
                    <!-- ============================================================================= -->
                    <div class="content">
                        <div class="content-inner row-fluid">

                            <div class="span12">
                                <?php if( !empty($item->images) ): ?>
                                <div class="content-img">
                                    <?php echo JHtml::_('image', $item->images, $item->title); ?>
                                </div>
                                <?php endif; ?>

                                <div class="text">
                                    <?php echo $item->text; ?>
                                </div>
                            </div>

                        </div>
                    </div>
                    <!-- ============================================================================= -->
                    <!-- Content End -->



                    <!-- afterDisplayContent -->
                    <!-- ============================================================================= -->
                    <?php echo $this->item->event->afterDisplayContent; ?>
                    <!-- ============================================================================= -->
                    <!-- afterDisplayContent -->



                </div>
            </div>

        </div>
    </div>

    <div>
        <input type="hidden" name="task" value="" />
        <input type="hidden" name="boxchecked" value="0" />
        <input type="hidden" name="return" value="<?php echo base64_encode($uri->toString()); ?>" />
        <?php echo JHtml::_('form.token'); ?>
    </div>
</form>        
