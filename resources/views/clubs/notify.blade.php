<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Send Update to Members: ') }} {{ $club->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                
                <!-- Display Success/Error Messages -->
                @if (session('status'))
                    <div class="mb-4 text-green-600 font-medium">
                        {{ session('status') }}
                    </div>
                @endif

                <form action="{{ route('clubs.notify.send', $club->id) }}" method="POST">
                    @csrf

                    <div class="mb-4">
                        <label for="message" class="block text-gray-700 text-sm font-bold mb-2">
                            Message Content:
                        </label>
                        <textarea 
                            name="message" 
                            id="message" 
                            rows="5" 
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('message') border-red-500 @enderror"
                            placeholder="Type your announcement here..."
                        >{{ old('message') }}</textarea>
                        
                        @error('message')
                            <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center justify-between">
                        <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                            Send Notification
                        </button>
                        <a href="{{ route('clubs.show', $club->id) }}" class="text-sm text-gray-600 hover:underline">
                            Cancel
                        </a>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>