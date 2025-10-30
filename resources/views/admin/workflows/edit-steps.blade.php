<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Adımları Yönet: <span class="text-indigo-600">{{ $workflow->name }}</span>
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @livewire('admin.workflow-steps-manager', ['workflow' => $workflow])
        </div>
    </div>
</x-app-layout>