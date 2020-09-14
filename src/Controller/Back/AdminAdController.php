<?php

namespace App\Controller\Back;

use App\Entity\Ad;
use App\Form\AdType;
use App\Repository\AdRepository;
use App\Controller\BaseController;
use App\Service\PaginationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * Class AdminAdController.
 * 
 * @Route("/admin/ads")
 * @IsGranted("ROLE_ADMIN")
 * 
 * @author Mickael Nambinintsoa <mickael.nambinintsoa07081999@gmail.com>
 */
class AdminAdController extends BaseController
{

    /**
     * AdminAdController constructeur.
     *
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct($entityManager);
    }

    /**
     * @Route("/{page<\d+>?1}", name="admin_ad_index")
     */
    public function index($page, PaginationService $pagination)
    {
        $pagination->setEntityClass(Ad::class)
            ->setLimit(5)
            ->setPage($page);

        return $this->render('admin/ad/index.html.twig', [
            'pagination' => $pagination
        ]);
    }

    /**
     * Permet de modifier une annonce.
     * 
     * @Route("/{id}/edit", name="admin_ad_edit", methods={"GET","POST"})
     *
     * @param Ad $ad
     * @param Request $request
     * 
     * @return Response
     */
    public function edit(Ad $ad, Request $request)
    {
        $form = $this->createForm(AdType::class, $ad);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $images = $form->get('images')->getData();

            $this->uploadFile($images, $ad);
            $this->save($ad);

            $this->addFlash(
                'success',
                "L'annonce {$ad->getTitle()} a bien été modifiée !"
            );
            return $this->redirectToRoute('admin_ad_index');
        }
        return $this->render('admin/ad/edit.html.twig', [
            'ad' => $ad,
            'form' => $form->createView()
        ]);
    }

    /**
     * Permet de supprimer une annonce.
     * 
     * @Route("/{id}/delete", name="admin_ad_delete")
     *
     * @param Ad $ad
     * 
     * @return Response
     */
    public function delete(Ad $ad)
    {
        if ($ad) {
            $this->remove($ad);
            $this->addFlash(
                'success',
                "L'annonce {$ad->getTitle()} a bien été supprimée !"
            );
            return new JsonResponse(['success' => 1]);
        } else {
            return new JsonResponse(['error' => 'Error'], 400);
        }
        return $this->redirectToRoute('admin_ad_index');
    }
}
