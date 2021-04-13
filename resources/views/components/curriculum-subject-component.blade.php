<div class="col-span-3 sm:col-span-2">
    <x-label for="subject_id" :value="__('Asignatura')"/>
    <div class="mt-1 flex rounded-md shadow-sm">
        <x-select id="subject_id" name="subject_id" required>
            <option value=""></option>
            @foreach ($subjects as $subject)
            <option value="{{ $subject->id }}" {{ ($subject->id == $curriculumSubject->subject_id) ? 'selected' : '' }}> 
            {{ $subject->code.' - '.$subject->name }}
            </option>
            @endforeach  
        </x-select>
    </div>
</div>
<div class="col-span-3 sm:col-span-2">
    <x-label for="name" :value="__('Tipo')"/>
    <div class="mt-1 flex rounded-md shadow-sm">
        <x-select id="type" name="type" required>
            <option value=""></option>
            @foreach ($types as $type)
            <option value="{{ $type }}" {{ ($type == $curriculumSubject->type) ? 'selected' : '' }}> 
            {{ $type }}
             </option>
            @endforeach  
        </x-select>
    </div>
</div>
<div class="col-span-3 sm:col-span-2">
    <x-label for="name" :value="__('Cuatrimestre')"/>
    <div class="mt-1 flex rounded-md shadow-sm">
        <x-select id="duration" name="duration" required>
            <option value=""></option>
            @foreach ($durations as $duration)
            <option value="{{ $duration }}" {{ ($duration == $curriculumSubject->duration) ? 'selected' : '' }}> 
            {{ $duration }}
            </option>
            @endforeach  
        </x-select>
    </div>
</div>
<div class="col-span-3 sm:col-span-2">
    <x-label for="name" :value="__('Curso')"/>
    <div class="mt-1 flex rounded-md shadow-sm">
        <x-select id="course" name="course" required>
            <option value=""></option>
            @foreach ($courses as $course)
            <option value="{{ $course }}" {{ ($course == $curriculumSubject->course) ? 'selected' : '' }}> 
            {{ $course }}
            </option>
            @endforeach  
        </x-select>
    </div>
</div>