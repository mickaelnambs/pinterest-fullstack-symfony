<?php

namespace App\Controller\Front;

use App\Entity\User;
use App\Form\ProfileType;
use App\Form\RegistrationType;
use App\Controller\BaseController;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Class AccountController.
 * 
 * @author Mickael Nambinintsoa <mickael.nambinitsoa07081999@gmail.com>
 */
class AccountController extends BaseController
{
    /** @var UserPasswordEncoderInterface */
    private $passwordEncoder;

    /**
     * AccountController constructeur.
     *
     * @param EntityManagerInterface       $entityManager
     * @param UserPasswordEncoderInterface $passwordEncoder
     */
    public function __construct(EntityManagerInterface $entityManager, UserPasswordEncoderInterface $passwordEncoder)
    {
        parent::__construct($entityManager);
        $this->passwordEncoder = $passwordEncoder;
    }

    /**
     * Permettant de creer un nouveau compte.
     * 
     * @Route("/register", name="account_register")
     *
     * @param Request $request
     * 
     * @return Response
     */
    public function registration(Request $request)
    {
        $user = new User();
        $form = $this->createForm(RegistrationType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $hash = $this->passwordEncoder->encodePassword($user, $user->getPassword());
            $user->setPassword($hash);

            $this->save($user);

            $this->addFlash(
                'success',
                "Votre compte a bien été créé ! Vous pouvez maintenant vous connecter !"
            );
            return $this->redirectToRoute('account_login');
        }
        return $this->render('account/registration.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * Permet a un user de se connecter.
     * 
     * @Route("/login", name="account_login")
     *
     * @param AuthenticationUtils $authenticationUtils
     * 
     * @return Response
     */
    public function login(AuthenticationUtils $authenticationUtils)
    {
        return $this->render('account/login.html.twig', [
            'username' => $authenticationUtils->getLastUsername(),
            'hasError' => $authenticationUtils->getLastAuthenticationError()
        ]);
    }

    /**
     * Permet de se deconnecter
     * 
     * @Route("/logout", name="account_logout")
     *
     * @return void
     */
    public function logout()
    {
        // Vide.
    }

    /**
     * Permet de modifier un profil user.
     * 
     * @Route("/{id}/edit", name="profile_edit", methods={"GET", "POST"})
     *
     * @param User $user
     * @param Request $request
     * 
     * @return Response
     */
    public function profile(Request $request)
    {
        $user = $this->getUser();
        $form = $this->createForm(ProfileType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->save($user);

            $this->addFlash(
                'success',
                "Votre compte a bien été modifié !"
            );

            return $this->redirectToRoute('account_logout');
        }
        return $this->render('account/profile.html.twig', [
            'user' => $user,
            'form' => $form->createView()
        ]);
    }
}
