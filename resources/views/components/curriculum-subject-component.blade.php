<div class="col-span-3 sm:col-span-2">
    <x-label for="subject_id" :value="__('Asignatura')"/>
    <div class="mt-1 flex rounded-md shadow-sm">
        <x-select id="subject_id" name="subject_id" required {{$attributes}}>
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
    <x-label for="type" :value="__('Tipo')"/>
    <div class="mt-1 flex rounded-md shadow-sm">
        <x-combo-component name="type" :options="$types" :selectedValue="$curriculumSubject->type" required >
            <option value=""></option>
        </x-combo-component>
    </div>
</div>
<div class="col-span-3 sm:col-span-2">
    <x-label for="name" :value="__('Cuatrimestre')"/>
    <div class="mt-1 flex rounded-md shadow-sm">
        <x-combo-component name="duration" :options="$durations" :selectedValue="$curriculumSubject->duration" required >
            <option value=""></option>
        </x-combo-component>
    </div>
</div>
<div class="col-span-3 sm:col-span-2">
    <x-label for="name" :value="__('Curso')"/>
    <div class="mt-1 flex rounded-md shadow-sm">
        <x-combo-component name="course" :options="$courses" :selectedValue="$curriculumSubject->course" required>
            <option value=""></option>
        </x-combo-component>
    </div>
</div>