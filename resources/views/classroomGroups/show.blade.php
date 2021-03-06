<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            <a href="{{ route('subjects.index') }}">{{ __('Asignaturas') }}</a> > {{$subject->name}}
        </h2>
    </x-slot>

    <div class="max-w-3xl mx-auto py-6 sm:px-6 lg:px-8 grid">
        <div class="flex flex-col overflow-x-auto -my-2 sm:-mx-6 lg:-mx-8">
            <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                <div class="py-2 align-middle inline-block min-w-full"> 
                    <div class="shadow sm:rounded-md sm:overflow-hidden">
                        <div class="px-4 py-5 bg-white space-y-6 sm:p-6">
                            <x-subject-component :subject="$subject" :departments="$departments"  />
                        </div>
                        <div class="px-4 py-3 bg-gray-50 text-right sm:px-6">
                            <x-abutton href="{{ route('subjects.edit', $subject->id)}}">{{ __('Editar') }}</x-abutton>
                            <form action="{{ route('subjects.destroy', $subject->id)}}" method="post" class="inline">
                            @method('DELETE')
                            @csrf
                            <x-button>{{ __('Borrar') }}</x-button>
                            </form>
                            <x-abutton href="{{ route('subjects.index')}}">{{ __('Volver') }}</x-abutton>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>