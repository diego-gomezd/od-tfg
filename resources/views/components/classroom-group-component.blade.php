<div class="col-span-3 sm:col-span-2">
    <x-label for="subject_id" :value="__('Asignatura')"/>
    <div class="mt-1 flex rounded-md shadow-sm">
        <x-select id="subject_id" name="subject_id" required {{$attributes}}>
            <option value=""></option>
            @foreach ($subjects as $subject)
            <option value="{{ $subject->id }}" {{ ($subject->id == $classroomGroup->subject_id) ? 'selected' : '' }}> 
            {{ $subject->code.' - '.$subject->name }}
            </option>
            @endforeach  
        </x-select>
    </div>
</div>
<div class="col-span-3 sm:col-span-2">
    <x-label for="name" :value="__('Nombre')"/>
    <div class="mt-1 flex rounded-md shadow-sm">
        <x-input id="name" type="text" name="name" :value="$classroomGroup->name" required />
    </div>
</div>
<div class="col-span-3 sm:col-span-2">
    <x-label for="location" :value="__('Ubicación')"/>
    <div class="mt-1 flex rounded-md shadow-sm">
        <x-input id="location" type="text" name="location" :value="$classroomGroup->location" />
    </div>
</div>
<div class="col-span-3 sm:col-span-2">
    <x-label for="activity_id" :value="__('ID Actividad')"/>
    <div class="mt-1 flex rounded-md shadow-sm">
        <x-input id="activity_id" type="text" name="activity_id" :value="$classroomGroup->activity_id" required />
    </div>
</div>
<div class="col-span-3 sm:col-span-2">
    <x-label for="activity_id" :value="__('Grupo')"/>
    <div class="mt-1 flex rounded-md shadow-sm">
        <x-input id="activity_group" type="text" name="activity_group" :value="$classroomGroup->activity_group" required />
    </div>
</div>
<div class="col-span-3 sm:col-span-2">
    <x-label for="name" :value="__('Cuatrimestre')"/>
    <div class="mt-1 flex rounded-md shadow-sm">
        <x-combo-component name="duration" :options="$durations" :selectedValue="$classroomGroup->duration" required >
            <option value=""></option>
        </x-combo-component>
    </div>
</div>
<div class="col-span-3 sm:col-span-2">
    <x-label for="language" :value="__('Idioma')"/>
    <div class="mt-1 flex rounded-md shadow-sm">
        <x-input id="language" type="text" name="language" :value="$classroomGroup->language" />
    </div>
</div>

<div>
    <x-label for="capacity" :value="__('Capacidad')"/>
    <div class="mt-1 flex rounded-md shadow-sm">
        <x-input id="capacity" type="text" name="capacity" :value="$classroomGroup->capacity" />
    </div>
</div>

<div>
    <x-label for="capacity_left" :value="__('Capacidad restante')"/>
    <div class="mt-1 flex rounded-md shadow-sm">
        <x-input id="capacity_left" type="text" name="capacity_left" :value="$classroomGroup->capacity_left" />
    </div>
</div>
<div>
    <x-label for="small_group" :value="__('Grupo reducido?')"/>
    <div class="mt-1 flex rounded-md shadow-sm">
        <x-combo-component name="small_group" :options="$sizeGroups" :selectedValue="$classroomGroup->small_group" required >
        </x-combo-component>
        
    </div>
</div>
