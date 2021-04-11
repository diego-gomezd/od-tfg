<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Departamentos') }}
        </h2>
    </x-slot>
   
    <div class="max-w-3xl mx-auto py-6 sm:px-6 lg:px-8 grid">
        <div class="flex flex-col overflow-x-auto -my-2 sm:-mx-6 lg:-mx-8">
            <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                <div class="py-2 align-middle inline-block min-w-full">
                    <div class="float-right">
                        <x-abutton href="{{ route('departments.create')}}">{{ __('Crear Departamento') }}</x-abutton>
                    </div>
                </div>
                <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-md font-bold text-gray-600 uppercase tracking-wider">Departamento</th>
                                <th scope="col" class="px-6 py-3 text-left text-md font-bold text-gray-600 uppercase tracking-wider">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach ($departments as $department)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{$department->name}}</div>
                                    <div class="text-sm font-medium text-gray-500">Código: {{$department->code}}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <x-abutton href="{{ route('departments.edit', $department->id)}}">{{ __('Editar') }}</x-abutton>
                                    <form action="{{ route('departments.destroy', $department->id)}}" method="post" class="inline">
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
                    {{$departments->links()}}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>