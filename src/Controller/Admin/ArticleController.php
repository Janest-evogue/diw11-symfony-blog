<?php


namespace App\Controller\Admin;


use App\Entity\Article;
use App\Form\ArticleType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
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

    /**
     * @Route("/edition/{id}", defaults={"id": null}, requirements={"id" : "\d+"})
     */
    public function edit(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $originalImage = null;

        if (is_null($id)) { // création
            $article = new Article();
            $article->setAuthor($this->getUser());
            // passe dans le constructeur de la classe Article
            // $article->setPublicationDate(new \DateTime());
        } else { //modification
            $article = $em->find(Article::class, $id);

            // 404 si l'id reçu dans l'url n'est pas en bdd
            if (is_null($article)) {
                throw new NotFoundHttpException();
            }

            // si l'article contient une image
            if (!is_null($article->getImage())) {
                // nom du fichier venant de la bdd
                $originalImage = $article->getImage();

                // on sette l'image avec un objet File
                // pour le traitement par le formulaire
                $article->setImage(
                    new File($this->getParameter('upload_dir') . $originalImage)
                );
            }
        }

        $form = $this->createForm(ArticleType::class, $article);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                /** @var UploadedFile $image */
                $image = $article->getImage();

                // s'il y a eu une image uploadée
                if (!is_null($image)) {
                    // nom que l'on donne à l'image
                    $filename = uniqid() . '.' . $image->guessExtension();

                    // équivalent de move_uploaded_file()
                    $image->move(
                        // répertoire de destination
                        // cf le paramètre upload_dir dans config/services.yaml
                        $this->getParameter('upload_dir'),
                        // nom du fichier
                        $filename
                    );

                    // on sette l'attribut image de l'article avec le nom
                    // de l'image pour enregistrement en bdd
                    $article->setImage($filename);

                    // en modification, on supprime l'ancienne image
                    // s'il y en a une
                    if (!is_null($originalImage)) {
                        unlink($this->getParameter('upload_dir') . $originalImage);
                    }
                } else {
                    // sans upload, pour la modification, on sette l'attribut image
                    // avec le nom de l'ancienne image
                    $article->setImage($originalImage);
                }

                $em->persist($article);
                $em->flush();

                // message de confirmation
                $this->addFlash('success', "L'article est enregistré");
                // redirection vers la liste
                return $this->redirectToRoute('app_admin_article_index');
            } else {
                $this->addFlash('error', 'Le formulaire contient des erreurs');
            }
        }

        return $this->render(
            'admin/article/edit.html.twig',
            [
                'form' => $form->createView(),
                'original_image' => $originalImage
            ]
        );
    }

    /**
     * @Route("/suppression/{id}")
     */
    public function delete(Article $article)
    {
        $em = $this->getDoctrine()->getManager();

        $em->remove($article);
        $em->flush();

        $this->addFlash('success', "L'article est supprimée");

        return $this->redirectToRoute('app_admin_article_index');
    }
}
