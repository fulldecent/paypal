{*
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
{assign var="dynamicFieldBanner" value=$form.fields.PAYPAL_ENABLE_INSTALLMENT|default:false}

<form id="{$form.id_form}" class="mt-4 {[
  'form-modal' => $isModal
]|classnames}" data-form-configuration {block name='form_attributes'}{/block}
  {if $isModal}style="min-height:auto;"{/if} 
onsubmit="function (e) { e.preventDefault(); e.stopPropagation();}">
  {include file="module:paypal/views/templates/admin/_partials/form-fields.tpl" field=$form.fields.PAYPAL_ENABLE_INSTALLMENT dynamicField=$dynamicFieldBanner}

  {include file="module:paypal/views/templates/admin/_partials/form-fields.tpl" field=$form.fields.PAYPAL_INSTALLMENT_MESSAGING_CONFIG dynamicField=$form.fields.PAYPAL_ENABLE_INSTALLMENT}

  {if isset($form.fields.widget_code) && !$isModal }
    <div class="{[
      'd-none' => $dynamicFieldBanner && !$dynamicFieldBanner.value
      ]|classnames}" {if $dynamicFieldBanner.name|default:false}group-name="PAYPAL_ENABLE_INSTALLMENTG"{/if}>
              {include file="module:paypal/views/templates/admin/_partials/form-fields.tpl" field=$form.fields.widget_code dynamicField=$form.fields.PAYPAL_ENABLE_INSTALLMENT}          
    </div>
  {/if}
</form>

<div class="form-group row {[
  'd-none' => $dynamicFieldBanner && !$dynamicFieldBanner.value
]|classnames}" {if $dynamicFieldBanner.name|default:false}group-name="PAYPAL_ENABLE_INSTALLMENTG"{/if}>

  {if !$isModal}
      <div class="col-12 pr-0">
  {/if}

        <div class="form-group row" group-name="PAYPAL_ENABLE_INSTALLMENT">
          <div id="messaging-configurator"></div>
        </div>  
  {if !$isModal}
      </div>
  {/if}
</div>

<div class="form-group mb-0 d-flex justify-content-between pt-3 mt-auto">
  {block name='form_footer_buttons'}
    {if $isModal}
      <div class="d-flex justify-content-between flex-fill mr-3">
        <button data-btn-action="prev" class="btn btn-secondary">{l s='Back' mod='paypal'}</button>
        <button data-btn-action="next" class="btn btn-outline-primary">{l s='Skip this step' mod='paypal'}</button>
      </div>
    {/if}
    <button data-form-installment class="btn btn-secondary ml-auto" name={$form.submit.name}>{$form.submit.title}</button>
  {/block}
</div>