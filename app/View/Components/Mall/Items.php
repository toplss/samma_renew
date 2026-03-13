<?php

namespace App\View\Components\Mall;

use Illuminate\View\Component;

class Items extends Component
{
    public $row;
    public $activeMember;
    public $timpArr;

    public $it_id;
    public $sold_out;
    public $min_cart_ct_qty;
    

    public function __construct($row, $activeMember = null, $timpArr = [])
    {
        $this->row = $row;
        $this->activeMember = $activeMember;
        $this->timpArr = $timpArr;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|string
     */
    public function render()
    {
        return view('components.mall.items');
    }
}
