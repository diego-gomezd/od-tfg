<?php

namespace App\View\Components;

use Illuminate\View\Component;

class SubjectComponent extends Component
{
    public $subject;
    public $departments;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($subject, $departments)
    {
        $this->subject = $subject;
        $this->departments = $departments;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.subject-component');
    }
}
