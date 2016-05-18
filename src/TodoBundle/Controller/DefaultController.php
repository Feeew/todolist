<?php

namespace TodoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /**
     * @Route("/{field}/{order}", name="homepage", requirements={
     *     "field" : "label|dueDate|createdAt",
     *     "order" : "asc|desc"
     * }, defaults={
     *     "field": "label",
     *     "order": "asc"})
     */
    public function indexAction(Request $request, $field, $order)
    {
        $tasks = $this
            ->getDoctrine()
            ->getRepository('TodoBundle:Task')
            ->getTasksPassedByUser(
                $this->getUser(),
                $field,
                $order
            );

        return $this->render('TodoBundle:Default:index.html.twig', array(
            'pagination' => $this->getPagination($request, $tasks),
            'mon_path' => 'homepage'
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
}
