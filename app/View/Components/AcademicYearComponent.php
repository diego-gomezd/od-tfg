<?php

namespace App\View\Components;

use Illuminate\View\Component;
use App\Models\AcademicYear;

class AcademicYearComponent extends Component
{
    public $academicYear;
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($academicYear)
    {
        $this->academicYear = $academicYear;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.academic-year');
    }
}
