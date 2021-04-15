<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Ficheros subidos') }}
        </h2>
    </x-slot>

    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8 grid grid-cols-3 gap-4">
        <div class="flex flex-col overflow-x-auto -my-2 sm:-mx-6 lg:-mx-8 col-span-2">
            <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                    <table class="min-w-full divide-y divide-gray-200 table table-sm table-bordered" id="table_files">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-md font-bold text-gray-600 uppercase tracking-wider">Fichero</th>
                                <th scope="col" class="px-6 py-3 text-left text-md font-bold text-gray-600 uppercase tracking-wider">Estado</th>
                                <th scope="col" class="px-6 py-3 text-left text-md font-bold text-gray-600 uppercase tracking-wider">Fecha de importación</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach ($excelFiles as $file)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{$file->file_name}}</div>
                                    @if ($file->format != null)
                                    <div class="text-sm font-medium text-gray-500">Formato: {{$file->format}}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if ($file->status == 'FINISHED')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Procesado</span>
                                    @else
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-orange-800">Pendiente</span>
                                    
                                    <br/>
                                    <a href="#" class="text-indigo-600 hover:text-indigo-900">Reprocesar</a> / 
                                    <a href="#" class="text-indigo-600 hover:text-indigo-900">Borrar</a>
                                    
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{$file->created_at}}</div>
                                    @if ($file->updated_at != null)
                                    <div class="text-sm font-medium text-gray-500">Actualizado: {{$file->updated_at}}</div>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                    {{$excelFiles->links()}}
                    </div>
                </div>
            </div>
        </div>
        <div class="flex flex-col overflow-x-auto -my-2 sm:-mx-6 lg:-mx-8">
            <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                    <div class="p-4 shadow overflow-hidden bg-gray-50 border-b-2">
                        <span class="text-lg font-bold text-gray-600 uppercase">Subir documentos</span>
                    </div>
                    <div class="p-3 bg-white divide-y divide-gray-200">
                        <form method="POST" action="{{route('uploadedFiles.store')}}" accept-charset="UTF-8" enctype="multipart/form-data">
                            @csrf
                            <div class="mb-2">
                                <input type="file" accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel" 
                                        name="excelFiles[]" multiple required />
                                <div class="flex justify-between items-center text-gray-400"> <span>Tipo de ficheros aceptados: .xls y .xslx</span> <span class="flex items-center "></div>
                            </div>
                            <div class="mt-3 text-center pb-3"><x-button class="ml-3">{{ __('Subir ficheros') }}</x-button></div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>