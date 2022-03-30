<?php

use Phalcon\Mvc\Model;

class Orders extends Model
{
    public $id;
    public $email;
    public $product_name;
    public $quantity;
    public $price;
}
