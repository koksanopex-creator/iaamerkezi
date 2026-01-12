<ul role="list" class="-mb-8">
    @foreach($case->logs as $log)
        <li>
            <div class="relative pb-8">
                @if(!$loop->last)
                    <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"></span>
                @endif
                <div class="relative flex space-x-3">
                    <div>
                        <span class="h-8 w-8 rounded-full bg-indigo-500 flex items-center justify-center ring-8 ring-white">
                            <svg class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                        </span>
                    </div>
                    <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                        <div>
                            <p class="text-sm text-gray-500">
                                <span class="font-medium text-gray-900">{{ $log->islem }}</span>: {{ $log->detay }}
                            </p>
                        </div>
                        <div class="text-right text-sm whitespace-nowrap text-gray-500">
                            <time datetime="{{ $log->created_at }}">{{ $log->created_at->format('d.m.Y H:i') }}</time>
                            <br>
                            <span class="text-xs text-gray-400">{{ $log->user->name ?? 'Sistem' }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </li>
    @endforeach
</ul>