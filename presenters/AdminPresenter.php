<?php

namespace App\Presenters;
use Nette;
use Nette\Application\UI;
use Nette\Utils\Json;
use Nette\Database\Context;
use App\Model\Product;

final class AdminPresenter extends BasePresenter
{
  private $editProduct;
  
  public function renderLogin()
  {
    if($this->user->isLoggedIn())
        $this->redirect(':default');
  }
  
  public function renderDefault()
  {
     if($this->user->isLoggedIn())
     {
         $this->template->orders = $this->db->table('orders')->order('date DESC');
         
         $delivery=$this->db->table("delivery");
         $this->template->deliveryMethods=[];
         foreach($delivery as $item) $this->template->deliveryMethods[$item->id]=$item->name;
         
         $payment=$this->db->table("payment");
         $this->template->paymentMethods=[];
         foreach($payment as $item) $this->template->paymentMethods[$item->id]=$item->name;
         
         $this->template->stateColors=[0=>"#ffe0b2",1=>"#c8e6c9",2=>"#ffcdd2"];
     }
     else  $this->redirect('Admin:login');
     
  }
  
    protected function createComponentOrderForm()
  {
    $form = new UI\Form;
    $form->addSubmit('send', 'Uložit');
        
		$form->addSubmit('send2', 'Uložit');

		$form->onSuccess[] = function (UI\Form $form, $values)  {
       \Tracy\Debugger::barDump($values);
			 //$values = $form->getHttpData($form::DATA_TEXT, 'sel[]');
       $orders = $this->db->table('orders')->order('date DESC');
       foreach($orders as $order)
       {
          $order_state = $form->getHttpData($form::DATA_LINE, 'order_state'.$order->id);

          $note = $form->getHttpData($form::DATA_TEXT, 'note'.$order->id);

          $order->update(["order_state"=>$order_state,"note"=>$note]);
       }
    };
    
    return $form;
  }
  
  
  
  protected function createComponentLoginForm()
  {
    $form = new UI\Form;
        
		$form->addText('username', 'Jméno:')
			->setRequired('Prosím, zadejte své jméno.');

		$form->addPassword('password', 'Heslo:')
			->setRequired('Prosím, zadejte své heslo.');

		$form->addSubmit('send', 'Přihlásit');

		$form->onSuccess[] = function (UI\Form $form, $values)  {
			try {
				$this->user->login($values->username, $values->password);
        
			} catch (Nette\Security\AuthenticationException $e) {
				$form->addError('Přihlašovací jméno nebo heslo není správné.');
				return;
			}
    };
    
    return $form;
  }
  

  
  public function actionOut()
	{
		$this->getUser()->logout();
    $this->redirect(':login');
	}
}
