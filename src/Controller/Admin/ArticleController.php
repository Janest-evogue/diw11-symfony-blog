<?php


namespace App\Controller\Admin;


use App\Entity\Article;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ArticleController
 * @package App\Controller\Admin
 *
 * @Route("/article")
 */
class ArticleController extends AbstractController
{
    /**
     * @Route("/")
     */
    public function index()
    {
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository(Article::class);
        $articles = $repository->findBy([], ['publicationDate' => 'DESC']);

        return $this->render(
            'admin/article/index.html.twig',
            [
                'articles' => $articles
            ]
        );
    }

    /*
     * Ajouter la méthode edit() qui fait le rendu du formulaire et son traitement
     * Mettre un lien 'ajouter' dans la page de liste
     *
     * Validation : tous les champs obligatoires
     *
     * En création :
     * - setter l'auteur avec l'utilisateur connecté
     *      ($this->getUser() depuis un contrôleur)
     * - mettre la date de publication à maintenant
     *
     * Adapter la route et le contenu de la méthode pour que la page fonctionne
     * en modification et ajouter le bouton 'Modifier' dans la page de liste
     *
     * Enregistrer l'article en bdd si le formulaire est bien rempli
     * puis rediriger vers la liste avec un message de confirmation
     */
}
