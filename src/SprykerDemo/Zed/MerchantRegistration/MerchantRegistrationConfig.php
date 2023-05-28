<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerDemo\Zed\MerchantRegistration;

use Spryker\Shared\Application\ApplicationConstants;
use Spryker\Zed\Kernel\AbstractBundleConfig;

class MerchantRegistrationConfig extends AbstractBundleConfig
{
    /**
     * @api
     *
     * @return string
     */
    public function getZedHost(): string
    {
        return $this->get(ApplicationConstants::BASE_URL_ZED);
    }
}
