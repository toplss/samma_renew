<?php

namespace App\View\Components\Main;

use Illuminate\View\Component;

class EventItems extends Component
{
    public $row;
    public $activeMember;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($row, $member = null)
    {
        //
        $this->row = $row;
        $this->activeMember = $member;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|string
     */
    public function render()
    {
        return view('components.main.event-items');
    }
}
