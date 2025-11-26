<?php
// phpcs:disable PSR1.Files.SideEffects
defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

use Joomla\CMS\Form\Form;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Factory;

// Sicherer Zugriff auf die übergebenen Daten
$product = $displayData['product'] ?? null;
$form = $displayData['form'] ?? null;
$available_stock = $displayData['available_stock'] ?? 0;
// Wenn kein Produkt oder Formular vorhanden ist, brechen wir ab.
if (!$product || !$form) {
    return;
}

// Eindeutige ID für die Zeile generieren, um Kollisionen bei IDs zu vermeiden
$rowId = ($product->product_id ?? uniqid());

?> 
<tr id="order_product_row_" <?php echo $rowId; ?>">
    <input type="hidden" name="jform[products][<?php echo $rowId; ?>][product_id]" value="<?php echo (int) $product->product_id; ?>">
    <td>
        <?php echo $product->product_title; ?>
    </td>
    <td class="product_description">
        <?php 
            $field = $form->getField('product_description');
            $field->setValue($product->product_description ?? 0);
            $field->name = 'products][' . $rowId . '][product_description';
            $field->id = 'products_' . $rowId . '_product_description';
            echo $field->input; ?>
        </td>
    <td class="product_price">
        <?php 
            $field = $form->getField('product_price');
            $field->setValue($product->product_price ?? 0);
            $field->name = 'products][' . $rowId . '][product_price';
            $field->id = 'products_' . $rowId . '_product_price';
            echo $field->input; ?>
    </td>
    <td class="reserved_quantity">
        <?php
            $field =$form->getField('reserved_quantity');
            $stockExceededMessage = Text::sprintf('COM_TSWRENT_ERROR_STOCK_EXCEEDED', (int) $available_stock);
            $field->setValue($product->reserved_quantity ?? 0);
            $field->name = 'products][' . $rowId . '][reserved_quantity';
            $field->id = 'products_' . $rowId . '_reserved_quantity';
            $field->max = (int) $available_stock; // Sicherstellen, dass max ein Integer ist
            $input = $field->input;
            $input = str_replace('onchange="', 'oninput="', $input); // Für sofortige Reaktion
            $input = str_replace('<input', '<input data-stock-exceeded-message="' . htmlspecialchars($stockExceededMessage) . '"', $input);
            echo $input;
        ?>
    </td>
    <td class="productdiscount">
        <?php
            $field = $form->getField('productdiscount');
            $field->setValue($product->productdiscount ?? 0);
            $field->name = 'products][' . $rowId . '][productdiscount';
            $field->id = 'products_' . $rowId . '_productdiscount';
            echo $field->input;
        ?>
    </td>
    <td class="product_price_total">
        <?php
            $field = $form->getField('product_price_total');
            $field->setValue($product->product_price_total ?? 0);
            $field->name = 'products][' . $rowId . '][product_price_total';
            $field->id = 'products_' . $rowId . 'product_price_total';
            echo $field->input;
        ?>
    </td>
    <td>
        <button type="button" class="btn btn-sm btn-danger" onclick="this.closest('tr').remove(); updateOrderTotal();">
            <span class="icon-trash" aria-hidden="true"></span>
            <?php echo Text::_('JACTION_DELETE'); ?>
        </button>
    </td>
</tr>
 