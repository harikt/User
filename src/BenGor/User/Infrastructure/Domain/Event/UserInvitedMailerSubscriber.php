<?php

/*
 * This file is part of the BenGorUser library.
 *
 * (c) Beñat Espiña <benatespina@gmail.com>
 * (c) Gorka Laucirica <gorka.lauzirika@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace BenGor\User\Infrastructure\Domain\Event;

use BenGor\User\Domain\Model\UserMailableFactory;
use BenGor\User\Domain\Model\UserMailer;
use BenGor\User\Domain\Event\UserInvitedMailerSubscriber as BaseUserInvitedMailerSubscriber;
use Symfony\Component\Routing\Router;

/**
 * User invited mailer subscriber class.
 *
 * @author Beñat Espiña <benatespina@gmail.com>
 */
final class UserInvitedMailerSubscriber extends BaseUserInvitedMailerSubscriber
{
    /**
     * The route name.
     *
     * @var string
     */
    private $route;

    /**
     * The Symfony router component.
     *
     * @var Router
     */
    private $router;

    /**
     * Constructor.
     *
     * @param UserMailer          $aMailer          The mailer
     * @param UserMailableFactory $aMailableFactory The mailable factory
     * @param Router              $aRouter          The Symfony router
     * @param string              $aRoute           The route name
     */
    public function __construct(UserMailer $aMailer, UserMailableFactory $aMailableFactory, Router $aRouter, $aRoute)
    {
        parent::__construct($aMailer, $aMailableFactory);
        $this->router = $aRouter;
        $this->route = $aRoute;
    }

    /**
     * {@inheritdoc}
     */
    public function handle($aDomainEvent)
    {
        $guest = $aDomainEvent->userGuest();
        $url = $this->router->generate($this->route, $guest->invitationToken());
        $mail = $this->mailableFactory->build($guest->email(), [
            'user' => $guest, 'url' => $url,
        ]);

        $this->mailer->mail($mail);
    }
}
