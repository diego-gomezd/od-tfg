<?php

namespace App\View\Components;

use Illuminate\View\Component;

class ClassroomGroupComponent extends Component
{
    public $classroomGroup;
    public $subjects;
    public $durations;
    public $sizeGroups;
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($classroomGroup, $subjects, $durations, $sizeGroups)
    {
        $this->classroomGroup = $classroomGroup;
        $this->subjects = $subjects;
        $this->durations = $durations;
        $this->sizeGroups = $sizeGroups;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.classroom-group-component');
    }
}
