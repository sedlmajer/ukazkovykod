<?php

namespace App\Presenters;
use Nette\Utils\Json;
use Nette\Utils\DateTime;


final class PrintPresenter extends BasePresenter
{
static function float2gps($float) {
    $x = abs($float);
    $deg = floor($x);
    $min_sec = ($x - $deg) * 60;
    $min = floor($min_sec);
    $sec = ($min_sec - $min) * 60;
    return  $deg . "°$min'" . number_format($sec, 3) . '"';
}

	public function renderStars($id)
	{
    if($this->user->isLoggedIn())
    {
      $product=$this->db->table('products')->get($id);
      if($product)
      {
		    $this->template->title = 'Tisk';
        
        $this->template->data = Json::decode($product->data);
        $format=$this->template->data->format;
        
        $this->template->resolutionScale=2;
        
        if($format=='60x90' || $format=='90x60') {
          $format=$this->template->data->format='60x90';
          $this->template->docWidth = 600+6;
          $this->template->docHeight = 900+6;
          $this->template->starsWidth = 527;
          $this->template->starsTop = ($this->template->docWidth-$this->template->starsWidth)/2+12.5;      
          $this->template->constellationwidth=3*$this->template->resolutionScale;
          $this->template->scalestars=4*$this->template->resolutionScale;
        }
        elseif($format=='50x70')
        {
          $this->template->docWidth = 500+6;
          $this->template->docHeight = 700+6;
          $this->template->starsWidth = 440;
          $this->template->starsTop = ($this->template->docWidth-$this->template->starsWidth)/2+10;
          $this->template->constellationwidth=3*$this->template->resolutionScale;
          $this->template->scalestars=3*$this->template->resolutionScale;
        }
        else
        {
          $this->template->docWidth = 300+6;
          $this->template->docHeight = 400+6;
          $this->template->starsWidth = 267;
          $this->template->starsTop = ($this->template->docWidth-$this->template->starsWidth)/2+6;
          $this->template->constellationwidth=2*$this->template->resolutionScale;
          $this->template->scalestars=2.5*$this->template->resolutionScale;
        }
        
        if($this->template->data->type!="circle") //pokud tam není kruh, ale nějaký obrázek, je potřeba tu plochu co nejvíc zvětšit,proto se tenkruh natáhne až do stran 
        {
           $this->template->starsTop = 5;
           $this->template->starsWidth = $this->template->docWidth;
        }
        
        
        $this->template->pdfFileName = $product->orders_id."-stars-pink-".$format.".pdf";
        
        $this->template->gps= self::float2gps($this->template->data->latitude).'N, '.self::float2gps($this->template->data->longitude).'E';
        $datetime = DateTime::from($this->template->data->date.' '.$this->template->data->time);
        
        if($this->template->data->showTime)
          $this->template->date= $datetime->format("j. n. Y G:i");
        else 
          $this->template->date= $datetime->format("j. n. Y ");
          
        $datetime->setTimezone(new \DateTimeZone("UTC"));
        $this->template->dateF=$datetime->format("Y-m-d H:i:s e");
        
	    }                       
      
      else $this->error('Stránku se nepodařilo načíst - id produktu neexistuje',404);
    }
     else  $this->redirect('Admin:login');
  }
}
