<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            <a href="{{ route('curriculumAcademicYears.index') }}">{{ __('Oferta Docente') }}</a> >
            <a href="{{ route('curriculumAcademicYears.index', ['filter_academic_year_id' => $curriculumSubject->academicYear->id]) }}">{{ $curriculumSubject->academicYear->name}}</a> >
            <a href="{{ route('curriculumSubjects.index', ['academic_year_id' => $curriculumSubject->academicYear->id, 'curriculum_id' => $curriculumSubject->curriculum->id]) }}">{{ $curriculumSubject->curriculum->name}}</a> >
            {{$curriculumSubject->subject->name}}
        </h2>
    </x-slot>

    <form method="post" action="{{ route('curriculumClassroomGroups.update', $curriculumSubject->id) }}">
        @csrf

        <div class="max-w-8xl mx-auto py-6 sm:px-6 lg:px-8 grid">
            <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-center text-md font-bold text-gray-600 uppercase tracking-wider">Ofertado</th>
                            <th scope="col" class="px-6 py-3 text-left text-md font-bold text-gray-600 uppercase tracking-wider">Nombre</th>
                            <th scope="col" class="px-6 py-3 text-left text-md font-bold text-gray-600 uppercase tracking-wider">ID Actividad</th>
                            <th scope="col" class="px-6 py-3 text-left text-md font-bold text-gray-600 uppercase tracking-wider">Grupo</th>
                            <th scope="col" class="px-6 py-3 text-left text-md font-bold text-gray-600 uppercase tracking-wider">Ubicación</th>
                            <th scope="col" class="px-6 py-3 text-left text-md font-bold text-gray-600 uppercase tracking-wider">Cuatrimestre</th>
                            <th scope="col" class="px-6 py-3 text-center text-md font-bold text-gray-600 uppercase tracking-wider">Plazas disponibles</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach ($classroomGroups as $classroomgroup)
                        <tr>
                            <td class="px-6 text-center py-4 whitespace-nowrap">
                                <input type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" name="classroomgroups[{{$classroomgroup->id}}]" {{$classroomgroup->offered == true ? 'checked' : ''}} />
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{Str::limit($classroomgroup->name, 50)}}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{$classroomgroup->activity_id}}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{$classroomgroup->activity_group}}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{$classroomgroup->location}}</div>
                                </th>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{$classroomgroup->durationTitle()}}</div>
                                </th>

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

                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="px-4 py-3 text-right sm:px-6">
                <x-button>{{ __('Actualizar lista') }}</x-buton>
            </div>
        </div>
    </form>
</x-app-layout>