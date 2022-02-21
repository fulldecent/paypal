<?php
/**
 * 2007-2022 PayPal
 *
 *  NOTICE OF LICENSE
 *
 *  This source file is subject to the Academic Free License (AFL 3.0)
 *  that is bundled with this package in the file LICENSE.txt.
 *  It is also available through the world-wide-web at this URL:
 *  http://opensource.org/licenses/afl-3.0.php
 *  If you did not receive a copy of the license and are unable to
 *  obtain it through the world-wide-web, please send an email
 *  to license@prestashop.com so we can send you a copy immediately.
 *
 *  DISCLAIMER
 *
 *  Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 *  versions in the future. If you wish to customize PrestaShop for your
 *  needs please refer to http://www.prestashop.com for more information.
 *
 *  @author 2007-2022 PayPal
 *  @author 202 ecommerce <tech@202-ecommerce.com>
 *  @copyright PayPal
 *  @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

namespace PaypalAddons\classes\API\Request;

use PaypalAddons\classes\AbstractMethodPaypal;
use PaypalAddons\classes\API\ExtensionSDK\GetSellerStatus;
use PaypalAddons\classes\API\Response\Error;
use PaypalAddons\services\Core\PaypalMerchantId;
use PayPalCheckoutSdk\Core\PayPalHttpClient;
use PayPalHttp\HttpException;
use PayPal;
use Symfony\Component\VarDumper\VarDumper;

class PaypalGetSellerStatusRequest extends RequestAbstract
{

    public function execute()
    {
        $response = $this->getResponse();
        $getSellerStatus = new GetSellerStatus($this->getPartnerMerchantId(), $this->getSellerMerchantId());
        $getSellerStatus->headers = array_merge($getSellerStatus->headers, $this->getHeaders());

        try {
            $exec = $this->client->execute($getSellerStatus);
        } catch (\Throwable $e) {
            $error = new Error();
            $error->setMessage($e->getMessage())
                ->setErrorCode($e->getCode());
            return $response->setSuccess(false)->setError($error);
        }

        $response->setSuccess(true);
        $response->setData($exec);

        return $response;
    }

    /** @return ResponsePartnerReferrals*/
    protected function getResponse()
    {
        return new \PaypalAddons\classes\API\Response\Response();
    }

    protected function getPartnerMerchantId()
    {
        if ($this->method->isSandbox()) {
            return PayPal::PAYPAL_PARTNER_ID_SANDBOX;
        } else {
            return PayPal::PAYPAL_PARTNER_ID_LIVE;
        }
    }

    protected function getSellerMerchantId()
    {
        return $this->initPaypalMerchantId()->get();
    }

    protected function initPaypalMerchantId()
    {
        return new PaypalMerchantId();
    }
}
