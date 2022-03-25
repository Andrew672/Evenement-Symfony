<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Users;
use App\Form\EditUserType;
use App\Repository\UsersRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\BrowserKit\Request;
use Symfony\Component\HttpFoundation\Request as HttpFoundationRequest;
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
    /**
     * Liste des utilisateurs
     * @Route("/utilisateurs", name ="utilisateurs")
     */
    public function usersList(UsersRepository $users){
        return $this->render("admin/users.html.twig", [
            'users' => $users->findAll()
        ]);
         
    }
    /**
     * Modifier un utilisateur
     * @Route("/utilisateurs/{id}/edit", name ="edit_utilisateurs")
     */
    public function editUser(Users $user, HttpFoundationRequest $request)
    {
        $form = $this->createForm(EditUserType::class, $user);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('message','Utilisateur modifié avec succés');
            return $this->redirectToRoute('admin_utilisateurs');
        }
        return $this->render('admin/edituser.html.twig',[
            'userForm' => $form->createView()
        ]);
    }
    /**
     * Modifier un utilisateur
     * @Route("/utilisateurs/{id}/delete", name ="delete_utilisateurs")
     */
    public function deleteUser(int $id)
    {
        $article = $this->getDoctrine()
        ->getRepository(Users::class)
        ->find($id);
   
      $manager = $this->getDoctrine()->getManager();
   
      $manager->remove($article);
      $manager->flush();
   
      $this->addFlash('success', "L'utilisateur a bien été supprimer");
   
      return $this->redirectToRoute('admin_utilisateurs');
    }
    

}
