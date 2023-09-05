<?php
/*
 * 2007-2023 PayPal
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 *  versions in the future. If you wish to customize PrestaShop for your
 *  needs please refer to http://www.prestashop.com for more information.
 *
 *  @author 2007-2023 PayPal
 *  @author 202 ecommerce <tech@202-ecommerce.com>
 *  @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 *  @copyright PayPal
 *
 */

namespace PaypalAddons\classes\Vaulting;

use Configuration;
use Country;
use PaypalAddons\classes\AbstractMethodPaypal;
use PaypalAddons\classes\Constants\PaypalConfigurations;
use PaypalAddons\classes\Constants\Vaulting;
use Tools;

if (!defined('_PS_VERSION_')) {
    exit;
}

class VaultingFunctionality
{
    /** @var \MethodEC */
    protected $method;

    public function __construct()
    {
        $this->method = AbstractMethodPaypal::load('EC');
    }

    public function isAvailable()
    {
        $iso = Country::getIsoById((int) Configuration::get('PS_COUNTRY_DEFAULT'));

        return Tools::strtolower($iso) == 'us';
    }

    public function isEnabled()
    {
        return Vaulting::ENABLED === (int) Configuration::get(PaypalConfigurations::ACCOUNT_VAULTING) && (int) Configuration::get(PaypalConfigurations::EXPRESS_CHECKOUT_IN_CONTEXT);
    }

    public function enable($state)
    {
        Configuration::updateValue(PaypalConfigurations::ACCOUNT_VAULTING, (int) $state);

        return $this;
    }

    public function isCapabilityAvailable($refresh = true)
    {
        $isAvailable = (int) Configuration::get(Vaulting::ACCOUNT_VAULTING_STATE);

        if ($refresh == false && in_array($isAvailable, [Vaulting::IS_AVAILABLE, Vaulting::IS_UNAVAILABLE])) {
            return $isAvailable == Vaulting::IS_AVAILABLE;
        }

        $sellerStatus = $this->method->getSellerStatus();

        if ($sellerStatus->isSuccess() == false) {
            Configuration::updateValue(Vaulting::ACCOUNT_VAULTING_STATE, Vaulting::IS_UNAVAILABLE);

            return false;
        }

        if (empty($sellerStatus->getCapabilities())) {
            Configuration::updateValue(Vaulting::ACCOUNT_VAULTING_STATE, Vaulting::IS_UNAVAILABLE);

            return false;
        }

        foreach ($sellerStatus->getCapabilities() as $capability) {
            if (Tools::strtoupper($capability) == Vaulting::CAPABILITY) {
                Configuration::updateValue(Vaulting::ACCOUNT_VAULTING_STATE, Vaulting::IS_AVAILABLE);

                return true;
            }
        }

        Configuration::updateValue(Vaulting::ACCOUNT_VAULTING_STATE, Vaulting::IS_UNAVAILABLE);

        return false;
    }
}
