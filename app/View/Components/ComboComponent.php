<?php

namespace App\View\Components;

use Illuminate\View\Component;

class ComboComponent extends Component
{
    public $options;
    public $selectedValue;


    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($options, $selectedValue)
    {
        $this->options = $options;
        $this->selectedValue = $selectedValue;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.combo-component');
    }
}
