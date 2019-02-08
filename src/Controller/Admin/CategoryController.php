<?php


namespace App\Controller\Admin;


use App\Entity\Category;
use App\Form\CategoryType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class CategoryController
 * @package App\Controller\Admin
 *
 * @Route("/categorie")
 */
class CategoryController extends AbstractController
{
    /**
     * @Route("/")
     */
    public function index()
    {
        $repository = $this->getDoctrine()->getRepository(Category::class);
        $categories = $repository->findBy([], ['name' => 'ASC']);

        return $this->render(
            'admin/category/index.html.twig',
            [
                'categories' => $categories
            ]
        );
    }

    /**
     * @Route("/edition")
     */
    public function edit()
    {
        $categorie = new Category();

        // création du formulaire relié à la catégorie
        $form = $this->createForm(CategoryType::class, $categorie);

        return $this->render(
            'admin/category/edit.html.twig',
            [
                // passage du formulaire au template
                'form' => $form->createView()
            ]
        );
    }
}
