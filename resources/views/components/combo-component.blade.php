<x-select {{$attributes}}>
    {{ $slot }}
    @foreach ($options as $option)
    <option value="{{ $option['id'] }}" {{ ($option['id'] == $selectedValue) ? 'selected' : '' }} >{{ $option['title'] }}</option>
    @endforeach  
</x-select>