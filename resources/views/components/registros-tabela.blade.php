 <div class="overflow-x-auto shadow rounded-sm bg-white dark:bg-gray-800">
     <table id="table-mes" class="table-auto  border-collapse border border-gray-200 dark:border-gray-700">

         <thead>
             <tr class="bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200">
                 <th class="border border-gray-300 dark:border-gray-600 px-4 py-2 text-left">
                     Data
                 </th>
                 <th class="border border-gray-300 dark:border-gray-600 px-4 py-2 text-left">
                     Entrada
                 </th>
                 <th class="border border-gray-300 dark:border-gray-600 px-4 py-2 text-left">
                     Saida Almoço
                 </th>
                 <th class="border border-gray-300 dark:border-gray-600 px-4 py-2 text-left">
                     Retorno Almoço
                 </th>
                 <th class="border border-gray-300 dark:border-gray-600 px-4 py-2 text-left">
                     Saida final
                 </th>
             </tr>
         </thead>
         @foreach ($registros as $data => $registrosDia)
             <tr  class="bg-white dark:bg-gray-800 hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-800 dark:text-gray-200">
                 <td class="px-4 py-2">{{ \Carbon\Carbon::parse($data)->format('d/m/Y') }}</td>
                 <td class="px-4 py-2 ">
                     {{ $registrosDia->where('tipo', 'entrada_manha')->first()?->hora ?? '-' }}
                 </td>
                 <td class="px-4 py-2 ">
                     {{ $registrosDia->where('tipo', 'saida_almoco')->first()?->hora ?? '-' }}
                 </td>
                 <td class="px-4 py-2 ">
                     {{ $registrosDia->where('tipo', 'retorno_almoco')->first()?->hora ?? '-' }}
                 </td>
                 <td class="px-4 py-2 ">
                     {{ $registrosDia->where('tipo', 'saida_fim')->first()?->hora ?? '-' }}
                 </td>

             </tr>
         @endforeach
     </table>
 </div>
