<?php

namespace App\Presenters;
use Nette;
use Nette\Application\UI;
use Nette\Utils\Json;
use Nette\Database\Context;
use App\Model\Product;
use App\Model\Cart;

final class CartPresenter extends BasePresenter
{
	public function renderOrder()
	{
		$this->template->title = 'Dokončení objednávky'.TITLE_SUFFIX;
    $this->template->showFAQ = false;
    $this->template->showInstaBlock = false;
    $this->template->showReviews = false;
    
    $this->template->deliveryMethods=$this->db->table("delivery");
    $this->template->paymentMethods=$this->db->table("payment");
    //$this->template->deliveryMethods=['1' => 'Česká pošta','2' => 'PPL','3' => 'Zásilkovna'];
    //$this->template->paymentMethods= ['1' => 'platba převodem (zdarma)','2' => 'platba na dobírku (49Kč)'];
    $this->template->prodPrice=$this->cart->getPrice();
    
    $this->template->paymentPrices=[];
    foreach($this->template->paymentMethods as $item) $this->template->paymentPrices[$item->id]=$item->price;
  }  
  
	public function renderProducts()
	{
		$this->template->title = 'Nákupní Košík'.TITLE_SUFFIX;
    $this->template->showFAQ = false;
    $this->template->showInstaBlock = false;
    $this->template->showReviews = false;
  
    //pro získání českých názvů rámečků
    $this->template->frameTypes=["none"=>"bez rámu","black"=>"dřevěný - černý","white"=>"dřevěný - bílý","aluminium"=>"hliníkový"];
  }  
  
    protected function createComponentSendOrderForm()
    {
        $form = new UI\Form;
        
        $form->addText('name', 'Jméno:')->setRequired("Jméno musí být zadáno!")->setAttribute("required","");
        $form->addText('email', 'Email')->setType('email')->setRequired("Email musí být zadán!")->setAttribute("required","");
        $form->addText('phone', 'Telefon:')->setRequired("Telefon musí být zadán!")->setAttribute("required","");
        $form->addText('street', 'Ulice:')->setRequired("Ulice musí být zadána!")->setAttribute("required","");
        $form->addText('city', 'Město:')->setRequired("Město musí být zadáno!")->setAttribute("required","");
        $form->addText('zipcode', 'PSČ:')->setRequired("PSČ musí být zadáno!")->setAttribute("required","");
        $form->addText('state', 'Stát:')->setRequired("Stát musí být zadán!")->setAttribute("required","");
        
        
        $form->addHidden('delivery','Doprava');
        $form->addHidden('payment','Platba');
        //$form->addRadioList('delivery', 'Doprava:', $this->template->deliveryMethods)->setRequired(true);
        //$form->addRadioList('payment', 'Platba:',$this->template->paymentMethods)->setRequired(true);
        
        $form->addSubmit('send', 'Dokončit objednávku')->setAttribute("class","orderButton");
        $form->onSuccess[] = [$this, 'sendOrderFormSucceeded'];
        $form->setDefaults(['state'=>'Česká republika','delivery'=>1,'payment'=>1]);
        
        return $form;
    }

    // volá se po úspěšném odeslání formuláře
    public function SendOrderFormSucceeded(UI\Form $form, \stdClass $values)
    {
        $paymentPrice=$this->db->table("payment")->get($values->payment)->price;
        
        $values->price=$this->cart->getPrice()+$paymentPrice;

        $order = $this->db->table('orders')->insert($values);
        $this->cart->saveProductsToDB($this->db, $order->id);
        
        $this->cart->reset();
         
        $this->flashMessage('Objednávka vytvořena');
        $this->redirect('Homepage:');
    }
    
  public function actionRemoveProduct($cartKey)
	{
    $this->cart->removeProduct($cartKey);
    
    $this->flashMessage('Produkt byl odstraněn z košíku');
    $this->redirect(':products');
	}
}
