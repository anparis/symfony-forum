<?php

namespace App\Controller;

use DateTime;
use App\Entity\Post;
use App\Entity\Topic;
use App\Entity\Auteur;
use App\Form\TopicType;
use App\Entity\Categorie;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class TopicController extends AbstractController
{
  #[Route('/topic', name: 'app_topic')]
  public function index(ManagerRegistry $doctrine): Response
  {
    $topics = $doctrine->getRepository(Topic::class)->findAll();
    return $this->render('topic/index.html.twig', [
      'topics' => $topics
    ]);
  }

  // #[Route('/categorie/{id}', name: 'add_topic')]
  // #[IsGranted('ROLE_USER')]
  // public function add(Topic $topic): Response
  // {
  //   return $this->render('topic/show.html.twig', [
  //     'topic' => $topic
  //   ]);
  // }

  #[Route('/categorie/{id}/add', name: 'add_topic')]
  #[IsGranted('ROLE_USER')]
  public function addCategorieTopic(ManagerRegistry $doctrine, Categorie $categorie = null, Request $request): Response
  {
    $post = $request->request;
    $titreTopic = $post->filter('titreTopic');
    $premierPost = $post->filter('premierPost');
    if ($post->has('submit') && $titreTopic && $premierPost) {
      $entityManager = $doctrine->getManager();

      // default -> to be replaced with connected user
      $auteur =  $doctrine->getRepository(Auteur::class)->findOneBy(['id' => 5]);
      // give actual date to every topics created
      $date = new DateTime();

      // hydrate topic object
      $topic = new Topic;
      $topic->setTitre($titreTopic);
      $topic->setDatePublication($date);
      $topic->setAuteur($auteur);
      $topic->setCategorie($categorie);

      // update topic in my database
      $entityManager->persist($topic);
      $entityManager->flush();

      // hydrate post object
      $post = new Post;
      $post->setTexte($titreTopic);
      $post->setDatePublication($date);
      $post->setAuteur($auteur);
      $post->setTopic($topic);

      // update post in my database
      $entityManager->persist($post);
      $entityManager->flush();

      return $this->redirectToRoute('show_topic', ['id' => $topic->getId()]);
    } else {
      $this->addFlash(
        'error',
        'Les champs ne sont pas correctement remplis'
      );
    }
  }

  #[Route('/topic/{id}/edit', name: 'edit_topic')]
  #[IsGranted('ROLE_USER')]
  public function edit(ManagerRegistry $doctrine, Topic $topic = null, Request $request): Response
  {
    $form = $this->createForm(TopicType::class, $topic);
    $form->handleRequest($request);

    // Traitement du formulaire
    // isValid = filterInput
    if ($form->isSubmitted() && $form->isValid()) {
      //objet topic hydrate par les donnees du formulaire
      $topic = $form->getData();

      // Manager de doctrine, permet d'acceder au persist et au flush
      $entityManager = $doctrine->getManager();
      // Prepare objet 
      $entityManager->persist($topic);
      // Execute = Insert Into 
      $entityManager->flush();

      return $this->redirectToRoute('show_categorie',['id'=>$topic->getCategorie()->getId()]);
    }
    return $this->render('topic/add.html.twig', [
      'formTopic' => $form->createView(),
      'edit' => $topic->getId()
    ]);
  }

  #[Route('/topic/{id}/delete', name: 'delete_topic')]
  #[IsGranted('ROLE_USER')]
  public function deltopic(Topic $topic, ManagerRegistry $doctrine): Response
  {
    // Manager de doctrine, permet d'acceder au persist et au flush
    $entityManager = $doctrine->getManager();
    $entityManager->remove($topic);
    $entityManager->flush();

    return $this->redirectToRoute('show_categorie',['id'=>$topic->getCategorie()->getId()]);
  }

  #[Route('/topic/{id}/verouiller', name: 'verouiller_topic')]
  #[IsGranted('ROLE_USER')]
  public function verouillerTopic(Topic $topic, ManagerRegistry $doctrine): Response
  {
    $entityManager = $doctrine->getManager();
    if($topic->isVerouille())
      $topic->setVerouille(0);
    else
      $topic->setVerouille(1);

    $entityManager->persist($topic);
    $entityManager->flush();

    return $this->redirectToRoute('show_categorie',['id'=>$topic->getCategorie()->getId()]);
  }

  #[Route('/topic/{id}/resoudre', name: 'resoudre_topic')]
  #[IsGranted('ROLE_USER')]
  public function resoudreTopic(Topic $topic, ManagerRegistry $doctrine): Response
  {
    $entityManager = $doctrine->getManager();
    if($topic->isResolu())
      $topic->setResolu(0);
    else
      $topic->setResolu(1);

    $entityManager->persist($topic);
    $entityManager->flush();

    return $this->redirectToRoute('show_categorie',['id'=>$topic->getCategorie()->getId()]);
  }

  #[Route('/topic/{id}', name: 'show_topic')]
  public function showTopics(Topic $topic): Response
  {
    return $this->render('topic/show.html.twig', [
      'topic' => $topic
    ]);
  }
}
