<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Asignaturas') }}
        </h2>
    </x-slot>
   
    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8 grid">
        <div class="flex flex-col overflow-x-auto -my-2 sm:-mx-6 lg:-mx-8">
            <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                <div class="py-2 align-middle inline-block min-w-full">
                    <div class="float-right">
                        <x-abutton href="{{ route('subjects.create')}}">{{ __('Crear Asignatura') }}</x-abutton>
                    </div>
                </div>
                <div class="py-2 align-middle inline-block min-w-full">
                    <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                        <div class="bg-gray-50 px-6 py-3 text-left text-md font-bold text-gray-600 uppercase tracking-wider">Filtrar</div>
                            <form method="post" action="{{ route('subjects.filter') }}" >
                                @csrf
                                <div class="bg-white overflow-hidden border-b border-gray-200 sm:rounded-lg grid grid-cols-3 gap-4">
                                    <div class="px-6 py-3 text-left text-md">
                                        <div class="mt-1 flex rounded-md shadow-sm">
                                            <x-input id="filter_name" type="text" name="filter_name" placeholder="Nombre de asignatura" :value="$filter_subject_name" />
                                        </div>
                                    </div>
                                    <div class="px-6 py-3 text-left text-md">
                                        <div class="mt-1 flex rounded-md shadow-sm">
                                            <x-select id="filter_department_id" name="filter_department_id">
                                                <option value="" >Selecciona un departamento</option>
                                                @foreach ($departments as $department)
                                                <option value="{{ $department->id }}" {{ ($department->id == $filter_department_id) ? 'selected' : '' }} > {{ $department->name }}</option>
                                                @endforeach  
                                            </x-select>
                                        </div>
                                    </div>
                                <div class="px-6 py-3 text-right text-md">
                                    <x-button id="filterSubjectsBtn">{{ __('Filtrar') }}</x-button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-md font-bold text-gray-600 uppercase tracking-wider">Asignatura</th>
                                <th scope="col" class="px-6 py-3 text-left text-md font-bold text-gray-600 uppercase tracking-wider">Departamento</th>
                                <th scope="col" class="px-6 py-3 text-left text-md font-bold text-gray-600 uppercase tracking-wider">ECTS</th>
                                <th scope="col" class="px-6 py-3 text-left text-md font-bold text-gray-600 uppercase tracking-wider">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach ($subjects as $subject)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{Str::limit($subject->name, 20)}}</div>
                                    <div class="text-sm font-medium text-gray-500">Código: {{$subject->code}}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{$subject->department->name}}</div>
                                    <div class="text-sm font-medium text-gray-500">Código: {{$subject->department->code}}</div>
                                </th>
                                <td class="px-6 py-4 whitespace-nowrap"><div class="text-sm font-medium text-gray-900">{{$subject->ects}}</div></th>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <x-abutton href="{{ route('subjects.edit', $subject->id)}}">{{ __('Editar') }}</x-abutton>
                                    <form action="{{ route('subjects.destroy', $subject->id)}}" method="post" class="inline">
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
                    {{$subjects->links()}}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>