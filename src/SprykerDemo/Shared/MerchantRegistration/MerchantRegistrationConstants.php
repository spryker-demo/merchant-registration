<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerDemo\Shared\MerchantRegistration;

/**
 * Declares global environment configuration keys. Do not use it for other class constants.
 */
class MerchantRegistrationConstants
{
    /**
     * Specification:
     * - Defines the recipient mail for merchant registration notification mail.
     *
     * @api
     *
     * @var string
     */
    public const MERCHANT_REGISTRATION_RECIPIENT_MAIL = 'MERCHANT_REGISTRATION_MAIL_RECIPIENT';

    /**
     * Specification:
     * - Defines the recipient name for merchant registration notification mail.
     *
     * @api
     *
     * @var string
     */
    public const MERCHANT_REGISTRATION_RECIPIENT_NAME = 'MERCHANT_REGISTRATION_RECIPIENT_NAME';
}
