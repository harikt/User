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

namespace BenGor\User\Domain\Event;

use BenGor\User\Domain\Model\Event\UserRegistered;
use BenGor\User\Domain\Model\UserMailableFactory;
use BenGor\User\Domain\Model\UserMailer;
use BenGor\User\Domain\Event\UserRememberPasswordRequestedSubscriber as BaseUserRegisteredMailerSubscriber;
use Ddd\Domain\DomainEventSubscriber;
use Symfony\Component\Routing\Router;

/**
 * User registered mailer subscriber class.
 *
 * @author Beñat Espiña <benatespina@gmail.com>
 * @author Gorka Laucirica <gorka.lauzirika@gmail.com>
 */
final class UserRegisteredMailerSubscriber extends BaseUserRegisteredMailerSubscriber
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
        $user = $aDomainEvent->user();
        $url = $this->router->generate($this->route, $user->confirmationToken());
        $mail = $this->mailableFactory->build($user->email(), [
            'user' => $user, 'url' => $url,
        ]);

        $this->mailer->mail($mail);
    }

    /**
     * {@inheritdoc}
     */
    public function isSubscribedTo($aDomainEvent)
    {
        return $aDomainEvent instanceof UserRegistered;
    }
}
