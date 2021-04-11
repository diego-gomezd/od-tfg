<div class="grid grid-cols-3 gap-6">
    <div class="col-span-3 sm:col-span-2">
        <x-label for="name" :value="__('Año academico')"/>
        <div class="mt-1 flex rounded-md shadow-sm">
            <x-input id="name" type="text" name="name" :value="old('name')" value="{{$academicYear->name}}" required autofocus />
        </div>
    </div>
</div>