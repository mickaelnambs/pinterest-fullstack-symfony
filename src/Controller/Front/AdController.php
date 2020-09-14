<?php

namespace App\Controller\Front;

use App\Repository\AdRepository;
use App\Controller\BaseController;
use App\Entity\Ad;
use App\Form\AdType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class AdController.
 * 
 * @Route("/ads")
 * 
 * @author Mickael Nambinintsoa <mickael.nambinintsoa07081999@gmail.com>
 */
class AdController extends BaseController
{
    /** @var AdRepository */
    private $adRepository;

    /**
     * AdController constructeur.
     *
     * @param EntityManagerInterface $entityManager
     * @param AdRepository           $adRepository
     */
    public function __construct(EntityManagerInterface $entityManager, AdRepository $adRepository)
    {
        parent::__construct($entityManager);
        $this->adRepository = $adRepository;  
    }

    /**
     * Permet de lister toutes les annonces.
     * 
     * @Route("/", name="ad_index", methods={"GET"})
     * 
     * @return Response
     */
    public function index()
    {
        return $this->render('ad/index.html.twig', [
            'ads' => $this->adRepository->findAll()
        ]);
    }

    /**
     * Permet de creer une nouvelle annonce.
     * 
     * @Route("/new", name="ad_new", methods={"GET", "POST"})
     *
     * @param Request $request
     * 
     * @return Response
     */
    public function new(Request $request)
    {
        $ad = new Ad();
        $form= $this->createForm(AdType::class, $ad);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $images = $form->get('images')->getData();
            $ad->setAuthor($this->getUser());

            $this->uploadFile($images, $ad);
            $this->save($ad);

            $this->addFlash(
                'success',
                "L'annonce {$ad->getTitle()} a bien été créee !"
            );
            return $this->redirectToRoute('ad_index');
        }
        return $this->render('ad/new.html.twig', [
            'ad' => $ad,
            'form' => $form->createView()
        ]);
    }

    /**
     * Permet de modifier une annonce.
     * 
     * @Route("/{id}/edit", name="ad_edit", methods={"GET","POST"})
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
            $ad->setAuthor($this->getUser());

            $this->uploadFile($images, $ad);
            $this->save($ad);

            $this->addFlash(
                'success',
                "L'annonce {$ad->getTitle()} a bien été modifiée !"
            );
            return $this->redirectToRoute('ad_index');
        }
        return $this->render('ad/edit.html.twig', [
            'ad' => $ad,
            'form' => $form->createView()
        ]);
    }

    /**
     * Permet de supprimer une annonce.
     * 
     * @Route("/{id}/delete", name="ad_delete")
     *
     * @param Ad $ad
     * @return void
     */
    public function delete(Ad $ad)
    {
        $this->remove($ad);

        $this->addFlash(
            'success',
            "L'annonce {$ad->getTitle()()} a bien été supprimée !"
        );
        return $this->redirectToRoute('ad_index');
    }
}
