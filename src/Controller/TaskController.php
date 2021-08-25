<?php

namespace App\Controller;

use App\Repository\TaskRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TaskController extends AbstractController
{
    /**
     * Get nurse's tasks 
     * 
     * @Route("/api/tasks", name="api_tasks_get", methods="GET")
     */
    public function browse(TaskRepository $taskRepository): Response
    {
        $tasks = $taskRepository->findBy(['nurse'=>$this->getUser()]);

        // Resquest to Symfony to "serialize" entities in form of JSON
        return $this->json($tasks, 200, [], []);
    }

}
