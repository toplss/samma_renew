<?php

namespace App\View\Components\Mall;

use Illuminate\View\Component;

class ItemPrice extends Component
{
    public $row;
    public $activeMember;
    public $min_cart_ct_qty;


    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($row, $member, $qty)
    {
        $this->row = $row;
        $this->activeMember = $member;
        $this->min_cart_ct_qty = $qty;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|string
     */
    public function render()
    {
        return view('components.mall.item-price');
    }
}
