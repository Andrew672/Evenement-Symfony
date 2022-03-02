<?php

namespace App\Controller;

use App\Entity\Article;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
/**
 * Class AdminController
 * @package App\Controller
 * @Route("/admin", name="admin_")
 */
class AdminController extends AbstractController
{
    /**
     * @Route("/evenements", name="evenements")
     */
    
    public function index(): Response
    {

        $articles = $this->getDoctrine()->getRepository(Article::class)->findBy(array(),array('createdAt' => 'DESC'));

        return $this->render('admin/index.html.twig', [
            'articles' => $articles,
        ]);
    }


}
