@section('title', 'Grupos '.$academic_year->name)
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Grupos') }} {{ $academic_year->name }}
        </h2>
    </x-slot>

    <div class="max-w-8xl mx-auto py-6 sm:px-6 lg:px-8 grid">
        <div class="flex flex-col overflow-x-auto -my-2 sm:-mx-6 lg:-mx-8">
            <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                <div class="py-2 align-middle inline-block min-w-full">
                    <div class="float-right">
                        <x-abutton href="{{ route('classroomGroups.create', ['academic_year_id' => $academic_year->id])}}">{{ __('Crear Grupo').' ('.$academic_year->name.')' }}</x-abutton>
                    </div>
                </div>
                <div class="py-2 align-middle inline-block min-w-full">
                    <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                        <div class="bg-gray-50 px-6 py-3 text-left text-md font-bold text-gray-600 uppercase tracking-wider">Filtrar</div>
                        <form method="post" action="{{ route('classroomGroups.filter') }}">
                            @csrf
                            <div class="bg-white overflow-hidden border-b border-gray-200 sm:rounded-lg grid grid-cols-5">
                                <div class="px-6 py-3 text-left text-md">
                                    <div class="mt-1 flex rounded-md shadow-sm">
                                        <x-select id="filter_academic_year_id" name="filter_academic_year_id">
                                            @foreach ($academic_years as $option)
                                            <option value="{{ $option->id }}" {{ ($option->id == $filter_academic_year_id) ? 'selected' : '' }}> {{ $option->name }}</option>
                                            @endforeach
                                        </x-select>
                                    </div>
                                </div>
                                <div class="px-6 py-3 text-left text-md">
                                    <div class="mt-1 flex rounded-md shadow-sm">
                                        <x-select id="filter_subject_id" name="filter_subject_id">
                                            <option value="">Seleccione una asignatura</option>
                                            @foreach ($subjects as $option)
                                            <option value="{{ $option->id }}" {{ ($option->id == $filter_subject_id) ? 'selected' : '' }}> {{ $option->code.' - '.$option->name }}</option>
                                            @endforeach
                                        </x-select>
                                    </div>
                                </div>
                                <div class="px-6 py-3 text-left text-md">
                                    <div class="mt-1 flex rounded-md shadow-sm">
                                        <x-select id="filter_location" name="filter_location">
                                            <option value="">Seleccione una ubicación</option>
                                            @foreach ($locations as $option)
                                            <option value="{{ $option['location'] }}" {{ ($option['location'] == $filter_location) ? 'selected' : '' }}> {{ $option['location'] }}</option>
                                            @endforeach
                                        </x-select>
                                    </div>
                                </div>
                                <div class="px-6 py-3 text-left text-md">
                                    <div class="mt-1 flex rounded-md shadow-sm">
                                        <x-combo-component name="duration" :options="$durations" :selectedValue="$filter_duration">
                                            <option value="">Seleccione un cuatrimestre</option>
                                        </x-combo-component>
                                    </div>
                                </div>
                                <div class="px-6 py-3 text-right text-md">
                                    <x-button id="filterSubjectsBtn">{{ __('Filtrar') }}</x-button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-md font-bold text-gray-600 uppercase tracking-wider">Asignatura</th>
                                <th scope="col" class="px-6 py-3 text-left text-md font-bold text-gray-600 uppercase tracking-wider">Ubicación</th>
                                <th scope="col" class="px-6 py-3 text-left text-md font-bold text-gray-600 uppercase tracking-wider">Cuatrimestre</th>
                                <th scope="col" class="px-6 py-3 text-left text-md font-bold text-gray-600 uppercase tracking-wider">Grupo</th>
                                <th scope="col" class="px-6 py-3 text-center text-md font-bold text-gray-600 uppercase tracking-wider">Plazas disponibles</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach ($groups as $classroomgroup)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{Str::limit($classroomgroup->subject->name, 50)}}</div>
                                    <div class="text-sm font-medium text-gray-500">Código: {{$classroomgroup->subject->code}}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{$classroomgroup->location}}</div>
                                    </th>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{$classroomgroup->durationTitle()}}</div>
                                    </th>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{Str::limit($classroomgroup->name, 50)}}</div>
                                    <div class="text-sm font-medium text-gray-500">Grupo: {{$classroomgroup->activity_group}}</div>
                                </td>

                                <td class="px-6 py-4 text-center whitespace-nowrap">
                                    @if ($classroomgroup->capacity != null && $classroomgroup->capacity > 0)

                                    @if ($classroomgroup->isCapacityRemainingMoreThan(50))
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">

                                        @elseif ($classroomgroup->isCapacityRemainingMoreThan(10))
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">

                                            @else
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">

                                                @endif
                                                {{$classroomgroup->capacity_left}} / {{$classroomgroup->capacity}} </span>
                                            @endif
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap">
                                    <x-abutton href="{{ route('classroomGroups.edit', $classroomgroup->id)}}" class="my-1">{{ __('Editar') }}</x-abutton><br />
                                    <form action="{{ route('classroomGroups.destroy', $classroomgroup->id)}}" method="post" class="inline">
                                        @method('DELETE')
                                        @csrf
                                        <x-button>{{ __('Borrar') }}</x-button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                        {{$groups->links()}}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>