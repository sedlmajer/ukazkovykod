<?php

namespace App\Presenters;


final class ProductPresenter extends BasePresenter
{
	public function renderStars()
	{
		$this->template->title = 'Darujte víc než dárek';
    $this->template->showFAQ = true;
	}
}
