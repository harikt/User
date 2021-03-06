<?php

/*
 * This file is part of the BenGorUser package.
 *
 * (c) Beñat Espiña <benatespina@gmail.com>
 * (c) Gorka Laucirica <gorka.lauzirika@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace BenGorUser\User\Infrastructure\Domain\Model;

use BenGorUser\User\Domain\Model\User;
use BenGorUser\User\Domain\Model\UserEmail;
use BenGorUser\User\Domain\Model\UserFactorySignUp as BaseUserFactorySignUp;
use BenGorUser\User\Domain\Model\UserId;
use BenGorUser\User\Domain\Model\UserPassword;

/**
 * Implementation of user sign up factory domain class.
 *
 * @author Beñat Espiña <benatespina@gmail.com>
 * @author Gorka Laucirica <gorka.lauzirika@gmail.com>
 */
final class UserFactorySignUp implements BaseUserFactorySignUp
{
    /**
     * The entity fully qualified namespace.
     *
     * @var string
     */
    private $class;

    /**
     * Constructor.
     *
     * @param string $aClass The entity fully qualified namespace
     */
    public function __construct($aClass = User::class)
    {
        $this->class = $aClass;
    }

    /**
     * {@inheritdoc}
     */
    public function build(UserId $anId, UserEmail $anEmail, UserPassword $aPassword, array $roles)
    {
        return forward_static_call_array([$this->class, 'signUp'], [$anId, $anEmail, $aPassword, $roles]);
    }
}
