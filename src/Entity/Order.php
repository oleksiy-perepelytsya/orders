<?php

namespace App\Entity;
use Doctrine\ORM\Mapping as ORM;

/**
 *@ORM\Entity
 *@ORM\Table(name="orders")
 */

class Order {

    const STATUS_CREATED = 'created';
    const STATUS_CONFIRMED = 'confirmed';
    const STATUS_DELIVERED = 'delivered';
    const STATUS_CANCELED = 'canceled';

    const ALLOWED_STATUSES = [
        self::STATUS_CREATED,
        self::STATUS_CONFIRMED,
        self::STATUS_DELIVERED,
        self::STATUS_CANCELED
    ];

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
    public function setStatus($status){
        $this->status = $status;
    }

    public function toArray(){
        return [
            'id' => $this->id,
            'status' => $this->status
        ];
    }
}