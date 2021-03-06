<?php


namespace App\Manager;


use App\Entity\RegisterToken;
use App\Entity\ResetPasswordToken;
use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\ApplicationMailer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserManager
{

    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $entityManager;
    /**
     * @var ApplicationMailer
     */
    private ApplicationMailer $mailer;
    /**
     * @var UserPasswordEncoderInterface
     */
    private UserPasswordEncoderInterface $passwordEncoder;
    /**
     * @var UserRepository
     */
    private UserRepository $userRepository;

    public function __construct(EntityManagerInterface $entityManager, ApplicationMailer $mailer, UserPasswordEncoderInterface $passwordEncoder, UserRepository $userRepository)
    {
        $this->entityManager   = $entityManager;
        $this->mailer          = $mailer;
        $this->passwordEncoder = $passwordEncoder;
        $this->userRepository = $userRepository;
    }

    public function register(User $user, $plainPassword)
    {
        dump($user);
        $user->setPassword(
            $this->passwordEncoder->encodePassword(
                $user,
                $plainPassword
            )
        );

        $token = new RegisterToken();
        $token->setUser($user);
        $this->mailer->sendConfirmationMail($token);

        $this->entityManager->persist($token);
        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }

    public function verifyUser(RegisterToken $registerToken)
    {
        $validation          = false;
        $tokenExpirationDate = $registerToken->getValidityDate();
        $interval            = $tokenExpirationDate->diff(new \DateTime());

        if ($interval->invert) {
            $this->entityManager->persist($registerToken->getUser()->setIsVerified(true));
            $this->entityManager->remove($registerToken);
            $this->entityManager->flush();
            $validation = true;
        }
        return $validation;
    }

    public function sendReset(string $pseudo)
    {
        $findUser = $this->userRepository->findOneBy(['pseudo'=>$pseudo]);
        if ($findUser){
            $token = new ResetPasswordToken();
            $token->setUser($findUser);
            $this->mailer->sendResetMail($token);
            $this->entityManager->persist($token);
            $this->entityManager->flush();
        }

    }
}