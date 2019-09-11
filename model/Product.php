<?php

namespace App\Model;
use Nette\Utils\Json;
use Nette;

class Product 
{
    public static $starsFormatPrices= ['30x40' => 690,'50x70' => 990,'60x90' => 1290];
    public static $starsFramePrices= [
        '30x40' => ["none"=>0,"white"=>199,"black"=>199,"aluminium"=>199],
        '50x70' => ["none"=>0,"white"=>349,"black"=>349,"aluminium"=>349],
        '60x90' => ["none"=>0,"white"=>449,"black"=>449,"aluminium"=>449]
        ];
    
    public $data;
    public $type;
    public $price;
    
    public function __construct($type,$data) 
    {
       $this->type=$type;
       $this->data=$data;       
       $this->calculatePrice();
    }
    
    
    public function calculatePrice()
    {
        $this->price=self::$starsFormatPrices[$this->data->format]+self::$starsFramePrices[$this->data->format][$this->data->frame];
        return $this->price;
    } 
    
    
    public function saveToDB(Nette\Database\Context $db, $orderID)
    {
        $row = $db->table('products')->insert([
         'price' => $this->price,
         'orders_id' => $orderID,
         'type' => $this->type,
         'data' => Json::encode($this->data),
        ]);
    }
}