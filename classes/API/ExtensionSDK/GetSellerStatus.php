<?php
/*
 * 2007-2024 PayPal
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
 *  @author 2007-2024 PayPal
 *  @author 202 ecommerce <tech@202-ecommerce.com>
 *  @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 *  @copyright PayPal
 *
 */

namespace PaypalAddons\classes\API\ExtensionSDK;

use PaypalAddons\classes\API\HttpAdoptedResponse;
use PaypalAddons\classes\API\HttpResponse;
use PaypalAddons\classes\API\Request\HttpRequestInterface;
use PaypalAddons\classes\API\WrapperInterface;

if (!defined('_PS_VERSION_')) {
    exit;
}

class GetSellerStatus implements HttpRequestInterface, WrapperInterface
{
    /** @var string */
    protected $partnerMerchantId;
    /** @var string */
    protected $sellerMerchantId;

    protected $headers = [];

    public function __construct($partnerMerchantId, $sellerMerchantId)
    {
        $this->headers['Content-Type'] = 'application/json';
        $this->partnerMerchantId = $partnerMerchantId;
        $this->sellerMerchantId = $sellerMerchantId;
    }

    public function getPath()
    {
        return sprintf(
            '/v1/customer/partners/%s/merchant-integrations/%s',
            $this->partnerMerchantId,
            $this->sellerMerchantId
        );
    }

    /** @return array*/
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * @param array $headers
     *
     * @return self
     */
    public function setHeaders($headers)
    {
        if (is_array($headers)) {
            $this->headers = $headers;
        }

        return $this;
    }

    public function getBody()
    {
        return null;
    }

    public function getMethod()
    {
        return 'GET';
    }

    public function wrap($object)
    {
        if ($object instanceof HttpResponse) {
            return new HttpAdoptedResponse($object);
        }

        return $object;
    }
}
