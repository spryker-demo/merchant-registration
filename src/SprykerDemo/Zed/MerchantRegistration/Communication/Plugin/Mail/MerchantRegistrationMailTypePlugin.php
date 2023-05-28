<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerDemo\Zed\MerchantRegistration\Communication\Plugin\Mail;

use Generated\Shared\Transfer\MailRecipientTransfer;
use Generated\Shared\Transfer\MailSenderTransfer;
use Generated\Shared\Transfer\MailTemplateTransfer;
use Generated\Shared\Transfer\MailTransfer;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;
use Spryker\Zed\MailExtension\Dependency\Plugin\MailTypeBuilderPluginInterface;

/**
 * @method \SprykerDemo\Zed\MerchantRegistration\Business\MerchantRegistrationFacadeInterface getFacade()
 * @method \SprykerDemo\Zed\MerchantRegistration\Communication\MerchantRegistrationCommunicationFactory getFactory()
 */
class MerchantRegistrationMailTypePlugin extends AbstractPlugin implements MailTypeBuilderPluginInterface
{
 /**
  * @var string
  */
    public const MAIL_TYPE = 'merchant registration mail';

    /**
     * @var string
     */
    protected const MAIL_TEMPLATE_HTML = 'merchantRegistration/mail/merchant_registration.html.twig';

    /**
     * @var string
     */
    protected const MAIL_TEMPLATE_TEXT = 'merchantRegistration/mail/merchant_registration.text.twig';

    /**
     * @var string
     */
    protected const GLOSSARY_KEY_MAIL_SUBJECT = 'New Merchant Registration | Spryker Marketplace Operations';

    /**
     * {@inheritDoc}
     * - Returns the name of mail for company status mail.
     *
     * @api
     *
     * @return string
     */
    public function getName(): string
    {
        return static::MAIL_TYPE;
    }

    /**
     * {@inheritDoc}
     * - Builds the `MailTransfer` with data for company status mail.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\MailTransfer $mailTransfer
     *
     * @return \Generated\Shared\Transfer\MailTransfer
     */
    public function build(MailTransfer $mailTransfer): MailTransfer
    {
        return $mailTransfer
            ->setSender($this->useDefaultSender())
            ->setSubject(static::GLOSSARY_KEY_MAIL_SUBJECT)
            ->addTemplate(
                (new MailTemplateTransfer())
                    ->setName(static::MAIL_TEMPLATE_HTML)
                    ->setIsHtml(true),
            )
            ->addTemplate(
                (new MailTemplateTransfer())
                    ->setName(static::MAIL_TEMPLATE_TEXT)
                    ->setIsHtml(false),
            )
            ->addRecipient(
                (new MailRecipientTransfer())
                    ->setEmail('karl.bischoff@spryker.com')
                    ->setName('Karl'),
            );
    }

    /**
     * @return \Generated\Shared\Transfer\MailSenderTransfer
     */
    protected function useDefaultSender(): MailSenderTransfer
    {
        $mailSenderTransfer = new MailSenderTransfer();

        $senderEmail = $this->getFactory()->getGlossaryFacade()->translate('mail.sender.email');
        $senderName = $this->getFactory()->getGlossaryFacade()->translate('mail.sender.name');

        $mailSenderTransfer
            ->setEmail($senderEmail)
            ->setName($senderName);

        return $mailSenderTransfer;
    }
}
