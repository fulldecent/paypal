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

namespace PaypalAddons\classes\API\Request;

use Exception;
use PayPal;
use PaypalAddons\classes\AbstractMethodPaypal;
use PaypalAddons\classes\API\Client\HttpClient;
use PaypalAddons\classes\API\ExtensionSDK\Order\AuthorizationsCaptureRequest;
use PaypalAddons\classes\API\HttpAdoptedResponse;
use PaypalAddons\classes\API\Response\Error;
use PaypalAddons\classes\API\Response\ResponseCaptureAuthorize;
use PaypalAddons\classes\PaypalException;
use Throwable;

if (!defined('_PS_VERSION_')) {
    exit;
}

class PaypalCaptureAuthorizeRequest extends RequestAbstract
{
    /** @var \PaypalOrder */
    protected $paypalOrder;

    public function __construct(HttpClient $client, AbstractMethodPaypal $method, \PaypalOrder $paypalOrder)
    {
        parent::__construct($client, $method);
        $this->paypalOrder = $paypalOrder;
    }

    /**
     * @return ResponseCaptureAuthorize
     */
    public function execute()
    {
        $response = new ResponseCaptureAuthorize();
        $captureAuhorize = new AuthorizationsCaptureRequest($this->paypalOrder->id_transaction);

        try {
            $exec = $this->client->execute($captureAuhorize);

            if ($exec instanceof HttpAdoptedResponse) {
                $exec = $exec->getAdoptedResponse();
            }

            if (in_array($exec->statusCode, [200, 201, 202])) {
                $response->setSuccess(true)
                    ->setIdTransaction($exec->result->id)
                    ->setStatus($exec->result->status)
                    ->setScaState($this->getScaState($exec))
                    ->setDateTransaction($this->getDateTransaction($exec));
            } else {
                $error = new Error();
                $resultDecoded = json_decode($exec->message);
                $error->setMessage($resultDecoded->message);
                $response->setSuccess(false)->setError($error);
            }
        } catch (PaypalException $e) {
            $error = new Error();
            $resultDecoded = json_decode($e->getMessage());
            $error->setMessage($resultDecoded->details[0]->description)->setErrorCode($e->getCode());
            $response->setSuccess(false)
                ->setError($error);
        } catch (Throwable $e) {
            $error = new Error();
            $error->setErrorCode($e->getCode())->setMessage($e->getMessage());
            $response->setError($error)->setSuccess(false);
        } catch (Exception $e) {
            $error = new Error();
            $error->setErrorCode($e->getCode())->setMessage($e->getMessage());
            $response->setError($error)->setSuccess(false);
        }

        return $response;
    }

    protected function getDateTransaction($exec)
    {
        $date = \DateTime::createFromFormat(\DateTime::ATOM, $exec->result->create_time);

        if (!$date) {
            $date = new \DateTime();
        }

        return $date->format('Y-m-d H:i:s');
    }

    protected function getScaState($response)
    {
        if (empty($response->result->payment_source->card->authentication_result)) {
            return PayPal::SCA_STATE_UNKNOWN;
        }

        $authResult = $response->result->payment_source->card->authentication_result;

        if (empty($authResult->liability_shift)) {
            return PayPal::SCA_STATE_FAILED;
        }

        if ($authResult->liability_shift === PayPal::SCA_LIABILITY_SHIFT_POSSIBLE) {
            return PayPal::SCA_STATE_SUCCESS;
        }

        if ($authResult->liability_shift === PayPal::SCA_LIABILITY_SHIFT_NO) {
            if (!empty($authResult->three_d_secure->enrollment_status)) {
                $isScaNotPassed = in_array(
                    $authResult->three_d_secure->enrollment_status,
                    [
                        PayPal::SCA_BANK_NOT_READY,
                        PayPal::SCA_UNAVAILABLE,
                        PayPal::SCA_BYPASSED,
                    ]
                );

                if ($isScaNotPassed) {
                    return PayPal::SCA_STATE_NOT_PASSED;
                }
            }
        }

        return PayPal::SCA_STATE_FAILED;
    }
}
