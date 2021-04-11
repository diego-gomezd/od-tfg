<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            <a href="{{ route('curriculumAcademicYears.index') }}">{{ __('Oferta Docente') }}</a> >
            <a href="{{ route('curriculumAcademicYears.index', ['filter_academic_year_id' => $academic_year->id]) }}">{{$academic_year->name}}</a> >
            {{$curriculum->name}}
        </h2>
    </x-slot>

    <div class="max-w-8xl mx-auto py-6 sm:px-6 lg:px-8 grid">
        <div class="flex flex-col overflow-x-auto -my-2 sm:-mx-6 lg:-mx-8">
            <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                <div class="py-2 align-middle inline-block min-w-full">
                    <div class="float-right">
                        <x-abutton href="{{ route('curriculumSubjects.create', ['academic_year_id' => $academic_year->id, 'curriculum_id' => $curriculum->id])}}">{{ __('Añadir Asignatura') }}</x-abutton>
                    </div>
                </div>
                <div class="py-2 align-middle inline-block min-w-full">
                    <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                        <div class="bg-gray-50 px-6 py-3 text-left text-md font-bold text-gray-600 uppercase tracking-wider">Filtrar</div>
                        <form method="post" action="{{ route('curriculumSubjects.filter', ['academic_year_id' => $academic_year->id, 'curriculum_id' => $curriculum->id]) }}" >
                            @csrf
                            <div class="bg-white overflow-hidden border-b border-gray-200 sm:rounded-lg grid grid-cols-8 gap-3">
                                <div class="px-6 py-3 text-left text-md col-span-2">
                                    <div class="mt-1 flex rounded-md shadow-sm">
                                        <x-input id="filter_name" type="text" name="filter_name" placeholder="Nombre de asignatura" :value="$filter_name" />
                                    </div>
                                </div>
                                <div class="px-6 py-3 text-left text-md col-span-2">
                                    <div class="mt-1 flex rounded-md shadow-sm">
                                        <x-select id="filter_department_id" name="filter_department_id">
                                            <option value="" >Departamento</option>
                                            @foreach ($departments as $department)
                                            <option value="{{ $department->id }}" {{ ($department->id == $filter_department_id) ? 'selected' : '' }} > {{ $department->name }}</option>
                                            @endforeach  
                                        </x-select>
                                    </div>
                                </div>
                                <div class="px-6 py-3 text-left text-md">
                                    <div class="mt-1 flex rounded-md shadow-sm">
                                        <x-select id="filter_course" name="filter_course">
                                            <option value="" >Curso</option>
                                            @foreach ($courses as $course)
                                            <option value="{{ $course }}" {{ ($course == $filter_course) ? 'selected' : '' }} > {{ $course }}</option>
                                            @endforeach  
                                        </x-select>
                                    </div>
                                </div>
                                <div class="px-6 py-3 text-left text-md">
                                    <div class="mt-1 flex rounded-md shadow-sm">
                                        <x-select id="filter_duration" name="filter_duration">
                                            <option value="" >Cuatrimestre</option>
                                            @foreach ($durations as $duration)
                                            <option value="{{ $duration }}" {{ ($duration == $filter_duration) ? 'selected' : '' }} > {{ $duration }}</option>
                                            @endforeach  
                                        </x-select>
                                    </div>
                                </div>
                                <div class="px-6 py-3 text-left text-md">
                                    <div class="mt-1 flex rounded-md shadow-sm">
                                        <x-select id="filter_type" name="filter_type">
                                            <option value="" >Tipo</option>
                                            @foreach ($types as $type)
                                            <option value="{{ $type }}" {{ ($type == $filter_type) ? 'selected' : '' }} > {{ $type }}</option>
                                            @endforeach  
                                        </x-select>
                                    </div>
                                </div>
                                <div class="px-6 py-3 text-right text-md">
                                    <x-button>{{ __('Filtrar') }}</x-button>
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
                                <th scope="col" class="px-6 py-3 text-left text-md font-bold text-gray-600 uppercase tracking-wider">Departamento</th>
                                <th scope="col" class="px-6 py-3 text-left text-md font-bold text-gray-600 uppercase tracking-wider">Curso</th>
                                <th scope="col" class="px-6 py-3 text-left text-md font-bold text-gray-600 uppercase tracking-wider">Cuatrimestre</th>
                                <th scope="col" class="px-6 py-3 text-left text-md font-bold text-gray-600 uppercase tracking-wider">Tipo</th>
                                <th scope="col" class="px-6 py-3 text-left text-md font-bold text-gray-600 uppercase tracking-wider"></th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach ($curriculumSubjects as $curriculumSubject)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{$curriculumSubject->subject->name}}</div>
                                    <div class="text-sm font-medium text-gray-500">Código: {{$curriculumSubject->subject->code}}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{$curriculumSubject->subject->department->name}}</div>
                                    <div class="text-sm font-medium text-gray-500">Código: {{$curriculumSubject->subject->department->code}}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{$curriculumSubject->course}}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{$curriculumSubject->duration}}</div>   
                                  </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                   <div class="text-sm font-medium text-gray-900">{{$curriculumSubject->type}}</div>   
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <a href="#" class="text-indigo-600 hover:text-indigo-900">Editar</a> / 
                                    <form action="{{ route('curriculumSubjects.destroy', $curriculumSubject->id)}}" method="post">
                                    @method('DELETE')
                                    @csrf
                                    <x-button>{{ __('Eliminar') }}</x-button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                    {{$curriculumSubjects->links()}}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>