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
                                <div class="relative h-40 rounded-lg border-dashed border-2 border-gray-200 bg-white flex justify-center items-center hover:cursor-pointer">
                                    <div class="absolute">
                                        <div class="flex flex-col items-center "> <i class="fa fa-cloud-upload fa-3x text-gray-200"></i> <span class="block text-gray-400 font-normal">Arrastra los ficheros aquí</span> <span class="block text-gray-400 font-normal">or</span> <span class="block text-blue-400 font-normal">Explora archivos</span> </div>
                                    </div>
                                    <input type="file" accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel" multiple 
                                    name="excelFiles[]" required class="h-full w-full opacity-0" ondrop="dropHandler(event);">
                                </div>
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
