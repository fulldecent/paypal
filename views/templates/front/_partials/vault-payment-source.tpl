{**
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
 *}

<div style="display: flex; align-items: center">
  <div payment-source vault-id="{$vault.id|default:''}" style="max-width: 400px; border: solid; margin: 20px; padding: 10px">
      {if $vault.paymentSource->getType() === 'paypal'}
        <div><b>{$vault.paymentSource->getEmail()|escape:'htmlall':'UTF-8'}</b></div>
        <div>{$vault.paymentSource->getAddress()|escape:'htmlall':'UTF-8'}</div>
        <div>{$vault.paymentSource->getPostcode()|escape:'htmlall':'UTF-8'}</div>
        <div>{$vault.paymentSource->getCity()|escape:'htmlall':'UTF-8'}</div>
        <div>{$vault.paymentSource->getCountry()|escape:'htmlall':'UTF-8'}</div>
      {/if}
  </div>
  <div>
    <button class="btn btn-danger">Remove</button>
  </div>
</div>

