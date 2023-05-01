<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerDemo\Zed\MerchantRegistration\Business\MerchantRegistrar;

use Generated\Shared\Transfer\MailTransfer;
use Generated\Shared\Transfer\MerchantResponseTransfer;
use Generated\Shared\Transfer\MerchantTransfer;
use Spryker\Zed\Locale\Business\LocaleFacadeInterface;
use Spryker\Zed\Mail\Business\MailFacadeInterface;
use SprykerDemo\Zed\MerchantRegistration\Business\Merchant\MerchantInterface;
use SprykerDemo\Zed\MerchantRegistration\Business\MerchantUser\MerchantUserInterface;
use SprykerDemo\Zed\MerchantRegistration\Communication\Plugin\Mail\MerchantRegistrationMailTypePlugin;

class MerchantRegistrar implements MerchantRegistrarInterface
{
    /**
     * @uses \Orm\Zed\User\Persistence\Map\SpyUserTableMap::COL_STATUS_ACTIVE
     *
     * @var string
     */
    protected const USER_STATUS_ACTIVE = 'active';

    /**
     * @var \SprykerDemo\Zed\MerchantRegistration\Business\Merchant\MerchantInterface
     */
    protected MerchantInterface $merchant;

    /**
     * @var \Spryker\Zed\Mail\Business\MailFacadeInterface
     */
    protected MailFacadeInterface $mailFacade;

    /**
     * @var \SprykerDemo\Zed\MerchantRegistration\Business\MerchantUser\MerchantUserInterface
     */
    protected MerchantUserInterface $merchantUser;

    protected LocaleFacadeInterface $localeFacade;

    /**
     * @param \SprykerDemo\Zed\MerchantRegistration\Business\Merchant\MerchantInterface $merchant
     * @param \Spryker\Zed\Mail\Business\MailFacadeInterface $mailFacade
     * @param \SprykerDemo\Zed\MerchantRegistration\Business\MerchantUser\MerchantUserInterface $merchantUser
     * @param \Spryker\Zed\Locale\Business\LocaleFacadeInterface $localeFacade
     */
    public function __construct(
        MerchantInterface $merchant,
        MailFacadeInterface $mailFacade,
        MerchantUserInterface $merchantUser,
        LocaleFacadeInterface $localeFacade
    ) {
        $this->merchant = $merchant;
        $this->mailFacade = $mailFacade;
        $this->merchantUser = $merchantUser;
        $this->localeFacade = $localeFacade;
    }

    /**
     * @param \Generated\Shared\Transfer\MerchantTransfer $merchantTransfer
     *
     * @return \Generated\Shared\Transfer\MerchantResponseTransfer
     */
    public function registerMerchant(MerchantTransfer $merchantTransfer): MerchantResponseTransfer
    {
        $merchantResponseTransfer = $this->merchant->add($merchantTransfer);
        $this->merchantUser->add($merchantTransfer, $merchantResponseTransfer);
        $this->sendRegistrationEmail($merchantTransfer);

        return $merchantResponseTransfer;
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
        $mailTransfer->setLocale($this->localeFacade->getCurrentLocale());

        $this->mailFacade->handleMail($mailTransfer);
    }
}
