<?php

namespace App\Controller\Back;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Controller\BaseController;
use App\Service\PaginationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Class AdminCategoryController.
 * 
 * @Route("/categories")
 * 
 * @author Mickael Nambinintsoa <mickael.nambinintsoa07081999@gmail.com>
 */
class AdminCategoryController extends BaseController
{   

    /**
     * AdminCategoryController constructeur.
     *
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct($entityManager);
    }

    /**
     * @Route("/{page<\d+>?1}", name="admin_category_index")
     */
    public function index($page, PaginationService $pagination)
    {
        $pagination->setEntityClass(Category::class)
            ->setLimit(5)
            ->setPage($page);

        return $this->render('admin/category/index.html.twig', [
            'pagination' => $pagination
        ]);
    }

    /**
     * Permet de modifier une categorie.
     * 
     * @Route("/{id}/edit", name="admin_category_edit", methods={"GET", "POST"})
     *
     * @param Category $category
     * @param Request $request
     * 
     * @return Response
     */
    public function edit(Category $category, Request $request)
    {
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $category->setAuthor($this->getUser());

            $this->save($category);

            $this->addFlash(
                'success',
                "La categorie {$category->getTitle()} a bien été modifiée !"
            );
            return $this->redirectToRoute('admin_category_index');
        }
        return $this->render('admin/category/edit.html.twig', [
            'category' => $category,
            'form' => $form->createView()
        ]);
    }

    /**
     * Remove category.
     * 
     * @Route("/{id}/delete", name="admin_category_delete")
     *
     * @param Category $category
     * 
     * @return Response
     */
    public function delete(Category $category)
    {
        if ($category) {
            $this->remove($category);
            $this->addFlash(
                'success',
                "La catégorie <strong>{$category->getTitle()}</strong> a bien été supprimée !"
            );
            return new JsonResponse(['success' => 1]);
        } else {
            return new JsonResponse(['error' => 'Error'], 400);
        }
        return $this->redirectToRoute('admin_category_index');
    }
}
