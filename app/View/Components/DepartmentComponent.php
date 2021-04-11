<?php

namespace App\View\Components;

use Illuminate\View\Component;

class DepartmentComponent extends Component
{

    public $department;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($department)
    {
        $this->department = $department;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.department-component');
    }
}
