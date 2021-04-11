
<div class="col-span-3 sm:col-span-2">
    <x-label for="code" :value="__('Código')"/>
    <div class="mt-1 flex rounded-md shadow-sm">
        <x-input id="code" type="text" name="code" :value="$curriculum->code" required />
    </div>
</div>
<div class="col-span-3 sm:col-span-2">
    <x-label for="name" :value="__('Plan de Estudios')"/>
    <div class="mt-1 flex rounded-md shadow-sm">
        <x-input id="name" type="text" name="name" :value="$curriculum->name" required />
    </div>
</div>