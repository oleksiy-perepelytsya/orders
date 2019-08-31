<?php

namespace App\Entity;
use Doctrine\ORM\Mapping as ORM;

/**
 *@ORM\Entity
 *@ORM\Table(name="orders")
 */

class Order {
    /**
    *@ORM\Column(type="integer")
    *@ORM\Id
    *@ORM\GeneratedValue(strategy="AUTO")
    */
    private $id;

    /**
    *@ORM\Column(type="string",length=100)
    **/
    private $status;

    /**
     *@return mixed
     */
    public function getId(){
        return $this->id;
    }

    /**
    *@param mixed $id
    */
    public function setId($id){
        $this->id = $id;
    }

    /**
     *@return mixed
     */
    public function getStatus(){
        return $this->status;
    }

    /**
     *@param mixed $status
     */
    public function setName($status){
        $this->status = $status;
    }
}