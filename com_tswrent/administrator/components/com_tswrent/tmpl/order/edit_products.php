
<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_tswrent
 *
 * @copyright   Copyright (C) 2005 - 2020 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Session\Session;
use \Joomla\CMS\Layout\LayoutHelper;

// Add the modal field script to the document head.
/** @var \Joomla\CMS\WebAsset\WebAssetManager $wa */
$wa = Factory::getApplication()->getDocument()->getWebAssetManager();
$wa ->useScript('com_tswrent.admin-order-edit');

// Modal-Link mit CSRF-Token
$modalUrl = Route::_('index.php?option=com_tswrent&view=products&layout=modal&tmpl=component&' .
    Session::getFormToken());
?>

<div class="row">
    <div class="col-lg-12">
        <fieldset id="fieldset-productsdata" class="options-form">
            <legend><?php echo Text::_('COM_TSWRENT_FIELDSET_GRADUATION'); ?></legend>
            <?php echo $this->form->renderField('graduation_id'); ?>
             <?php echo $this->form->renderField('factor'); ?>
            
        </fieldset>
        <fieldset id="fieldset-productsdata" class="options-form">
            <legend><?php echo Text::_('COM_TSWRENT_FIELDSET_PRODUCTS'); ?></legend>
            <div id="repeatable_products">
                <div class="table-responsive">
                    <div class="js-modal-content-select-field">
                        <?php
                           // Joomla Bootstrap Modal rendern
                            echo HTMLHelper::_('bootstrap.renderModal', 'ModalSelect', [
                                'title'  => 'Produkt auswählen',
                                'url'    => $modalUrl,
                                'height' => '720',
                                'width'  => '1040px',
                                'max-width' => '100%',           
                                'footer' => '<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Schließen</button>',
                            ]);
                        ?>
                          


                        <button type="button" class="btn btn-primary"  data-bs-toggle="modal"data-bs-target="#ModalSelect">
                         <span class="icon-plus" aria-hidden="true"></span>
                        </button>
                    </div>
                </div>
            </div>
                
            <div id="selected-products"> 
                
                <table class="table" id="order_product_table">
                    <thead>
                        <tr>
                            <th><?php echo Text::_('JGLOBAL_TITLE'); ?></th>
                            <th><?php echo Text::_('COM_TSWRENT_DESCRIPTION'); ?></th>
                            <th><?php echo Text::_('COM_TSWRENT_PER_PIECE'); ?></th>
                            <th><?php echo Text::_('COM_TSWRENT_RESERVATION'); ?></th>
                            <th><?php echo Text::_('COM_TSWRENT_ORDER_PRODUCT_DISCOUNT'); ?></th> 
                            <th><?php echo Text::_('COM_TSWRENT_ORDER_PRODUCT_TOTAL_PRICE'); ?></th>
                            <th><?php echo Text::sprintf('JACTIONS', Text::_('COM_TSWRENT_PRODUKTS')); ?></th>
                        </tr>
                    </thead>
                    <tbody id="product-table-body">
                        <!-- JS will insert product rows here -->
                        <?php if (!empty($this->item->products)) {
                            foreach ($this->item->products as $product){
                              //$app = Factory::getApplication();    $app->enqueueMessage(print_r( $this->item->available_stock, true)) ;
                               echo LayoutHelper::render('order.order_product_row', ['product' => $product, 'form' => $this->form, 'available_stock' => $this->item->available_stock]);
                            
                            }
                        }?>
                        </tbody>
                    <tbody id= "order_totals">
                        <tr>
                            <td colspan="5" class="text-end"></td>
                           <td colspan="2" id="orderdiscount"> <?php echo $this->form->renderField('orderdiscount'); ?></td>

                        </tr>
                        <tr>
                             <td colspan="5" class="text-end"></td>
                           <td colspan="2" id="order_total_price"> <?php echo $this->form->renderField('order_total_price'); ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </fieldset>
    </div>
</div>