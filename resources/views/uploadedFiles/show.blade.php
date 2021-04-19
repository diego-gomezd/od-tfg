@section('title', 'Resultado fichero '.$uploadedFile->file_name)
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight"><a href="{{ route('uploadedFiles.index') }}">{{ __('Importaciones') }}</a> > {{$uploadedFile->file_name}}</h2>
    </x-slot>

    <div class="max-w-5xl mx-auto py-6 sm:px-6 lg:px-8 grid">
        <div class="flex flex-col overflow-x-auto -my-2 sm:-mx-6 lg:-mx-8">
            <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                <div class="py-2 align-middle inline-block min-w-full">
                    <div class="shadow sm:rounded-md sm:overflow-hidden">
                        <div class="px-4 py-5 bg-white space-y-6 sm:p-6">
                            <div class="col-span-3 sm:col-span-2">
                                <x-label for="code" :value="__('Fecha subida')" />
                                <div class="mt-1 flex rounded-md shadow-sm">
                                    <x-input :value="$uploadedFile->created_at" disabled />
                                </div>
                            </div>
                            <div class="col-span-3 sm:col-span-2">
                                <x-label for="code" :value="__('Fichero')" />
                                <div class="mt-1 flex rounded-md shadow-sm">
                                    <x-input :value="$uploadedFile->file_name" disabled />
                                </div>
                            </div>
                            <div class="col-span-3 sm:col-span-2">
                                <x-label for="name" :value="__('Estado')" />
                                <div class="mt-1 flex rounded-md shadow-sm">
                                    <x-input value="{{$uploadedFile->status == 'FINISHED' ? 'Procesado' : ($uploadedFile->status == 'IN_PROGRESS' ? 'En progreso' : 'En cola')}}" disabled />
                                </div>
                            </div>
                            @if ($uploadedFileResult->result_description != null && !empty($uploadedFileResult->result_description))
                            <div class="col-span-3 sm:col-span-2">
                                <x-label for="name" :value="__('Resultados')" />
                                <div class="mt-1 flex rounded-md shadow-sm">
                                    <table class="divide-y divide-gray-200 table table-sm table-bordered">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th scope="col" class="px-6 py-3 text-left text-md font-bold text-gray-600 uppercase tracking-wider">Gravedad</th>
                                                <th scope="col" class="px-6 py-3 text-left text-md font-bold text-gray-600 uppercase tracking-wider">Descrición</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                            @foreach ($uploadedFileResult->array_descriptions() as $description)
                                            <tr>
                                                <td class="px-6 py-4 whitespace-nowrap">{{$description['status']}}</td>
                                                <td class="px-6 py-4 whitespace-nowrap">{{$description['msg']}}</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            @endif
                        </div>
                        <div class="px-4 py-3 bg-gray-50 text-right sm:px-6">
                            @if ($uploadedFile->status == 'FINISHED' && $uploadedFileResult != null && $uploadedFileResult->result_status != 'OK')
                            <x-abutton href="{{ route('uploadedFiles.process', ['uploaded_file_id' => $uploadedFile->id])}}">{{ __('Reprocesar') }}</x-abutton>
                            @endif
                            <x-abutton href="{{ route('uploadedFiles.download', $uploadedFile->id) }}">{{ __('Descargar') }}</x-abutton>
                            <x-abutton href="{{ route('uploadedFiles.index') }}">{{ __('Volver') }}</x-abutton>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>