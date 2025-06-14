<div class="w-full">
 
    <div class="flex justify-between mb-2">
        <h1 class="text-3x1 font-bold">Articles</h1>
        <flux:button wire:navigate href="/articles/create" variant="primary">Create</flux:button>
    </div>
  
    <div class="flex w-xl mb-3">
       
        <flux:input   wire:model="textInput" placeholder="Search..." class="me-2" />
        <flux:button  wire:click="search" variant="primary">Search</flux:button>
       
    </div>
     
    <div class="w-full">
        <x-message></x-message>
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr class="border-b">
                    <td class="px-3 py-3">ID</td>
                    <td class="px-3 py-3">Title</td>
                    <td class="px-3 py-3">Author</td>
                    <td class="px-3 py-3">Status</td>
                    <td class="px-3 py-3">Action</td>
                </tr>
            </thead>
            <tbody>
                @if($articles->isNotEmpty())
                @foreach ($articles as $article)
                <tr class="border-b">
                    <td class="px-3 py-3">{{ $article->id }}</td>
                    <td class="px-3 py-3">{{ $article->title }}</td>
                    <td class="px-3 py-3">{{ $article->author }}</td>
                    <td class="px-3 py-3">
                        @if($article->status=='Active')
                            <flux:badge color="green">Active</flux:badge>
                        @else
                            <flux:badge color="red">Block</flux:badge>
                        @endif
                    </td>
                    <td class="px-3 py-3">
                        <flux:button wire:navigate href="/articles/{{$article->id}}/edit" size="sm"  variant="primary">Edit</flux:button>
                        <flux:button size="sm" 
                        wire:click="delete({{$article->id}})"
                        wire:confirm="Are you sure you want to delete?"
                        
                        variant="danger">Delete</flux:button>
                      
                    </td>
                </tr>    
                @endforeach   
                @else
                    
                @endif
            </tbody>
        </table>
        <div class="mt-3">
            {{ $articles->links()}}
        </div>
    </div>   
</div>
