<?php

namespace App\Controller;

use App\Entity\Post;
use App\Form\NewPostFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="index")
     */
    public function index()
    {
        $date = date('Y-m-d');
        $post = $this->getDoctrine()
            ->getRepository(Post::class)
            ->findLastPost($date);
        

        return $this->render('default/index.html.twig', [
            'controller_name' => 'DefaultController',
            'post' => $post,

        ]);
    }

    /**
     * @Route("/newpost", name="new_post")
     */
    public function newPost(EntityManagerInterface $em, Request $request)
    {

        $post = new Post();


        $form = $this->createForm(NewPostFormType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){

                $em->persist($post);
                $em->flush();
                $id = $post->getId();
                
            return $this->redirectToRoute('view_post', [
                'id' => $id,
            ]);
        }



        return $this->render('default/newpost.html.twig', [
           'post' => $post,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/viewpost/{id}", name="view_post")
     */
    public function viewPost(Post $post)
    {


        return $this->render('default/view.html.twig', [
            'post' => $post,
        ]);
    }

    /**
     * @Route("/rewrite/{id}", name="rewrite_post")
     */
    public function rewritePost(Post $post, EntityManagerInterface $em, Request $request )
    {
        $form = $this->createFormBuilder($post)
            ->add('title', TextType::class, array('attr' => [
                'value' => $post->getTitle()
            ]))

            ->add('text', TextareaType::class, array('attr' => [
                'value' => $post->getText(),
                'class' => 'textarea'
            ]))
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {


            $em->persist($post);
            $em->flush();

            return $this->redirectToRoute('view_post', [
                'id' => $post->getId(),
            ]);
        }

        return $this->render('default/rewrite.html.twig', array(
            'form' => $form->createView(),
            'id' => $post->getId(),

        ));

    }

    /**
     * @Route("/list", name="list_post")
     */
        public function listPost()
        {

            $post = $this->getDoctrine()
                ->getRepository(Post::class)
                ->findAll();

            return $this->render('default/list.html.twig', [
               'post' => $post 
            ]);
            
        }

}
