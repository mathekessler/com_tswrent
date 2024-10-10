
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
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Session\Session;

// Add the modal field script to the document head.
/** @var \Joomla\CMS\WebAsset\WebAssetManager $wa */
$wa = Factory::getApplication()->getDocument()->getWebAssetManager();
$wa ->useScript('com_tswrent.admin-order-edit')
->useScript('modal-content-select')
->useScript('joomla.dialog');



?>

<div class="row">
    <div class="col-lg-12">
        <fieldset id="fieldset-productsdata" class="options-form">
            <legend><?php echo Text::_('COM_TSWRENT_FIELDSET_GRADUATION'); ?></legend>
            <?php echo $this->form->renderField('graduation'); ?>
             <?php echo $this->form->renderField('factor'); ?>
             <?php echo $this->form->renderField('product_title'); ?>
        </fieldset>
        <fieldset id="fieldset-productsdata" class="options-form">
            <legend><?php echo Text::_('COM_TSWRENT_FIELDSET_PRODUCTS'); ?></legend>
            <div id="repeatable_products">
                <div class="table-responsive">
                    <div class="js-modal-content-select-field">
                        <?php
                            // Erstelle den Link zur modalen Ansicht
                            $link = 'index.php?option=com_tswrent&amp;view=products&amp;layout=modal&amp;tmpl=component&amp;'
                                . Session::getFormToken() . '=1';
  

                            // Erstelle eine zufällige ID für die Modalansicht
                            $randomId = base64_encode($item->title);
                            $modalParams = array(
                                'title'     => $this->escape($item->title),
                                'url'       => $link,
                                'height'    => '100%',
                                'width'     => '100%',
                                'bodyHeight' => 70,
                                'modalWidth' => 80,
                                'footer'    => '<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">'
                                            . Text::_('JLIB_HTML_BEHAVIOR_CLOSE') . '</button>',
                            );

                            // Render die Modal-Ansicht
                            echo HTMLHelper::_('bootstrap.renderModal', 'ModalSelect', $modalParams);
                            
                            /* Button zum Öffnen der Modalansicht
                            echo '<button type="button" class="group-add btn btn-sm btn-success" id="' . $this->id . '_select" 
                            data-bs-toggle="modal" data-bs-target="#ModalSelect' . $randomId . '">
                                <span class="icon-plus" aria-hidden="true"></span>
                            </button>';*/
                            
                            $wa->useScript('joomla.dialog-autocreate');

$dialogOptions = [
    'popupType'  => 'iframe',
    'src'        => Route::_($link, false),
    'textHeader' => $this->escape($item->title),
];

?>
<button
    type="button"
    class="btn btn-secondary"
    data-joomla-dialog="<?php echo $this->escape(json_encode($dialogOptions, JSON_UNESCAPED_SLASHES)); ?>"
   >
        <span class="icon-code-branch" aria-hidden="true"></span>
        <?php echo $label; ?>
</button>
      

                       ?>
                    </div>
                </div>
            </div>
              <table class="hika_listing adminlist hika_table" id="hikashop_order_product_listing" style="width:100%">
                    <thead>
                        <tr>
                            <th scope="col" class="w-10 d-none d-md-table-cell"><?php echo Text::_('COM_TSWRENT_HEADING_TITLE'); ?></th>
                            <th scope="col" class="w-10 d-none d-md-table-cell"><?php echo Text::_('COM_TSWRENT_HEADING_DESCRIPTION'); ?></th>
                            <th scope="col" class="w-5 d-none d-md-table-cell"><?php echo Text::_('COM_TSWRENT_HEADING_PRICE_PER_UNIT'); ?></th>
                            <th scope="col" class="w-1 d-none d-md-table-cell"><?php echo Text::_('COM_TSWRENT_HEADING_RESERVATION'); ?></th>
                            <th scope="col" class="w-1 d-none d-md-table-cell"><?php echo Text::_('COM_TSWRENT_HEADING_DISCOUNT'); ?></th>
                            <th scope="col" class="w-1 d-none d-md-table-cell"><?php echo Text::_('COM_TSWRENT_HEADING_PRICE'); ?></th>
                            
                        </tr>
                    </thead>
                    
                    <tbody id="order-products-table">
                    <tr>
                            <td>
                            
                       
                                    </tr>
                    </tbody>
                </table>

                    <template id="order-products-template">
                        <tr>
                            <td>
                            <?php echo $this->form->renderField('product-title'); ?>
                       
                                    </tr>
                    </template>
        </fieldset>
    </div>
</div>