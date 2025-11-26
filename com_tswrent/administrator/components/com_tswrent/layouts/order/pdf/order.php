<?php
/**
 * PDF Order template used by TemplateRenderer
 * Expects `$order` and optionally `$params` or other variables.
 */
\defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;

?>
<style>
    table { border-collapse: collapse; width: 100%; }
    table th, table td { border: 1px solid #ddd; padding: 4px; vertical-align: top; }
    table th { background-color: #f2f2f2; font-weight: bold; }
    .right { text-align: right; }
</style>

<table>
    <thead>
        <tr>
            <th>Pos.</th>
            <th><?php echo Text::_('JGLOBAL_TITLE'); ?></th>
            <th><?php echo Text::_('COM_TSWRENT_DESCRIPTION'); ?></th>
            <th><?php echo Text::_('COM_TSWRENT_RESERVATION'); ?></th>
            <th class="right"><?php echo Text::_('COM_TSWRENT_PER_PIECE'); ?></th>
            <th class="right"><?php echo Text::_('COM_TSWRENT_ORDER_PRODUCT_DISCOUNT'); ?></th>
            <th class="right"><?php echo Text::_('COM_TSWRENT_ORDER_PRODUCT_TOTAL_PRICE'); ?></th>
        </tr>
    </thead>
    <tbody>
    <?php $position = 1; foreach ($order->products as $product): ?>
        <tr>
            <td><?php echo $position++; ?></td>
            <td><?php echo htmlspecialchars($product->product_title); ?></td>
            <td><?php echo nl2br(htmlspecialchars($product->product_description)); ?></td>
            <td><?php echo (int) $product->reserved_quantity; ?></td>
            <td class="right"><?php echo number_format($product->product_price, 2, ',', '.'); ?> CHF</td>
            <td class="right"><?php echo number_format($product->productdiscount, 1, ',', '.'); ?> %</td>
            <td class="right"><?php echo number_format($product->product_price_total, 2, ',', '.'); ?> CHF</td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>

<br/><br/>
<table cellspacing="0" cellpadding="4" border="0" style="width:100%;">
    <tr>
        <td width="55%"></td>
        <td width="25%" class="right" style="font-weight:bold; border-top: 1px solid #000; border-bottom: 1px solid #000;"><?php echo Text::_('COM_TSWRENT_ORDER_DISCOUNT'); ?></td>
        <td width="20%" class="right" style="font-weight:bold; border-top: 1px solid #000; border-bottom: 1px solid #000;"><?php echo number_format($order->orderdiscount, 1, ',', '.'); ?> %</td>
    </tr>
    <tr>
        <td width="55%"></td>
        <td width="25%" class="right" style="font-weight:bold; border-top: 1px solid #000; border-bottom: 3px double #000;"><?php echo Text::_('COM_TSWRENT_TOTAL_PRICE'); ?></td>
        <td width="20%" class="right" style="font-weight:bold; border-top: 1px solid #000; border-bottom: 3px double #000;"><?php echo number_format($order->order_total_price, 2, ',', '.'); ?> CHF</td>
    </tr>
</table>

<br/><br/>
<p><?php echo Text::_('COM_TSWRENT_PDF_PAYMENT_TERMS'); ?></p>
<p><?php echo Text::_('COM_TSWRENT_PDF_CLOSING_GREETING'); ?></p>
