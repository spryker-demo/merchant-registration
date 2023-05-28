<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerDemo\Zed\MerchantRegistration\Business\MerchantRegistrar;

use Generated\Shared\Transfer\MailTransfer;
use Generated\Shared\Transfer\MerchantTransfer;
use Spryker\Zed\Locale\Business\LocaleFacadeInterface;
use Spryker\Zed\Mail\Business\MailFacadeInterface;
use SprykerDemo\Zed\MerchantRegistration\Communication\Plugin\Mail\MerchantRegistrationMailTypePlugin;
use SprykerDemo\Zed\MerchantRegistration\MerchantRegistrationConfig;

class MerchantRegistrarMailer implements MerchantRegistrarMailerInterface
{
    /**
     * @var \Spryker\Zed\Mail\Business\MailFacadeInterface
     */
    protected MailFacadeInterface $mailFacade;

    /**
     * @var \Spryker\Zed\Locale\Business\LocaleFacadeInterface
     */
    protected LocaleFacadeInterface $localeFacade;

    /**
     * @var \SprykerDemo\Zed\MerchantRegistration\MerchantRegistrationConfig
     */
    protected MerchantRegistrationConfig $config;

    /**
     * @param \Spryker\Zed\Mail\Business\MailFacadeInterface $mailFacade
     * @param \Spryker\Zed\Locale\Business\LocaleFacadeInterface $localeFacade
     * @param \SprykerDemo\Zed\MerchantRegistration\MerchantRegistrationConfig $config
     */
    public function __construct(
        MailFacadeInterface $mailFacade,
        LocaleFacadeInterface $localeFacade,
        MerchantRegistrationConfig $config
    ) {
        $this->mailFacade = $mailFacade;
        $this->localeFacade = $localeFacade;
        $this->config = $config;
    }

    /**
     * @param \Generated\Shared\Transfer\MerchantTransfer $merchantTransfer
     *
     * @return void
     */
    public function sendRegistrationEmail(MerchantTransfer $merchantTransfer): void
    {
        $mailTransfer = new MailTransfer();
        $mailTransfer->setType(MerchantRegistrationMailTypePlugin::MAIL_TYPE);
        $mailTransfer->setMerchant($merchantTransfer);
        $mailTransfer->setUrl($this->config->getZedHost() . '/merchant-gui/list-merchant');
        $mailTransfer->setLocale($this->localeFacade->getCurrentLocale());

        $this->mailFacade->handleMail($mailTransfer);
    }
}
