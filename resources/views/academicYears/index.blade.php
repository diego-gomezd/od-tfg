@section('title', 'Cursos academicos')
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Cursos academicos') }}
        </h2>
    </x-slot>

    <div class="max-w-3xl mx-auto py-6 sm:px-6 lg:px-8 grid">
        <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
            <div class="py-2 align-middle inline-block min-w-full">
                <div class="float-right">
                    <x-abutton href="{{ route('academicYears.create')}}">{{ __('Crear Curso Académico') }}</x-abutton>
                </div>
            </div>
            <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-center text-md font-bold text-gray-600 uppercase tracking-wider">Año</th>
                            <th scope="col" class="px-6 py-3 text-center text-md font-bold text-gray-600 uppercase tracking-wider">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach ($academicYears as $academicYear)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <div class="text-sm font-medium text-gray-900">{{$academicYear->name}}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <x-abutton href="{{ route('academicYears.edit', $academicYear->id)}}">{{ __('Editar') }}</x-abutton>
                                <form action="{{ route('academicYears.destroy', $academicYear->id)}}" method="post" class="inline">
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
                    {{$academicYears->links()}}
                </div>
            </div>
        </div>

    </div>
</x-app-layout>