<?php

namespace TodoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use TodoBundle\Entity\Category;
use TodoBundle\Form\Type\CategoryType;

class CategoryController extends Controller
{
    /**
     * @Route("/category/create", name="create_category")
     */
    public function createAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $category = new Category();

        $form = $this->createForm(CategoryType::class, $category);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em->persist($category);
            $em->flush();

            $this->addFlash(
                'notice',
                'Category added with success'
            );

            return $this->redirect('/');
        }

        return $this->render('TodoBundle:Category:create.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/category/list", name="list_category")
     */
    public function listAction()
    {
        $categories = $this->getDoctrine()->getRepository('TodoBundle:Category')->getCategoryWithTaskNumber();

        return $this->render('TodoBundle:Category:list.html.twig', array(
            'categories' => $categories,
        ));
    }

    /**
     * @Route("/category/delete/{id}", name="delete_category")
     */
    public function deleteAction($id)
    {
        $categorie = $this->getDoctrine()->getRepository('TodoBundle:Category')->findOneById($id);

        if(count($categorie->getTasks()) == 0){
            $em = $this->getDoctrine()->getManager();
            $em->remove($categorie);
            $em->flush();
            $this->addFlash(
                'notice',
                'Category deleted'
            );
        }
        else{
            $this->addFlash(
                'warning',
                'Category cannot be deleted'
            );
        }

        return $this->redirect("/category/list");
    }
}
