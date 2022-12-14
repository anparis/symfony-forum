<?php

namespace App\Controller;

use App\Entity\Categorie;
use App\Form\CategorieType;
use App\Repository\CategorieRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CategorieController extends AbstractController
{
  #[Route('/categorie', name: 'app_categorie')]
  public function index(CategorieRepository $cr): Response
  {
    $categories = $cr->findAll();
    return $this->render('categorie/index.html.twig', [
      'categories' => $categories
    ]);
  }

  #[Route('/categorie/add', name: 'add_categorie')]
  public function add(ManagerRegistry $doctrine, Categorie $categorie = null, Request $request): Response
  {
    $post = $request->request;
    $nom_cat = $post->filter('nom');
    if ($post->has('submit') && $nom_cat) {
      $entityManager = $doctrine->getManager();
      $categorie = new Categorie();

      $categorie->setNom($nom_cat);
      $entityManager->persist($categorie);
      $entityManager->flush();
      return $this->redirectToRoute('app_categorie');
    } else {
      $this->addFlash(
        'error',
        'Le champ n\'est pas correctement rempli'
      );
    }
  }

  #[Route('/categorie/{id}/edit', name: 'edit_categorie')]
  public function edit(ManagerRegistry $doctrine, Categorie $categorie = null, Request $request): Response
  {
    $form = $this->createForm(CategorieType::class, $categorie);
    $form->handleRequest($request);

    // Traitement du formulaire
    // isValid = filterInput
    if ($form->isSubmitted() && $form->isValid()) {
      //objet categorie hydrate par les donnees du formulaire
      $categorie = $form->getData();

      // Manager de doctrine, permet d'acceder au persist et au flush
      $entityManager = $doctrine->getManager();
      // Prepare objet 
      $entityManager->persist($categorie);
      // Execute = Insert Into 
      $entityManager->flush();

      return $this->redirectToRoute('app_categorie');
    }
    return $this->render('categorie/add.html.twig', [
      'formCategorie' => $form->createView(),
      'edit' => $categorie->getId()
    ]);
  }

  #[Route('/categorie/{id}/delete', name: 'del_categorie')]
  public function delcategorie(Categorie $categorie, ManagerRegistry $doctrine): Response
  {
    // Manager de doctrine, permet d'acceder au persist et au flush
    $entityManager = $doctrine->getManager();
    $entityManager->remove($categorie);
    $entityManager->flush();

    return $this->redirectToRoute('app_categorie');
  }

  #[Route('/categorie/{id}', name: 'show_categorie')]
  public function showTopics(Categorie $cat): Response
  {
    return $this->render('categorie/show.html.twig', [
      'categorie' => $cat
    ]);
  }
}
