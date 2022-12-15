<?php

namespace App\Controller;

use DateTime;
use App\Entity\Post;
use App\Entity\Topic;
use App\Entity\Auteur;
use App\Form\PostType;
use App\Repository\PostRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PostController extends AbstractController
{
  #[Route('/post', name: 'app_post')]
  public function index(PostRepository $pr): Response
  {
    $posts = $pr->findAll();
    return $this->render('post/index.html.twig', [
      'posts' => $posts
    ]);
  }

  #[Route('/topic/{id}/post/add', name: 'add_post')]
  public function add(Topic $topic, Request $request, ManagerRegistry $doctrine)
  {
    $post = $request->request;
    $postTexte = $post->filter('post');
    if ($post->has('submit') && $postTexte) {
      $entityManager = $doctrine->getManager();
      // default -> to be replaced with connected user
      $auteur = $doctrine->getRepository(Auteur::class)->findOneBy(['id' => 5]);
      // give actual date to every posts created
      $date = new DateTime();

      // hydrate post object
      $post = new Post;
      $post->setTexte($postTexte);
      $post->setDatePublication($date);
      $post->setAuteur($auteur);
      $post->setTopic($topic);

      // update post in my database
      $entityManager->persist($post);
      $entityManager->flush();
      return $this->redirectToRoute('show_topic',['id' => $topic->getId()]);
    } else {
      $this->addFlash(
        'error',
        'Le champ n\'est pas correctement remplis'
      );
    }
  }

  #[Route('/post/{id}/edit', name: 'edit_post')]
  public function edit(ManagerRegistry $doctrine, Post $post = null, Request $request): Response
  {
    $form = $this->createForm(PostType::class, $post);
    $form->handleRequest($request);

    // Traitement du formulaire
    // isValid = filterInput
    if ($form->isSubmitted() && $form->isValid()) {
      //objet post hydrate par les donnees du formulaire
      $post = $form->getData();

      // Manager de doctrine, permet d'acceder au persist et au flush
      $entityManager = $doctrine->getManager();
      // Prepare objet 
      $entityManager->persist($post);
      // Execute = Insert Into 
      $entityManager->flush();

      return $this->redirectToRoute('show_topic', ['id'=> $post->getTopic()->getId()]);
    }
    return $this->render('post/add.html.twig', [
      'formPost' => $form->createView(),
      'edit' => $post->getId()
    ]);
  }

  #[Route('/post/{id}/delete', name: 'delete_post')]
  public function delpost(Post $post, ManagerRegistry $doctrine): Response
  {
    // Manager de doctrine, permet d'acceder au persist et au flush
    $entityManager = $doctrine->getManager();
    $entityManager->remove($post);
    $entityManager->flush();

    return $this->redirectToRoute('show_topic',['id'=>$post->getTopic()->getId()]);
  }
}
