<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ClassroomGroup;
use App\Models\CurriculumSubject;
use App\Models\Combos\CreationType;
use App\Models\CurriculumClassroomGroup;
use App\Http\Requests\UpdateCurriculumClassroomGroupRequest;

class CurriculumClassroomGroupController extends Controller
{
    public function index(Request $request)
    {
        $curriculumSubject = CurriculumSubject::find($request->curriculum_subject_id);
        

        $classroomGroups = ClassroomGroup::where('subject_id', $curriculumSubject->subject_id)->where('academic_year_id', $curriculumSubject->academic_year_id)->get();

        foreach ($classroomGroups as $group) {
            $cs = CurriculumClassroomGroup::where('curriculum_subject_id', $curriculumSubject->id)->where('classroom_group_id', $group->id)->first();
            
            $group->offered = $cs != null ? true : false;
        }
        return view('curriculumClassroomGroups.index', [
            'classroomGroups' => $classroomGroups,
            'curriculumSubject' => $curriculumSubject,
        ]);
    }

    public function update(UpdateCurriculumClassroomGroupRequest $request)
    {
        $curriculumSubject = CurriculumSubject::find($request->curriculum_subject_id);

        CurriculumClassroomGroup::where('curriculum_subject_id', $curriculumSubject->id)->delete();

        if ($request->classroomgroups != null) {
            foreach ($request->classroomgroups as $classroom_group_id => $offered) {
                $curriculumClassroomGroup = CurriculumClassroomGroup::where('curriculum_subject_id', $curriculumSubject->id)->where('classroom_group_id', $classroom_group_id)->first();

                if ($offered == true && $curriculumClassroomGroup == null) {
                    $curriculumClassroomGroup = new CurriculumClassroomGroup();
                    $curriculumClassroomGroup->classroom_group_id = $classroom_group_id;
                    $curriculumClassroomGroup->curriculum_subject_id = $curriculumSubject->id;
                    $curriculumClassroomGroup->creation_type = CreationType::MANUAL;
                    $curriculumClassroomGroup->save();
                    
                } else if ($offered == false && $curriculumClassroomGroup != null) {
                    $curriculumClassroomGroup->delete();
                }
            }
        }

        return redirect()->route('curriculumClassroomGroups.index', [
            'curriculum_subject_id' => $request->curriculum_subject_id,
        ])->with('success', 'Lista de grupos actualizada');
    }
}