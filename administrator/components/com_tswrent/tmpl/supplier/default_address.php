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

use Joomla\CMS\Language\Text;
use Joomla\CMS\String\PunycodeHelper;

?>
<dl class="com-tswrent__address supplier-address dl-horizontal" itemprop="address" itemscope itemtype="https://schema.org/PostalAddress">
    <?php
    if (
        ($this->item->address || $this->item->city  || $this->item->postalcode)
    ) : ?>
        <dt>
            <span class="icon-address" aria-hidden="true"></span><span class="visually-hidden"><?php echo Text::_('COM_TSWRENT_ADDRESS'); ?></span>
        </dt>
        <?php if ($this->item->address) : ?>
            <dd>
                <span class="contact-street" itemprop="streetAddress">
                    <?php echo nl2br($this->item->address, false); ?>
                </span>
            </dd>
        <?php endif; ?>

        <?php if ($this->item->city) : ?>
            <dd>
                <span class="contact-suburb" itemprop="addressLocality">
                    <?php echo $this->item->city; ?>
                </span>
            </dd>
        <?php endif; ?>
        <?php if ($this->item->postalcode) : ?>
            <dd>
                <span class="contact-postcode" itemprop="postalCode">
                    <?php echo $this->item->postalcode; ?>
                </span>
            </dd>
        <?php endif; ?>
    <?php endif; ?>

<?php if ($this->item->email_to) : ?>
    <dt>
        <span class="icon-envelope" aria-hidden="true"></span><span class="visually-hidden"><?php echo Text::_('COM_TSWRENT_EMAIL_LABEL'); ?></span>
    </dt>
    <dd>
        <span class="contact-emailto">
            <?php echo $this->item->email_to; ?>
        </span>
    </dd>
<?php endif; ?>

<?php if ($this->item->telephone) : ?>
    <dt>    
        <span class="icon-phone" aria-hidden="true"></span><span class="visually-hidden"><?php echo Text::_('COM_TSWRENT_TELEPHONE'); ?></span>
    </dt>
    <dd>
        <span class="contact-telephone" itemprop="telephone">
            <?php echo $this->item->telephone; ?>
        </span>
    </dd>
<?php endif; ?>
<?php if ($this->item->fax) : ?>
    <dt>
            <span class="icon-fax" aria-hidden="true"></span><span class="visually-hidden"><?php echo Text::_('COM_TSWRENT_FAX'); ?></span>
    </dt>
    <dd>
        <span class="contact-fax" itemprop="faxNumber">
        <?php echo $this->item->fax; ?>
        </span>
    </dd>
<?php endif; ?>
<?php if ($this->item->mobile) : ?>
    <dt>
            <span class="icon-mobile" aria-hidden="true"></span><span class="visually-hidden"><?php echo Text::_('COM_TSWRENT_MOBILE'); ?></span>
    </dt>
    <dd>
        <span class="contact-mobile" itemprop="telephone">
            <?php echo $this->item->mobile; ?>
        </span>
    </dd>
<?php endif; ?>
<?php if ($this->item->webpage) : ?>
    <dt>
            <span class="icon-home" aria-hidden="true"></span><span class="visually-hidden"><?php echo Text::_('COM_TSWRENT_WEBPAGE'); ?></span>
    </dt>
    <dd>
        <span class="contact-webpage">
            <a href="<?php echo $this->item->webpage; ?>" target="_blank" rel="noopener noreferrer" itemprop="url">
            <?php echo PunycodeHelper::urlToUTF8($this->item->webpage); ?></a>
        </span>
    </dd>
<?php endif; ?>
</dl>
