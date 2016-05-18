<?php

namespace TodoBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use TodoBundle\Entity\Tag;
use TodoBundle\Entity\Task;
use TodoBundle\Form\Type\TaskType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class TaskController extends Controller
{
    /**
     * @Route("/task/create", name="create_task")
     */
    public function createAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $task = new Task($this->getUser());

        $form = $this->createForm(TaskType::class, $task);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em->persist($task);
            $em->flush();

            $this->addFlash(
                'notice',
                'Task added with success'
            );

            return $this->redirect('/');
        }

        return $this->render('TodoBundle:Task:create.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    private function getPagination($request, $tasks)
    {
        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $tasks,
            $request->query->getInt('page', 1),
            5
        );

        return $pagination;
    }

    /**
     * @Route("/task/list/{field}/{order}", requirements={
     *     "field" : "label|dueDate|createdAt",
     *     "order" : "asc|desc"
     * }, defaults={
     *     "field": "label",
     *     "order": "asc"
     * },  name="list_task")
     */
    public function listAction(Request $request, $field, $order)
    {
        $tasks = $this
            ->getDoctrine()
            ->getRepository('TodoBundle:Task')
            ->getTasksOrdered(
                $this->getUser(),
                $field,
                $order
            );

        return $this->render('TodoBundle:Task:list.html.twig', array(
            'pagination' => $this->getPagination($request, $tasks),
            'mon_path' => 'list_task'
        ));
    }

    /**
     * @Route("/task/category/{id}/{field}/{order}", requirements={
     *     "id" = "\d+",
     *     "field" : "label|dueDate|createdAt",
     *     "order" : "asc|desc"
     * }, defaults={
     *     "field": "label",
     *     "order": "asc"
     * }, name="list_task_category")
     */
    public function listByCategoryAction(Request $request, $field, $order)
    {
        $tasks = $this
            ->getDoctrine()
            ->getRepository('TodoBundle:Task')
            ->getTasksByCategoryAndUser(
                $this->getUser(),
                $request->get('id'),
                $field,
                $order
            );

        return $this->render('TodoBundle:Task:list.html.twig', array(
            'pagination' => $this->getPagination($request, $tasks)
        ));
    }
/**/
    /**
     * @Route("/task/tag/{tag}/{field}/{order}", requirements={
     *     "tag" = "\d+",
     *     "field" : "label|dueDate|createdAt",
     *     "order" : "asc|desc"
     * }, defaults={
     *     "field": "label",
     *     "order": "asc"
     * }, name="list_task_tag")
     * @ParamConverter("tag", class="TodoBundle:Tag")
     */
    public function listByTagAction(Request $request, Tag $tag, $field, $order)
    {
        $tasks = $this
            ->getDoctrine()
            ->getRepository('TodoBundle:Task')
            ->getTasksByTagAndUser(
                $this->getUser(),
                $tag,
                $field,
                $order
            );

        return $this->render('TodoBundle:Task:list.html.twig', array(
            'pagination' => $this->getPagination($request, $tasks)
        ));
    }

    /**
     * @Route("/task-of-the-day", name="tasks_of_the_day")
     */
    public function tasksOfTheDayAction(Request $request)
    {
        $tasks = $this
            ->getDoctrine()
            ->getRepository('TodoBundle:Task')
            ->getAllOfTheDay($this->getUser());

        return $this->render('TodoBundle:Task:list.html.twig', array(
            'tasks' => $tasks,
        ));
    }

    /**
     * @Route("/task-of-the-week", name="tasks_of_the_week")
     */
    public function tasksOfTheWeekAction(Request $request)
    {
        $tasks = $this
            ->getDoctrine()
            ->getRepository('TodoBundle:Task')
            ->getAllOfTheWeek($this->getUser());

        return $this->render('TodoBundle:Task:list.html.twig', array(
            'tasks' => $tasks,
        ));
    }

    /**
     * @Route("/task-of-the-month", name="tasks_of_the_month")
     */
    public function tasksOfTheMonthAction(Request $request)
    {
        $tasks = $this
            ->getDoctrine()
            ->getRepository('TodoBundle:Task')
            ->getAllOfTheMonth($this->getUser());

        return $this->render('TodoBundle:Task:list.html.twig', array(
            'tasks' => $tasks,
        ));
    }

    /**
    +     * @Route("/task/remove/{task}",requirements={"task" = "\d+"}, name="remove_task")
    +     */
    public function deleteAction(Task $task)
     {
         $em = $this->getDoctrine()->getEntityManager();
         $select_task = $em->getRepository('TodoBundle:Task')->find($task->getId());

         $em->remove($select_task);
         $em->flush();

         $this->addFlash(
             'notice',
             'Task deleted'
         );

         return $this->redirect("/task/list");
     }
}
