<?php

namespace App\View\Components;

use Illuminate\View\Component;

class CurriculumComponent extends Component
{
    public $curriculum;

    public function __construct($curriculum)
    {
        $this->curriculum = $curriculum;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.curriculum-component');
    }
}
