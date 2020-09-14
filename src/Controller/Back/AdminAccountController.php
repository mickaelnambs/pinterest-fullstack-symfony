<?php

namespace App\Controller\Back;

use App\Entity\User;
use App\Form\EditUserType;
use App\Controller\BaseController;
use App\Repository\UserRepository;
use App\Service\PaginationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

/**
 * Class AdminAccountController.
 * 
 * @Route("/admin")
 * 
 * @author Mickael Nambinintsoa <mickael.nambinintsoa07081999@gmail.com>
 */
class AdminAccountController extends BaseController
{
    /**
     * AdminAccountController constructeur.
     *
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct($entityManager);
    }

    /**
     * @Route("/accounts/{page<\d+>?1}", name="admin_account_index")
     */
    public function index($page, PaginationService $pagination)
    {
        $pagination->setEntityClass(User::class)
            ->setLimit(5)
            ->setPage($page);

        return $this->render('admin/account/index.html.twig', [
            'pagination' => $pagination
        ]);
    }

    /**
     * Login.
     * 
     * @Route("/login", name="admin_account_login")
     *
     * @param AuthenticationUtils $authenticationUtils
     * 
     * @return Response
     */
    public function login(AuthenticationUtils $authenticationUtils)
    {
        return $this->render('admin/account/login.html.twig', [
            'username' => $authenticationUtils->getLastUsername(),
            'hasError' => $authenticationUtils->getLastAuthenticationError()
        ]);
    }

    /**
     * Logout.
     * 
     * @Route("/logout", name="admin_account_logout")
     *
     * @return void
     */
    public function logout()
    {
        // Vide.
    }

    /**
     * Permettant de modifier le profil user.
     * 
     * @Route("/accounts/{id}/edit", name="admin_account_edit")
     * @IsGranted("ROLE_ADMIN")
     *
     * @param User $user
     * @param Request $request
     * 
     * @return Response
     */
    public function edit(User $user, Request $request)
    {
        $form = $this->createForm(EditUserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->save($user);

            $this->addFlash(
                'success',
                "Le profil de {$user->getFirstName()} a bien été modifié !"
            );
            return $this->redirectToRoute('admin_account_index');
        }
        return $this->render('admin/account/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * Remove user.
     * 
     * @Route("/accounts/{id}/delete", name="admin_account_delete")
     * @IsGranted("ROLE_ADMIN")
     *
     * @param User $user
     * 
     * @return Response
     */
    public function delete(User $user)
    {
        if ($user) {
            $this->remove($user);
            $this->addFlash(
                'success',
                "L'utilisateur {$user->getFirstName()} a bien été supprimé !"
            );
            return new JsonResponse(['success' => 1]);
        } else {
            return new JsonResponse(['error' => 'Error'], 400);
        }
        return $this->redirectToRoute('admin_account_index');
    }
}
