<?php

namespace TodoBundle\Repository;

use Doctrine\ORM\EntityRepository;
use TodoBundle\Entity\Category;
use TodoBundle\Entity\Tag;
use TodoBundle\Entity\User;
use DateTime;

class CategoryRepository extends EntityRepository
{
    public function getCategoryWithTaskNumber()
    {
        $categories = $this->findAll();

        foreach($categories as $categorie){
            $categorie->nbTaches = count($categorie->getTasks());
        }

        return $categories;
    }
}
