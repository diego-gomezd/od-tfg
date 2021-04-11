
<div class="col-span-3 sm:col-span-2">
    <x-label for="code" :value="__('Código')"/>
    <div class="mt-1 flex rounded-md shadow-sm">
        <x-input id="code" type="text" name="code" :value="old('code')" value="{{$department->code}}" required />
    </div>
</div>
<div class="col-span-3 sm:col-span-2">
    <x-label for="name" :value="__('Departamento')"/>
    <div class="mt-1 flex rounded-md shadow-sm">
        <x-input id="name" type="text" name="name" :value="old('name')" value="{{$department->name}}" required />
    </div>
</div>
