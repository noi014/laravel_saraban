@if (Session::has('success'))
  
            
            <flux:callout variant="success" icon="check-circle" inline x-data="{ visible: true }" x-show="visible"
           
            >
                <flux:callout.heading class="flex gap-2 @max-md:flex-col items-start">{{ Session::get('success') }}</flux:callout.heading>
                <x-slot name="controls">
                    <flux:button icon="x-mark" variant="ghost" x-on:click="visible = false" />
                </x-slot>
            </flux:callout>
         
   @endif

@if (Session::has('error'))
    {{-- <div class="w-full bg-red-300 px-3 py-3 border-red-500 mb-3">
        
        <flux:callout variant="danger" icon="x-circle" heading="{{ Session::get('error') }}" />
    </div> --}}
    <flux:callout variant="danger" icon="x-circle" inline x-data="{ visible: true }" x-show="visible">
        <flux:callout.heading class="flex gap-2 @max-md:flex-col items-start">{{ Session::get('error') }}</flux:callout.heading>
        <x-slot name="controls">
            <flux:button icon="x-mark" variant="ghost" x-on:click="visible = false" />
        </x-slot>
    </flux:callout>
@endif