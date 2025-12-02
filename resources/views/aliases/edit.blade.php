@extends('layouts.header')

@section('main')
<div class="p-8 bg-gray-100 min-h-screen">
    <div class="max-w-2xl mx-auto bg-white rounded-lg shadow p-8">
        <h1 class="text-2xl font-bold mb-6">Editar Alias</h1>
        <form method="POST" action="{{ route('api.aliases.update', ['uuid' => $alias['uuid']]) }}" id="edit-alias-form">
            @csrf
            @method('PUT')
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Nome *</label>
                <input type="text" name="name" value="{{ is_array($alias['name'] ?? null) ? implode(', ', $alias['name']) : ($alias['name'] ?? '') }}" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Tipo *</label>
                <select name="type" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                    @php $typeValue = $alias['type'] ?? ''; if (is_array($typeValue)) $typeValue = implode(', ', $typeValue); @endphp
                    <option value="host" @if($typeValue=='host') selected @endif>Host(s)</option>
                    <option value="network" @if($typeValue=='network') selected @endif>Network(s)</option>
                    <option value="port" @if($typeValue=='port') selected @endif>Port(s)</option>
                    <option value="url" @if($typeValue=='url') selected @endif>URL (IPs)</option>
                    <option value="geoip" @if($typeValue=='geoip') selected @endif>GeoIP</option>
                </select>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Conteúdo *</label>
                <textarea name="content" required rows="4" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">{{ is_array($alias['content'] ?? null) ? implode("\n", $alias['content']) : ($alias['content'] ?? '') }}</textarea>
                <p class="mt-1 text-sm text-gray-500">Insira IPs, redes ou portas (um por linha ou separados por vírgula)</p>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Descrição</label>
                <textarea name="description" rows="2" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">{{ is_array($alias['description'] ?? null) ? implode('\n', $alias['description']) : ($alias['description'] ?? ($alias['descr'] ?? '')) }}</textarea>
            </div>
            <div class="mb-6">
                <label class="inline-flex items-center cursor-pointer">
                    <input type="checkbox" name="enabled" value="1" @if(($alias['enabled'] ?? '1')=='1') checked @endif class="sr-only peer">
                    <div class="relative w-11 h-6 bg-gray-200 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-green-500"></div>
                    <span class="ml-3 text-sm font-medium text-gray-700">Ativo</span>
                </label>
            </div>
            <div class="flex justify-end gap-3">
                <a href="{{ route('aliases.index') }}" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300">Cancelar</a>
                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">Salvar Alterações</button>
            </div>
        </form>
        <script>
        document.getElementById('edit-alias-form').addEventListener('submit', async function(e) {
            e.preventDefault();
            const form = e.target;
            const formData = new FormData(form);
            let data = {};
            formData.forEach((value, key) => {
                // For checkbox
                if (key === 'enabled') {
                    data[key] = form.elements['enabled'].checked ? '1' : '0';
                } else {
                    data[key] = value;
                }
            });
            // Laravel expects _method for PUT
            data['_method'] = 'PUT';
            try {
                const response = await fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                        'Accept': 'application/json',
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: new URLSearchParams(data)
                });
                const result = await response.json();
                if (result.status === 'success') {
                    // Redirect to index with success message
                    sessionStorage.setItem('aliasEditSuccess', result.message || 'Alias atualizado com sucesso!');
                    window.location.href = "{{ route('aliases.index') }}";
                } else {
                    showNotification('error', result.message || 'Erro ao atualizar alias.');
                }
            } catch (err) {
                showNotification('error', 'Erro inesperado ao atualizar alias.');
            }
        });

        function showNotification(type, message) {
            const colors = {
                success: 'bg-green-100 border-green-400 text-green-700',
                error: 'bg-red-100 border-red-400 text-red-700',
                info: 'bg-blue-100 border-blue-400 text-blue-700'
            };

            const notification = document.createElement('div');
            notification.className = `fixed top-4 right-4 ${colors[type]} border px-4 py-3 rounded-lg shadow-lg z-50 max-w-md`;
            notification.innerHTML = `
                <div class="flex items-center">
                    <span class="mr-2">${message}</span>
                    <button onclick="this.parentElement.parentElement.remove()" class="ml-auto">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            `;

            document.body.appendChild(notification);

            setTimeout(() => {
                notification.remove();
            }, 5000);
        }
        </script>
    </div>
</div>
@endsection
