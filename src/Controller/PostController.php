<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Post;
use App\Form\CommentType;
use App\Form\PostType;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


#[Route('/post')]
class PostController extends AbstractController
{
    #[Route('/', name: 'app_post')]
    public function index(PostRepository $postRepository): Response
    {
        $posts = $postRepository->findAll();

        return $this->render('post/index.html.twig', [
        'posts'=>$posts
        ]);
    }
    #[Route('/show/{id}', name: 'show_post')]
    public function show(Post $post):Response
    {
        $comment = new Comment();
        $formCom = $this->createForm(CommentType::class, $comment);

        return $this->renderForm('post/show.html.twig', [
            'post'=>$post,
            'formCom'=>$formCom

        ]);
    }

    #[Route('/delete/{id}', name: 'delete_post')]
    public function delete(Post $post, EntityManagerInterface $manager):response
    {

        if($post){
            $manager->remove($post);
            $manager->flush();
        }

        return $this->redirectToRoute('app_post');
    }

        #[Route('/create', name: 'create_post')]
        #[Route('/create/{id}', name: 'update_post')]
        public function create(EntityManagerInterface $manager, Request $request, Post $post=null){

        $edit =false;

        if ($post) {
        $edit=true;
        }
        if(!$edit) {
            $post = new Post();
        }

        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){

            if (!$edit){
                $post->setCreatedAt(new \DateTime());
            }

            $manager->persist($post);
            $manager->flush();

            return $this->redirectToRoute("app_post");
        }

            return $this->renderForm('post/create.html.twig', [

                'form'=>$form
            ]);

        }

}
