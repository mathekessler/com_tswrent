<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_tswrent
 * @copyright   Copyright (C) 2005 - 2020 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

\defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\String\PunycodeHelper;
use Joomla\CMS\Uri\Uri;

$user       = Factory::getUser();
$userId     = $user->get('id');
$address = $displayData['address'] ?? null;


$logoRaw = $displayData['logo'] ?? ($address->logo ?? null);
$logo = null;
if ($logoRaw) {
    $logoParts = explode('#', $logoRaw);
    $logoRel = $logoParts[0];
    if ($logoRel) {
        if (preg_match('/^https?:\\/\\//', $logoRel)) {
            // Externe URL
            $logo = $logoRel;
        } else {
            // Lokaler Joomla-Pfad
            $logo = Uri::root() . ltrim($logoRel, '/');
        }
    }
}
?>

<div class="row">
    <div class="col-md-8">
        <dl class="com-tswrent__address contact-address dl-horizontal" itemprop="address" itemscope itemtype="https://schema.org/PostalAddress">
            <div class="controls has-success">
                <?php echo $address->title ?>
            </div>

            <?php if (is_object($address) ) : ?>
                <?php if (!empty($address->address)) : ?>
                <dt>
                    <span class="icon-address" aria-hidden="true"></span><span class="visually-hidden"><?php echo Text::_('COM_TSWRENT_ADDRESS'); ?></span>
                </dt>
                    <dd>
                        <span class="contact-street" itemprop="streetAddress">
                            <?php echo nl2br(htmlspecialchars($address->address), false); ?>
                        </span>
                    </dd>
                <?php endif; ?>
                <?php if (!empty($address->city)) : ?>
                    <dd>
                        <span class="contact-suburb" itemprop="addressLocality">
                            <?php echo htmlspecialchars($address->city); ?>
                        </span>
                    </dd>
                <?php endif; ?>
                <?php if (!empty($address->postalcode)) : ?>
                    <dd>
                        <span class="contact-postcode" itemprop="postalCode">
                            <?php echo htmlspecialchars($address->postalcode); ?>
                        </span>
                    </dd>
                <?php endif; ?>


                <?php if (!empty($address->email_to)) : ?>
                    <dt>
                        <span class="icon-envelope" aria-hidden="true"></span><span class="visually-hidden"><?php echo Text::_('COM_TSWRENT_EMAIL_LABEL'); ?></span>
                    </dt>
                    <dd>
                        <span class="contact-emailto">
                            <?php echo htmlspecialchars($address->email_to); ?>
                        </span>
                    </dd>
                <?php endif; ?>

                <?php if (!empty($address->telephone)) : ?>
                    <dt>
                        <span class="icon-phone" aria-hidden="true"></span><span class="visually-hidden"><?php echo Text::_('COM_TSWRENT_TELEPHONE'); ?></span>
                    </dt>
                    <dd>
                        <span class="contact-telephone" itemprop="telephone">
                            <?php echo htmlspecialchars($address->telephone); ?>
                        </span>
                    </dd>
                <?php endif; ?>

                <?php if (!empty($address->mobile)) : ?>
                    <dt>
                        <span class="icon-mobile" aria-hidden="true"></span><span class="visually-hidden"><?php echo Text::_('COM_TSWRENT_MOBILE'); ?></span>
                    </dt>
                    <dd>
                        <span class="contact-mobile" itemprop="telephone">
                            <?php echo htmlspecialchars($address->mobile); ?>
                        </span>
                    </dd>
                <?php endif; ?>

        
                <?php if(!empty($address->address)) : ?>
                    <?php if (isset($displayData['view'])) : ?>
                        <dt>
                            <span class="icon-download" aria-hidden="true"></span><span class="visually-hidden"><?php echo Text::_('COM_TSWRENT_DOWNLOAD'); ?></span>
                        </dt>
                        <dd>
                            <?php echo Text::_('COM_TSWRENT_DOWNLOAD_INFORMATION_AS'); ?>
                            <a href="<?php echo Route::_('index.php?option=com_tswrent&view=' . $displayData['view']['view'] . '&id=' . $displayData['view']['id'] . '&format=vcf'); ?>">
                                <?php echo Text::_('COM_TSWRENT_VCARD'); ?>
                            </a>
                        </dd>
                    <?php endif; ?>
                <?php endif; ?>
            <?php endif; ?>
        </dl>
    </div>

    <div class="col-md-4">
        <?php if ($logo) : ?>
            <div class="logo">
                <img src="<?php echo htmlspecialchars($logo); ?>" alt="Logo" style="max-width: 100%; height: auto;" />
            </div>
        <?php endif; ?>
    </div>
</div>