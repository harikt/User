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

namespace spec\BenGorUser\User\Application\Service\SignUp;

use BenGorUser\User\Application\Service\SignUp\SignUpUserCommand;
use BenGorUser\User\Application\Service\SignUp\SignUpUserHandler;
use BenGorUser\User\Domain\Model\Exception\UserAlreadyExistException;
use BenGorUser\User\Domain\Model\User;
use BenGorUser\User\Domain\Model\UserEmail;
use BenGorUser\User\Domain\Model\UserFactory;
use BenGorUser\User\Domain\Model\UserId;
use BenGorUser\User\Domain\Model\UserPassword;
use BenGorUser\User\Domain\Model\UserRepository;
use BenGorUser\User\Domain\Model\UserRole;
use BenGorUser\User\Infrastructure\Security\DummyUserPasswordEncoder;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * Spec file of SignUpUserHandler class.
 *
 * @author Beñat Espiña <benatespina@gmail.com>
 * @author Gorka Laucirica <gorka.lauzirika@gmail.com>
 */
class SignUpUserHandlerSpec extends ObjectBehavior
{
    function let(UserRepository $repository, UserFactory $factory)
    {
        $this->beConstructedWith(
            $repository,
            new DummyUserPasswordEncoder('encoded-password'),
            $factory
        );
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(SignUpUserHandler::class);
    }

    function it_signs_the_user_up(
        SignUpUserCommand $command,
        UserRepository $repository,
        UserFactory $factory,
        User $user
    ) {
        $command->id()->shouldBeCalled()->willReturn('user-id');
        $id = new UserId('user-id');
        $repository->userOfId($id)->shouldBeCalled()->willReturn(null);

        $command->email()->shouldBeCalled()->willReturn('user@user.com');
        $email = new UserEmail('user@user.com');
        $repository->userOfEmail($email)->shouldBeCalled()->willReturn(null);

        $command->password()->shouldBeCalled()->willReturn('plain-password');

        $command->roles()->shouldBeCalled()->willReturn(['ROLE_USER']);
        $roles = [new UserRole('ROLE_USER')];

        $factory->register(
            $id, $email, Argument::type(UserPassword::class), $roles
        )->shouldBeCalled()->willReturn($user);
        $user->enableAccount()->shouldBeCalled();
        $repository->persist($user)->shouldBeCalled();

        $this->__invoke($command);
    }

    function it_does_not_sign_up_if_user_id_already_exists(
        SignUpUserCommand $command,
        UserRepository $repository,
        User $user
    ) {
        $command->id()->shouldBeCalled()->willReturn('user-id');
        $id = new UserId('user-id');
        $repository->userOfId($id)->shouldBeCalled()->willReturn($user);

        $this->shouldThrow(UserAlreadyExistException::class)->during__invoke($command);
    }

    function it_does_not_sign_up_if_user_email_already_exists(
        SignUpUserCommand $command,
        UserRepository $repository,
        User $user
    ) {
        $command->id()->shouldBeCalled()->willReturn('user-id');
        $id = new UserId('user-id');
        $repository->userOfId($id)->shouldBeCalled()->willReturn(null);

        $command->email()->shouldBeCalled()->willReturn('user@user.com');
        $email = new UserEmail('user@user.com');
        $repository->userOfEmail($email)->shouldBeCalled()->willReturn($user);

        $this->shouldThrow(UserAlreadyExistException::class)->during__invoke($command);
    }
}
