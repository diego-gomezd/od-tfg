<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        <a href="{{ route('classroomGroups.index', ['filter_academic_year_id' => $academic_year->id]) }}">{{ __('Grupos') }} {{$academic_year->name}}</a> > Actualizar grupo
        </h2>
    </x-slot>

    <div class="max-w-3xl mx-auto py-6 sm:px-6 lg:px-8 grid">
        <div class="flex flex-col overflow-x-auto -my-2 sm:-mx-6 lg:-mx-8">
            <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                <div class="py-2 align-middle inline-block min-w-full">
                    <form method="post" action="{{ route('classroomGroups.update', $classroomGroup->id) }}">
                        @method('PUT')
                        @csrf
                        <div class="shadow sm:rounded-md sm:overflow-hidden">
                            <div class="px-4 py-5 bg-white space-y-6 sm:p-6">
                                <x-classroom-group-component :classroomGroup="$classroomGroup" :subjects="$subjects" :durations="$durations" :sizeGroups="$size_groups" disabled/>
                            </div>
                            <div class="px-4 py-3 bg-gray-50 text-right sm:px-6">
                            <x-button>{{ __('Actualizar') }}</x-buton>
                            <x-abutton href="{{ route('classroomGroups.index', ['filter_academic_year_id' => $academic_year->id])}}">{{ __('Cancelar') }}</x-abutton>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>