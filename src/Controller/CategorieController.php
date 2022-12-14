<?php

namespace App\Controller;

use App\Entity\Categorie;
use App\Repository\CategorieRepository;
use Doctrine\Persistence\ManagerRegistry;
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

  #[Route('/categorie/{id}', name: 'show_categorie')]
  public function showTopics(Categorie $cat): Response
  {
    return $this->render('categorie/show.html.twig', [
      'categorie' => $cat
    ]);
  }
}
