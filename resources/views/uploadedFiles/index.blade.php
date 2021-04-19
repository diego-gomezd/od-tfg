@section('title', 'Importaciones')
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Importaciones') }}
        </h2>
    </x-slot>

    <div class="max-w-8xl mx-auto py-6 sm:px-6 lg:px-8 grid">
        <div class="flex flex-col overflow-x-auto -my-2 sm:-mx-6 lg:-mx-8">
            <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                    <div class="p-4 shadow overflow-hidden bg-gray-50 border-b-2">
                        <span class="text-lg font-bold text-gray-600 uppercase">Importar ficheros</span>
                    </div>
                    <form method="POST" action="{{route('uploadedFiles.store')}}" accept-charset="UTF-8" enctype="multipart/form-data">
                        @csrf
                        <div class="sm:overflow-hidden">
                            <div class="px-4 py-5 bg-white space-y-6 sm:p-6">
                                <div class="col-span-3 sm:col-span-2">
                                    <x-label for="excelFiles" :value="__('Ficheros')" />
                                    <input type="file" accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel" name="excelFiles[]" multiple required />
                                    <div class="flex justify-between items-center text-gray-400"> <span>Tipo de ficheros aceptados: .xls y .xslx</span></div>
                                </div>
                                <div class="col-span-3 sm:col-span-2">
                                    <x-label for="format" :value="__('Formato')" />
                                    <div class="mt-1 flex rounded-md shadow-sm">
                                        <x-select id="format" name="format">
                                            @foreach ($formats as $format => $text)
                                            <option value="{{ $format }}"> {{ $text }}</option>
                                            @endforeach
                                        </x-select>
                                    </div>
                                </div>
                                <div class="col-span-3 sm:col-span-2">
                                    <x-label for="academic_year_id" :value="__('Curso academico')" />
                                    <div class="mt-1 flex rounded-md shadow-sm">
                                        <x-select id="academic_year_id" name="academic_year_id">
                                            <option value="" selected></option>
                                            @foreach ($academic_years as $year)
                                            <option value="{{ $year->id }}"> {{ $year->name }}</option>
                                            @endforeach
                                        </x-select>
                                        <span></span>
                                    </div>
                                    <p class="mt-2 text-sm text-gray-500">Solo para los ficheros con formato 'Oferta de Transversales'</p>
                                </div>
                            </div>
                            <div class="px-4 py-3 bg-gray-50 text-right sm:px-6">
                                <x-button>{{ __('Importar ficheros') }}</x-buton>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-8xl mx-auto py-6 sm:px-6 lg:px-8 grid">
        <div class="flex flex-col overflow-x-auto -my-2 sm:-mx-6 lg:-mx-8">
            <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                    <table class="min-w-full divide-y divide-gray-200 table table-sm table-bordered" id="table_files">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-md font-bold text-gray-600 uppercase tracking-wider">Fichero</th>
                                <th scope="col" class="px-6 py-3 text-left text-md font-bold text-gray-600 uppercase tracking-wider">Estado</th>
                                <th scope="col" class="px-6 py-3 text-left text-md font-bold text-gray-600 uppercase tracking-wider">Fecha de importación</th>
                                <th scope="col" class="px-6 py-3 text-left text-md font-bold text-gray-600 uppercase tracking-wider">Acciones</th>
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
                                    @if ($file->uploaded_file_result == null || $file->uploaded_file_result->result_status == 'OK')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Procesado sin errores</span>
                                    @elseif ($file->uploaded_file_result->result_status == 'WARNING')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-orange-800">Procesado con incidencias</span>
                                    @elseif ($file->uploaded_file_result->result_status == 'ERROR')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Procesado con errores</span>
                                    @endif
                                    @elseif ($file->status == 'IN_PROGRESS')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">En progreso</span>
                                    @else
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">En cola</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{$file->created_at}}</div>
                                    @if ($file->updated_at != null)
                                    <div class="text-sm font-medium text-gray-500">Actualizado: {{$file->updated_at}}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if ($file->status == 'FINISHED')

                                    @if ($file->uploaded_file_result != null && $file->uploaded_file_result->result_status != 'OK')
                                    <x-abutton href="{{ route('uploadedFiles.show', ['uploadedFile' => $file->id])}}" class="my-1">{{ __('Ver resultados') }}</x-abutton>
                                    <br />
                                    <x-abutton href="{{ route('uploadedFiles.process', ['uploaded_file_id' => $file->id])}}" class="my-1">{{ __('Reprocesar') }}</x-abutton>
                                    @endif

                                    @elseif ($file->status == 'UPLOADED')
                                    <a href="#" class="text-indigo-600 hover:text-indigo-900">Borrar</a>
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
    </div>
</x-app-layout>