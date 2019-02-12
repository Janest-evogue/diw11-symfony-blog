<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Comment;
use App\Form\CommentType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ArticleController
 * @package App\Controller
 * @Route("/article")
 */
class ArticleController extends AbstractController
{
    /**
     * @Route("/{id}")
     */
    public function index(Request $request, Article $article)
    {
        /*
         * Sous l'article, si l'utilisateur n'est pas connecté,
         * l'inviter à le faire pour pouvoir écrire un commentaire
         * Sinon, lui afficher un formulaire avec un textarea
         * pour pouvoir écrire un commentaire.
         * Nécessite une entité Comment :
         * - content (text en bdd)
         * - publicationDate (datetime)
         * - user (l'utilisateur qui écrit le commentaire)
         * - article (l'article sur lequel on écrit le commentaire)
         * Nécessite le form type qui va avec contenant le textarea,
         * le contenu du commentaire ne doit pas pas être vide.
         *
         * Lister les commentaires en dessous, avec nom utilisateur,
         * date de publication, contenu du message
         *
         */
        $em = $this->getDoctrine()->getManager();
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $comment
                    ->setPublicationDate(new \DateTime())
                    ->setUser($this->getUser())
                    ->setArticle($article)
                ;

                $em->persist($comment);
                $em->flush();

                $this->addFlash(
                    'success',
                    'Votre commentaire est enregistré'
                );

                // redirection vers la page sur laquelle on est
                // pour ne pas être en POST
                return $this->redirectToRoute(
                    // la route de la page courante
                    $request->get('_route'),
                    [
                        'id' => $article->getId()
                    ]
                );
            }
        }

        return $this->render(
            'article/index.html.twig',
            [
                'article' => $article,
                'form' => $form->createView()
            ]
        );
    }
}
