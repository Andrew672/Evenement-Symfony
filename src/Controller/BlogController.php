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

class BlogController extends AbstractController
{

protected $em;

public function __construct(EntityManagerInterface $entityManager)
{
    $this->em = $entityManager;
}
    /**
     * @Route("/blog", name="app_blog")
     */
    public function index(): Response
    {
        $repo = $this->getDoctrine()->getRepository(Article::class);
        $articles = $repo->findAll();
        return $this->render('blog/index.html.twig', [
            'controller_name' => 'BlogController',
            'articles' => $articles,
        ]);
    }
    /**
     * @Route("/", name="home")
     */
    public function home(){

        return $this->render('blog/home.html.twig',[
            'user' => 'Boloss'
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
            return $this->render('blog/error404.html.twig');
        }
        else{
        return $this->render('blog/show.html.twig',[
            'article' => $article
        ]);
    }
    }
    
}
