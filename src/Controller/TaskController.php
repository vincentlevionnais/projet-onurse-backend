<?php

namespace App\Controller;

use App\Entity\Task;
use Doctrine\ORM\EntityManager;
use App\Repository\TaskRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

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
        return $this->json($tasks, 200, [], ['groups' => 'tasks_get']);
    }

    /**
     * Get one task by id
     * 
     * @Route("/api/tasks/{id<\d+>}", name="api_tasks_get_item", methods="GET")
     */
    public function read(Task $task): Response
    {       
        $user = $this->getUser();
        $userId = $user->getId();

        $nurseTask = $task->getNurse();
        $nurseTaskId = $nurseTask->getId();

        if($userId != $nurseTaskId)
        {
            return new JsonResponse(["message" => "Tâche non trouvée"], Response::HTTP_NOT_FOUND);
        }

        return $this->json($task, Response::HTTP_OK, [], ['groups' => 'tasks_get']);
    }

    /**
     * Edit task by id
     * 
     * @Route("/api/tasks/{id<\d+>}", name="api_tasks_put_item", methods={"PUT", "PATCH"})
     */
    public function edit(Task $task = null, SerializerInterface $serializer, ValidatorInterface $validator, EntityManagerInterface $entityManager, Request $request): Response
    {
        $user = $this->getUser();
        $userId = $user->getId();

        $nurseTask = $task->getNurse();
        $nurseTaskId = $nurseTask->getId();

        if($userId != $nurseTaskId)
        {
            return new JsonResponse(["message" => "Tâche non trouvé"], Response::HTTP_NOT_FOUND);
        }

        if ($task === null) {
            return new JsonResponse(["message" => "Tâche non trouvé"], Response::HTTP_NOT_FOUND);
        }
        $data = $request->getContent();

        $task = $serializer->deserialize($data, Task::class, 'json', [AbstractNormalizer::OBJECT_TO_POPULATE => $task]);

        $errors = $validator->validate($task);

        if (count($errors) > 0) {

            $newErrors = [];

            foreach ($errors as $error) {       
                $newErrors[$error->getPropertyPath()][] = $error->getMessage();
            }

            return new JsonResponse(["errors" => $newErrors], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $entityManager->flush();

        return new JsonResponse(["message" => "Tâche modifiée"], Response::HTTP_OK);
    }

    /**
     * Add tasks 
     * 
     * @Route("/api/tasks", name="api_tasks_post", methods="POST")
     */
    public function add(Request $request, SerializerInterface $serializer, EntityManagerInterface $entityManager, ValidatorInterface $validator)
    {
        $jsonContent = $request->getContent();

        $task = $serializer->deserialize($jsonContent, Task::class, 'json');

        $errors = $validator->validate($task);

        if (count($errors) > 0) {
            return $this->json(["errors" => $errors],Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $task->setNurse($this->getUser());

        $entityManager->persist($task);
        $entityManager->flush();

        return $this->json(
            $task,
            Response::HTTP_CREATED,
            ['Location' => $this->generateUrl('api_tasks_get_item', ['id' => $task->getId()])],
            ['groups' => 'tasks_get']
        );
    }

    /**
     * Delete a task
     * 
     * @Route("/api/tasks/{id<\d+>}", name="api_tasks_delete", methods="DELETE")
     */
    public function delete(Task $task = null, EntityManagerInterface $entityManager)
    {
        $user = $this->getUser();
        $userId = $user->getId();

        $nurseTask = $task->getNurse();
        $nurseTaskId = $nurseTask->getId();

        if($userId != $nurseTaskId)
        {
            return new JsonResponse(["message" => "Tâche non trouvée"], Response::HTTP_NOT_FOUND);
        }

        if (null === $task) {

            $error = 'Tâche non trouvée';

            return $this->json(['error' => $error], Response::HTTP_NOT_FOUND);
        }

        $entityManager->remove($task);
        $entityManager->flush();

        return $this->json(['message' => 'Tâche supprimée.'], Response::HTTP_OK);
    }

}
