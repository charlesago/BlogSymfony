<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Post;
use App\Form\CommentType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CommentController extends AbstractController
{
    #[Route('/comment', name: 'app_comment')]
    public function index(): Response
    {
        return $this->render('comment/index.html.twig', [
        ]);
    }


    #[Route('/create/{id}', name: 'create_comment')]
    public function create(EntityManagerInterface $manager, Request $request, Post $post)
    {

        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $comment->setPost($post);
            $comment->setCreatedAt(new \DateTime());
            $manager->persist($comment);
            $manager->flush();

            return $this->redirectToRoute("show_post", ['id' => $comment->getPost()->getId()]);
        }
    }

    #[Route('/delete/{id}', name: 'delete_comment')]
    public function delete(Comment $comment, EntityManagerInterface $manager):response
    {

        if($comment){
            $manager->remove($comment);
            $manager->flush();
        }

        return $this->redirectToRoute("show_post", ['id' => $comment->getPost()->getId()]);
    }

    #[Route('/update/{id}', name: 'update_comment')]
    public function update(Comment $comment, EntityManagerInterface $manager, Request $request):Response
    {

        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $manager->persist($comment);
            $manager->flush();

            return $this->redirectToRoute("show_post", ['id' => $comment->getPost()->getId()]);
        }
        return $this->renderForm('comment/update.html.twig', [
            'formCom'=>$form
        ]);
    }


}
