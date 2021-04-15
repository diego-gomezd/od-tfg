<?php

namespace App\View\Components;

use Illuminate\View\Component;

class CurriculumSubjectComponent extends Component
{
    public $curriculumSubject;
    public $subjects;
    public $courses;
    public $durations;
    public $types;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($curriculumSubject, $subjects, $courses, $durations, $types)
    {
        $this->curriculumSubject = $curriculumSubject;
        $this->subjects = $subjects;
        $this->courses = $courses;
        $this->durations = $durations;
        $this->types = $types;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.curriculum-subject-component');
    }
}
