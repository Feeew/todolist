<?php

namespace TodoBundle\Repository;

use Doctrine\ORM\EntityRepository;
use TodoBundle\Entity\Category;
use TodoBundle\Entity\Tag;
use TodoBundle\Entity\User;
use DateTime;

class TagRepository extends EntityRepository
{
    public function getTagWithTaskNumber()
    {
        $tags = $this->findAll();

        foreach($tags as $tag){
            $tag->nbTaches = count($tag->getTasks());
        }

        return $tags;

    }
}
