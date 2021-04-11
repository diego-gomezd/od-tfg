<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Ofertas docentes') }}
        </h2>
    </x-slot>

    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8 grid">
        <div class="flex flex-col overflow-x-auto -my-2 sm:-mx-6 lg:-mx-8">
            <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                <div class="py-2 align-middle inline-block min-w-full">
                    <div class="float-right">
                        <x-abutton href="{{ route('subjects.create')}}">{{ __('Crear Oferta docente') }}</x-abutton>
                    </div>
                </div>
                <div class="py-2 align-middle inline-block min-w-full">
                    <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                        <div class="bg-gray-50 px-6 py-3 text-left text-md font-bold text-gray-600 uppercase tracking-wider">Filtrar</div>
                            <form method="post" action="{{ route('curriculumAcademicYears.filter') }}" >
                                @csrf
                                <div class="bg-white overflow-hidden border-b border-gray-200 sm:rounded-lg grid grid-cols-3 gap-4">
                                    <div class="px-6 py-3 text-left text-md">
                                        <div class="mt-1 flex rounded-md shadow-sm">
                                            <x-select id="filter_academic_year_id" name="filter_academic_year_id">
                                                <option value="" >Selecciona un año</option>
                                                @foreach ($academic_years as $year)
                                                <option value="{{ $year->id }}" {{ ($year->id == $filter_academic_year_id) ? 'selected' : '' }} > {{ $year->name }}</option>
                                                @endforeach  
                                            </x-select>
                                        </div>
                                    </div>
                                    <div class="px-6 py-3 text-left text-md">
                                        <div class="mt-1 flex rounded-md shadow-sm">
                                            <x-select id="filter_curriculum_id" name="filter_curriculum_id">
                                                <option value="" >Selecciona un Plan de Estudios</option>
                                                @foreach ($curriculums as $curriculum)
                                                <option value="{{ $curriculum->id }}" {{ ($curriculum->id == $filter_curriculum_id) ? 'selected' : '' }} > {{ $curriculum->name }}</option>
                                                @endforeach  
                                            </x-select>
                                        </div>
                                    </div>
                                <div class="px-6 py-3 text-right text-md">
                                    <x-button>{{ __('Filtrar') }}</x-button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-md font-bold text-gray-600 uppercase tracking-wider">Año</th>
                                <th scope="col" class="px-6 py-3 text-left text-md font-bold text-gray-600 uppercase tracking-wider">Plan de estudios</th>
                                <th scope="col" class="px-6 py-3 text-left text-md font-bold text-gray-600 uppercase tracking-wider">Número de asignaturas</th>
                                <th scope="col" class="px-6 py-3 text-left text-md font-bold text-gray-600 uppercase tracking-wider">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach ($curriculumAcademicYears as $curriculum)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{$curriculum->academic_year_name}}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <a href="{{route('curriculumSubjects.index', ['academic_year_id' => $curriculum->academic_year_id, 'curriculum_id' => $curriculum->curriculum_id])}}" 
                                    class="text-sm font-medium text-gray-900 hover:text-indigo-600">{{$curriculum->curriculum_name}}</a>
                                    <div class="text-sm font-medium text-gray-500">Código: {{$curriculum->curriculum_code}}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">{{$curriculum->number_subjects}}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <a href="#" class="text-indigo-600 hover:text-indigo-900">Exportar Oferta Docente</a><br/>
                                    <a href="#" class="text-indigo-600 hover:text-indigo-900">Duplicar</a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                    {{$curriculumAcademicYears->links()}}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>