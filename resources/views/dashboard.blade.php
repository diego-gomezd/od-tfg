<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>
    <form method="POST" action="{{route('uploadFiles')}}" accept-charset="UTF-8" enctype="multipart/form-data">
        @csrf
        <div class="py-20 h-screen px-2">
            <div class="max-w-md mx-auto bg-white shadow rounded-lg overflow-hidden md:max-w-lg">
                <div class="md:flex">
                    <div class="w-full">
                        <div class="p-4 border-b-2"> <span class="text-lg font-bold text-gray-600">Subir documentos</span> </div>
                        <div class="p-3">
                            <div class="mb-2">
                            <input type="file" accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel" 
                                    name="excelFiles[]" multiple required>
                                <div class="flex justify-between items-center text-gray-400"> <span>Tipo de ficheros aceptados: .xls y .xslx</span> <span class="flex items-center "></div>
                            </div>
                            <div class="mt-3 text-center pb-3">
                            
                                <x-button class="ml-3">{{ __('Subir ficheros') }}</x-button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>    
        </div>
    </form>
    
</x-app-layout>
