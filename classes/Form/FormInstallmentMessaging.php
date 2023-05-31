<?php
/**
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
 */

namespace PaypalAddons\classes\Form;

use Configuration;
use Module;
use PaypalAddons\classes\InstallmentBanner\ConfigurationMap;
use Tools;

class FormInstallmentMessaging implements FormInterface
{
    /** @var \Paypal */
    protected $module;

    protected $className;

    public function __construct()
    {
        $this->module = Module::getInstanceByName('paypal');
        $this->className = 'FormInstallmentMessaging';
    }

    /**
     * @return array
     */
    public function getDescription()
    {
        $fields = [];

        $fields[ConfigurationMap::ENABLE_INSTALLMENT] = [
            'type' => 'switch',
            'label' => $this->module->l('Pay later messaging', $this->className),
            'name' => ConfigurationMap::ENABLE_INSTALLMENT,
            'hint' => $this->module->l('Let your customers know about the option \'Pay 4x PayPal\' by displaying banners on your site.', $this->className),
            'values' => [
                [
                    'id' => ConfigurationMap::ENABLE_INSTALLMENT . '_on',
                    'value' => 1,
                    'label' => $this->module->l('Enabled', $this->className),
                ],
                [
                    'id' => ConfigurationMap::ENABLE_INSTALLMENT . '_off',
                    'value' => 0,
                    'label' => $this->module->l('Disabled', $this->className),
                ],
            ],
            'value' => (int) Configuration::get(ConfigurationMap::ENABLE_INSTALLMENT),
        ];

        $fields[ConfigurationMap::MESSENGING_CONFIG] = [
            'type' => 'hidden',
            'label' => '',
            'value' => Configuration::get(ConfigurationMap::MESSENGING_CONFIG),
            'name' => ConfigurationMap::MESSENGING_CONFIG,
        ];

        $fields['widget_code'] = [
            'type' => 'widget-code',
            'code' => '{widget name=\'paypal\' action=\'banner4x\'}',
            'name' => 'banner-widget-code',
            'label' => $this->module->l('Widget code', $this->className),
            'hint' => $this->module->l('By default, PayPal 4x banner is displayed on your web site via PrestaShop native hook. If you choose to use widgets, you will be able to copy widget code and insert it wherever you want in the web site template.', $this->className),
        ];

        $description = [
            'legend' => [
                'title' => $this->module->l('Buy Now Pay Later Banner', $this->className),
            ],
            'fields' => $fields,
            'submit' => [
                'title' => $this->module->l('Save', $this->className),
                'name' => 'installmentMessengingForm',
            ],
            'id_form' => 'pp_installment_messenging_form',
            'help' => '',
        ];

        return $description;
    }

    /**
     * @return bool
     */
    public function save($data = null)
    {
        if (is_null($data)) {
            $data = Tools::getAllValues();
        }

        $return = true;

        if (empty($data['installmentMessengingForm'])) {
            return $return;
        }

        $return &= Configuration::updateValue(
            ConfigurationMap::ENABLE_INSTALLMENT,
            (isset($data[ConfigurationMap::ENABLE_INSTALLMENT]) ? (int) $data[ConfigurationMap::ENABLE_INSTALLMENT] : 0)
        );
        $return &= Configuration::updateValue(
            ConfigurationMap::MESSENGING_CONFIG,
            (isset($data[ConfigurationMap::MESSENGING_CONFIG]) ? pSQL($data[ConfigurationMap::MESSENGING_CONFIG]) : '{}')
        );

        return $return;
    }
}
