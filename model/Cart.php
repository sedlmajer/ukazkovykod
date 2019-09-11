<?php

namespace App\Model;

use Nette;

class Cart 
{
    public $products;
    private $count;
    
    public function __construct() 
    {
       $this->reset();
    }
    
    public function reset() 
    {
       $this->count=0; 
       $this->products=array(); 
    }
    
    public function getItemsCount()
    {
        return $this->count;
    } 
    
    public function getPrice()
    {
        $price=0;
        foreach ($this->products as $key => $product)
        {
           $price=$price+$product->price;
        }
        
        return $price;
    } 
    
    public function addProduct($product)
    {  
       $this->count++;
       $this->products[$this->count]=$product;
    } 
    
    public function removeProduct($id)
    {
        unset($this->products[$id]);
        $this->count--;
    }
    
    public function saveProductsToDB(Nette\Database\Context $db, $orderID)
    {
        foreach ($this->products as $key => $product)
        {
             $product->saveToDB($db, $orderID);
        }
    }
}
