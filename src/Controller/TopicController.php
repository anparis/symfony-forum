<?php

namespace App\Controller;

use App\Entity\Topic;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
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
}
