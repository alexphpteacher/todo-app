<?php

namespace App\Controller;

use App\Entity\Album;
use App\Entity\Task;
use App\Entity\User;
use App\Form\AlbumType;
use App\Form\TaskType;
use App\Repository\TaskRepository;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Routing\ClassResourceInterface;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\VarDumper\VarDumper;

/**
 * @Rest\RouteResource(
 *     "Task",
 *     pluralize=false
 * )
 */
class TaskController extends FOSRestController implements ClassResourceInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var TaskRepository
     */
    private $taskRepository;

    /**
     * temporary
     * @var User
     */
    private $user;

    public function __construct(
        EntityManagerInterface $entityManager,
        TaskRepository $taskRepository
    )
    {
        $this->entityManager = $entityManager;
        $this->taskRepository = $taskRepository;

        $this->user = $this->entityManager->getRepository(User::class)->find(1);
    }

    /**
     * @param $id
     *
     * @return Task|null
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    private function findTaskById($id)
    {
        $tasks = $this->taskRepository->findBy([
            'id' => $id,
            'user' => $this->user,
        ]);

        if (0 === count($tasks)) {
            throw new NotFoundHttpException();
        }

        return $tasks[0];
    }

    public function postAction(
        Request $request
    ) {
        $task = new Task();
        $task->setUser($this->user);

        $form = $this->createForm(TaskType::class, $task);

        $form->submit($request->request->all());

        if (false === $form->isValid()) {
            return $form;
        }

        $this->entityManager->persist($form->getData());
        $this->entityManager->flush();

        return $this->view(
            [
                'status' => 'ok',
            ],
            Response::HTTP_CREATED
        );
    }

    public function putAction(Request $request, string $id)
    {
        $task = $this->findTaskById($id);

        $form = $this->createForm(TaskType::class, $task);
        $form->submit($request->request->all());

        if (false === $form->isValid()) {
            return $form;
        }

        $this->entityManager->flush();

        return $this->view(null, Response::HTTP_NO_CONTENT);
    }

    public function deleteAction(string $id)
    {
        $task = $this->findTaskById($id);

        $this->entityManager->remove($task);
        $this->entityManager->flush();

        return $this->view(null, Response::HTTP_NO_CONTENT);
    }

    public function getAction(string $id)
    {
        return $this->findTaskById($id);
    }

    public function cgetAction()
    {
        return $this->user->getTasks();
    }
}