<div class="col-span-3 sm:col-span-2">
    <x-label for="code" :value="__('Código')"/>
    <div class="mt-1 flex rounded-md shadow-sm">
        <x-input id="code" type="text" name="code" :value="old('code')" value="{{$subject->code}}" required />
    </div>
</div>
<div class="col-span-3 sm:col-span-2">
    <x-label for="name" :value="__('Nombre')"/>
    <div class="mt-1 flex rounded-md shadow-sm">
        <x-input id="name" type="text" name="name" :value="old('name')" value="{{$subject->name}}" required />
    </div>
</div>
<div class="col-span-3 sm:col-span-2">
    <x-label for="department_id" :value="__('Departamento')"/>
    <div class="mt-1 flex rounded-md shadow-sm">
        <x-select id="department_id" name="department_id" required>
        @foreach ($departments as $department)
            <option value="{{ $department->id }}" {{ ($department->id == $subject->department_id) ? 'selected' : '' }}> 
            {{ $department->name }}
            </option>
        @endforeach  
        </x-select>
    </div>
</div>
<div class="col-span-3 sm:col-span-2">
    <x-label for="english_name" :value="__('Nombre en inglés')"/>
    <div class="mt-1 flex rounded-md shadow-sm">
        <x-input id="english_name" type="text" name="english_name" :value="old('english_name')" value="{{$subject->english_name}}" />
    </div>
</div>
<div class="col-span-3 sm:col-span-2">
    <x-label for="ects" :value="__('ECTS')"/>
    <div class="mt-1 flex rounded-md shadow-sm">
        <x-input id="ects" type="text" name="ects" :value="old('ects')" value="{{$subject->ects}}" required />
    </div>
</div>    
<div class="col-span-3 sm:col-span-2">
    <x-label for="comments" :value="__('Comentarios')"/>
    <div class="mt-1 flex rounded-md shadow-sm">
        <x-textarea id="comments" name="comments">
        {{$subject->comments}}
        </x-textarea>
    </div>
</div>