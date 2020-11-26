<?php

namespace App\Controller;

use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class CategoryController extends AbstractController
{
    /**
     * @Route("/admin/category/create", name="category_create")
     */
    public function create(Request $request, SluggerInterface $sluggerInterface, EntityManagerInterface $entityManagerInterface): Response
    {
        $form = $this->createForm(CategoryType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $category = $form->getData();
            $category->setSlug(strtolower($sluggerInterface->slug($category->getName())));
            $entityManagerInterface->persist($category);
            $entityManagerInterface->flush();

            return $this->redirectToRoute('homepage');
        }

        $formView = $form->createView();

        return $this->render('category/create.html.twig', [
            'formView' => $formView
        ]);
    }

    /**
     * @Route("/admin/category/{id}/edit", name="category_edit")
     */
    public function edit($id, CategoryRepository $categoryRepository, Request $request, SluggerInterface $sluggerInterface, EntityManagerInterface $entityManagerInterface): Response
    {
        $category = $categoryRepository->find($id);
        $form = $this->createForm(CategoryType::class, $category);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $category->setSlug(strtolower($sluggerInterface->slug($category->getName())));
            $entityManagerInterface->flush();

            return $this->redirectToRoute('homepage');
        }

        $formView = $form->createView();
        
        return $this->render('category/edit.html.twig', [
            'category' => $category,
            'formView' => $formView
        ]);
    }
}