<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerDemo\Zed\MerchantRegistration;

use Spryker\Shared\Application\ApplicationConstants;
use Spryker\Zed\Kernel\AbstractBundleConfig;
use SprykerDemo\Shared\MerchantRegistration\MerchantRegistrationConstants;

class MerchantRegistrationConfig extends AbstractBundleConfig
{
    /**
     * @uses \Spryker\Zed\MerchantGui\MerchantGuiConfig::PREFIX_MERCHANT_URL
     *
     * @var string
     */
    protected const PREFIX_MERCHANT_URL = 'merchant';

    /**
     * @var string
     */
    protected const MERCHANT_OMS_DEFAULT_PROCESS_NAME = 'MerchantDefaultStateMachine';

    /**
     * @api
     *
     * @return string
     */
    public function getMerchantOmsDefaultProcessName(): string
    {
        return static::MERCHANT_OMS_DEFAULT_PROCESS_NAME;
    }

    /**
     * @api
     *
     * @return string
     */
    public function getMerchantUrlPrefix(): string
    {
        return static::PREFIX_MERCHANT_URL;
    }

    /**
     * @api
     *
     * @return string
     */
    public function getZedHost(): string
    {
        return $this->get(ApplicationConstants::BASE_URL_ZED);
    }

    /**
     * @api
     *
     * @return string
     */
    public function getMerchantRegistrationRecipientEmail(): string
    {
        return $this->getConfig()->get(MerchantRegistrationConstants::MERCHANT_REGISTRATION_RECIPIENT_MAIL);
    }

    /**
     * @api
     *
     * @return string
     */
    public function getMerchantRegistrationRecipientName(): string
    {
        return $this->getConfig()->get(MerchantRegistrationConstants::MERCHANT_REGISTRATION_RECIPIENT_NAME);
    }
}
