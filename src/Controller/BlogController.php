<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Article;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;

class BlogController extends AbstractController
{

protected $em;

public function __construct(EntityManagerInterface $entityManager)
{
    $this->em = $entityManager;
}


    /**
     * @Route("/", name="home")
     */
    
    public function home(){
    
            
            return $this->render('blog/home.html.twig',[
                'user' => "Boloss"]);
        
    }
    /**
     * @Route("/blog", name="app_blog")
     */
    public function index(Request $request, PaginatorInterface $paginator): Response
    {
        $repo = $this->getDoctrine()->getRepository(Article::class);
        $donnees = $repo->findAll();

        $articles = $paginator->paginate(
            $donnees,
            $request->query->getInt('page',1),
            4
        );

        return $this->render('blog/index.html.twig', [
            'controller_name' => 'BlogController',
            'articles' => $articles,
        ]);
    }
    /**
    * @Route("/blog/new", name="blog_create")
    * @Route("/blog/{id}/edit", name="blog_edit")
    */
    public function form(Article $article = null,Request $request, EntityManagerInterface $manager){

        if(!$article){
            $article = new Article();
        }
        //$article = new Article();

        $form = $this->createFormBuilder($article)
                     ->add('title')
                     ->add('content')
                     ->add('image')
                     ->getForm();
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            if(!$article->getId()){
                $article->setCreatedAt(new \DateTimeImmutable());
            }
            
            $em = $this->em;
            $manager->persist($article);
            $manager->flush();

            return $this->redirectToRoute('blog_show',['id' => $article->getId()]);
        }
        return $this->render('/blog/create.html.twig',[
            'formArticle' => $form->createView(),
            'editMode' => $article->getId() !== null
        ]);
    }

    /**
     * @Route("/blog/{id}", name="blog_show")
     */
    public function show($id){
        
        $repo = $this->getDoctrine()->getRepository(Article::class);
        $article = $repo->find($id);
        if(! $article)
        {
            throw $this->createNotFoundException('The product does not exist');
        }
        else{
        return $this->render('blog/show.html.twig',[
            'article' => $article
        ]);
    }
    }
     /**
     * @Route("/blog/{id}/delete", name="blog_delete")
     */
    public function delete(int $id): Response {
        $article = $this->getDoctrine()
          ->getRepository(Article::class)
          ->find($id);
     
        $manager = $this->getDoctrine()->getManager();
     
        $manager->remove($article);
        $manager->flush();
     
        $this->addFlash('success', "L'article $id a bien été supprimer");
     
        return $this->redirectToRoute('admin_evenements');
      }
    
}
    

