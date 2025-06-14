<div class="w-full">
 
    <div class="flex justify-between mb-3">
        <h1 class="text-3x1 font-bold">Articles / Create</h1>
        <flux:button wire:navigate href="/articles" variant="primary">Back</flux:button>
    </div>
    <div class="w-full">
        <form wire:submit="save" class="space-y-2">
            <flux:field>
                <flux:label>Title</flux:label>
                <flux:input placeholder="Title" wire:model="title" type="text" />
                <flux:error name="title" />
            </flux:field>

            <flux:field>
                <flux:label>Author</flux:label>
                <flux:input placeholder="Author" wire:model="author" type="text" />
                <flux:error name="author" />
            </flux:field>

            <flux:field>
                <flux:label>Content</flux:label>
                <flux:textarea placeholder="Content" rows="5" wire:model="content" type="text" />
               
            </flux:field>
            <div class="mb-3 flex justify-between">
                <flux:radio.group wire:model="status" label="Status" variant="segmented">
                    <flux:radio label="Active" value="Active" />
                    <flux:radio label="Block" value="Block"/>
                    
                </flux:radio.group>
            </div>
            

            <flux:button type="submit" variant="primary">
                Save
            </flux:button>
    

        </form>
        
    </div>   
</div>

