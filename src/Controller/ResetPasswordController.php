<?php

namespace App\Controller;

use App\Entity\Nurse;
use Symfony\Component\Mime\Address;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use SymfonyCasts\Bundle\ResetPassword\ResetPasswordHelperInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use SymfonyCasts\Bundle\ResetPassword\Controller\ResetPasswordControllerTrait;
use SymfonyCasts\Bundle\ResetPassword\Exception\ResetPasswordExceptionInterface;

/**
 * @Route("/reset-password")
 */
class ResetPasswordController extends AbstractController
{
    use ResetPasswordControllerTrait;

    private $resetPasswordHelper;

    public function __construct(ResetPasswordHelperInterface $resetPasswordHelper)
    {
        $this->resetPasswordHelper = $resetPasswordHelper;
    }

    /**
     * Display & process form to request a password reset.
     *
     * @Route("", name="app_forgot_password_request", methods="POST")
     */
    public function request(Request $request, MailerInterface $mailer)
    {
        $data = $request->getContent();
        $jsonData = json_decode($data, true);

        if (isset($jsonData['email'])){

            return $this->processSendingPasswordResetEmail(
                $jsonData['email'],
                $mailer
             );  
        }
    }

    /**
     * Validates and process the reset URL that the user clicked in their email.
     *
     * @Route("/reset/{token}", name="app_reset_password", methods="POST")
     */
    public function reset(Request $request, UserPasswordHasherInterface $passwordEncoder, string $token)
    {
        try {
            
            $user = $this->resetPasswordHelper->validateTokenAndFetchUser($token);

        } catch (ResetPasswordExceptionInterface $e) {

            return new JsonResponse(["message" => "le lien n'est pas valide"], Response::HTTP_NOT_FOUND);
        }

        $data = $request->getContent();

        $jsonData = json_decode($data, true);

        if($jsonData['password'] != $jsonData['confirmationPassword']) {
            return new JsonResponse(["message" => "mots de passe non identiques"], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        $this->resetPasswordHelper->removeResetRequest($token);
        
        // Encode the plain password, and set it.
        $encodedPassword = $passwordEncoder->hashPassword(
            $user,
            $jsonData['password']
        );
            
        $user->setPassword($encodedPassword);
        $this->getDoctrine()->getManager()->flush();

        return new JsonResponse(["message" => "mot de passe modifié"], Response::HTTP_OK);

    }

    private function processSendingPasswordResetEmail(string $emailFormData, MailerInterface $mailer)
    {

        $user = $this->getDoctrine()->getRepository(Nurse::class)->findOneBy([
            'email' => $emailFormData,
        ]);

        // Do not reveal whether a user account was found or not.
        if (!$user) {
            return new JsonResponse(["message" => "la requête n'est pas valide"], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {

            $resetToken = $this->resetPasswordHelper->generateResetToken($user);

        } catch (ResetPasswordExceptionInterface $e) {

            return new JsonResponse(["message" => $e->getReason()], Response::HTTP_UNPROCESSABLE_ENTITY);  
        }

        $email = (new TemplatedEmail())
            
            ->from(new Address('noreply.onurse@gmail.com', 'O\'Nurse'))
            ->to($user->getEmail())
            ->subject('Demande de réinitialisation de mot de passe')
            ->htmlTemplate('reset_password/email.html.twig')
            ->context([
                'resetToken' => $resetToken,
            ])
        ;

        $myToken= $resetToken->getToken();

        $mailer->send($email);

        return new JsonResponse(["token" => $myToken, "message" => "email envoyé"], Response::HTTP_OK);
    }
}
