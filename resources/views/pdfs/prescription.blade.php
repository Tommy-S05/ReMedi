<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>{{ __('Prescription') }} - {{ $prescription->title ?? "#{$prescription->id}" }}</title>
        @vite('resources/css/app.css')
        <style>
            /* Puedes añadir CSS personalizado aquí si es necesario */
            @layer base {
                h1, h2, h3 {
                    font-weight: 600;
                }
            }
            /* Para asegurar que el fondo sea blanco y el texto negro al imprimir */
            body {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
                background-color: #ffffff !important;
                color: #000000 !important;
            }
        </style>
    </head>
    <body class="font-sans p-8">

        <div class="w-full max-w-4xl mx-auto">
            {{-- Encabezado de la Receta --}}
            <header class="flex justify-between items-start pb-4 border-b-2 border-gray-800">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">{{ $prescription->user->name }}</h1>
                    <p class="text-gray-600">{{ __('Patient') }}</p>
                </div>
                <div class="text-right">
                    {{-- Aquí podrías poner el logo de ReMedi si lo tienes en una URL pública --}}
                    <h2 class="text-2xl font-bold text-primary">ReMedi</h2>
                    <p class="text-gray-600">{{ __('Medical Prescription') }}</p>
                </div>
            </header>

            {{-- Información del Doctor y Fecha --}}
            <section class="mt-6 grid grid-cols-2 gap-4">
                <div>
                    @if($prescription->doctor_name)
                        <p class="text-sm text-gray-500">{{ __('Doctor') }}</p>
                        <p class="font-semibold">{{ $prescription->doctor_name }}</p>
                    @endif
                </div>
                <div class="text-right">
                    <p class="text-sm text-gray-500">{{ __('Date') }}</p>
                    <p class="font-semibold">{{ $prescription->prescription_date_formatted }}</p>
                </div>
            </section>

            {{-- Título y Notas --}}
            <section class="mt-6">
                @if($prescription->title)
                    <h2 class="text-xl font-semibold border-b pb-2 mb-2">{{ $prescription->title }}</h2>
                @endif
                @if($prescription->notes)
                    <div class="p-3 bg-gray-50 rounded-lg text-sm">
                        <p><strong>{{ __('Notes') }}:</strong> {{ $prescription->notes }}</p>
                    </div>
                @endif
            </section>

            {{-- Símbolo de Receta "Rx" --}}
            <div class="my-6">
                <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                     xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M3.75 12h16.5m-16.5 3.75h16.5M3.75 19.5h16.5M5.625 4.5h12.75a1.875 1.875 0 010 3.75H5.625a1.875 1.875 0 010-3.75z"></path>
                </svg>
            </div>


            {{-- Tabla de Medicamentos --}}
            <section class="mt-4">
                <table class="w-full text-left">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="p-3 text-sm font-semibold uppercase text-gray-700">{{ __('Medication') }}</th>
                            <th class="p-3 text-sm font-semibold uppercase text-gray-700">{{ __('Dosage') }}</th>
                            <th class="p-3 text-sm font-semibold uppercase text-gray-700">{{ __('Quantity') }}</th>
                            <th class="p-3 text-sm font-semibold uppercase text-gray-700">{{ __('Instructions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($prescription->medications as $medication)
                            <tr class="border-b">
                                <td class="p-3 align-top">
                                    <p class="font-semibold">{{ $medication->name }}</p>
                                    <p class="text-xs text-gray-500">{{ __($medication->type?->label()) }}</p>
                                </td>
                                <td class="p-3 align-top">{{ $medication->pivot->dosage_on_prescription ?? '-' }}</td>
                                <td class="p-3 align-top">{{ $medication->pivot->quantity_prescribed ?? '-' }}</td>
                                <td class="p-3 align-top">{{ $medication->pivot->instructions_on_prescription ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4"
                                    class="p-3 text-center text-gray-500">{{ __('No medications assigned to this prescription.') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </section>

            {{-- Pie de Página (opcional) --}}
            <footer class="mt-12 pt-4 border-t text-center text-xs text-gray-500">
                <p>{{ __('This prescription was generated by ReMedi') }}.</p>
            </footer>
        </div>

    </body>
</html>
