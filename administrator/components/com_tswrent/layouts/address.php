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
use Joomla\CMS\Router\Route;
use Joomla\CMS\String\PunycodeHelper;

$user       = Factory::getUser();
$userId     = $user->get('id');
$address = $displayData['address'];
?>

<div class="col-6 ">
    <div class="row">
<dl class="com-tswrent__address contact-address dl-horizontal" itemprop="address" itemscope itemtype="https://schema.org/PostalAddress">
    <div class="controls has-success">
	    <?php echo $address->title ?>
	</div>
    <?php
    if (
        ($address->address || $address->city  || $address->postalcode)
    ) : ?>
        <dt>
            <span class="icon-address" aria-hidden="true"></span><span class="visually-hidden"><?php echo Text::_('COM_TSWRENT_ADDRESS'); ?></span>
        </dt>
        <?php if ($address->address) : ?>
            <dd>
                <span class="contact-street" itemprop="streetAddress">
                    <?php echo nl2br($address->address, false); ?>
                </span>
            </dd>
        <?php endif; ?>

        <?php if ($address->city) : ?>
            <dd>
                <span class="contact-suburb" itemprop="addressLocality">
                    <?php echo $address->city; ?>
                </span>
            </dd>
        <?php endif; ?>
        <?php if ($address->postalcode) : ?>
            <dd>
                <span class="contact-postcode" itemprop="postalCode">
                    <?php echo $address->postalcode; ?>
                </span>
            </dd>
        <?php endif; ?>
    <?php endif; ?>

<?php if ($address->email_to) : ?>
    <dt>
        <span class="icon-envelope" aria-hidden="true"></span><span class="visually-hidden"><?php echo Text::_('COM_TSWRENT_EMAIL_LABEL'); ?></span>
    </dt>
    <dd>
        <span class="contact-emailto">
            <?php echo $address->email_to; ?>
        </span>
    </dd>
<?php endif; ?>

<?php if ($address->telephone) : ?>
    <dt>    
        <span class="icon-phone" aria-hidden="true"></span><span class="visually-hidden"><?php echo Text::_('COM_TSWRENT_TELEPHONE'); ?></span>
    </dt>
    <dd>
        <span class="contact-telephone" itemprop="telephone">
            <?php echo $address->telephone; ?>
        </span>
    </dd>
<?php endif; ?>
<?php if ($address->fax) : ?>
    <dt>
            <span class="icon-fax" aria-hidden="true"></span><span class="visually-hidden"><?php echo Text::_('COM_TSWRENT_FAX'); ?></span>
    </dt>
    <dd>
        <span class="contact-fax" itemprop="faxNumber">
        <?php echo $address->fax; ?>
        </span>
    </dd>
<?php endif; ?>
<?php if ($address->mobile) : ?>
    <dt>
            <span class="icon-mobile" aria-hidden="true"></span><span class="visually-hidden"><?php echo Text::_('COM_TSWRENT_MOBILE'); ?></span>
    </dt>
    <dd>
        <span class="contact-mobile" itemprop="telephone">
            <?php echo $address->mobile; ?>
        </span>
    </dd>
<?php endif; ?>

</dl>

            </tbody>
        </table>
    </div>
</div>