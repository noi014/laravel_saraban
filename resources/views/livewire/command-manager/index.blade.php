
<div class="p-6 bg-white rounded-2xl shadow-md space-y-6">
    <div class="flex justify-between mb-2">
      
        <h1 class="text-2xl font-semibold text-gray-800 mb-6">รายการคำสั่ง</h1>
            @if (auth()->user()->role === 'admin'||auth()->user()->receiving_agency_id === 1)
           <flux:button size="sm" wire:click="create">+ เพิ่มคำสั่ง</flux:button>
             @endif
           
    
    </div>
    {{-- Search --}}
    <div class="flex flex-wrap gap-4 mb-4 items-end">
        <div>
            <label>ค้นหา</label>
            <input type="text" wire:model.defer="searchTerm" class="border px-3 py-2 rounded w-full" placeholder="เลขที่ / ชื่อ"/>
        </div>

        <div>
            <label>วันที่</label>
            <input type="date" wire:model.defer="searchDate" class="border px-3 py-2 rounded w-full" />
        </div>
    {{-- เฉพาะ admin: หน่วยงานผู้บันทึก --}}
        @if (auth()->user()->role === 'admin'||auth()->user()->receiving_agency_id === 1)
            <div>
                <label>หน่วยงานผู้บันทึก</label>
                <select wire:model.defer="searchCreatedByAgency" class="border px-3 py-2 rounded w-full">
                    <option value="">-- เลือกทั้งหมด --</option>
                    @foreach ($receivingAgencyOptions as $agency)
                        <option value="{{ $agency->id }}">{{ $agency->name }}</option>
                    @endforeach
                </select>
            </div>
        @endif
        <div>
            <label>&nbsp;</label>
            <flux:button wire:click="search" variant="primary">
                <flux:icon.magnifying-glass variant="solid" /> 
            </flux:button>
        </div>
    </div>

<flux:modal wire:model.defer="showModal" class="md:w-960">
     <h2 class="text-2xl font-bold mb-4">
                {{ $letterId ? 'แก้ไข' : 'เพิ่ม' }} คำสั่ง
        </h2>
  <form wire:submit.prevent="save">
    <flux:input label="เลขที่คำสั่ง" wire:model.defer="command_number" />
    <flux:input label="ชื่อคำสั่ง" wire:model.defer="command_name" />
    <flux:input type="date" label="วันที่สั่ง" wire:model.defer="command_date" />
    <flux:input type="file" wire:model="file_path" label="ไฟล์แนบ" />

                @error('file_path')
                    <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                @enderror

                @if ($letterId && $filePreviewPath = optional(\App\Models\Command::find($letterId))->file_path)
                    <div class="mt-2">
                        <a href="{{ asset('storage/' . $filePreviewPath) }}" target="_blank" class="text-blue-600 underline">
                            ดูไฟล์เดิม
                        </a>
                    </div>
                @endif
    {{-- <flux:input type="file" label="ไฟล์แนบ" wire:model="file_path" /> --}}
    {{-- <flux:select multiple label="หน่วยงาน" wire:model.defer="agency_ids">
        @foreach($departments as $agency)
            <option value="{{ $agency->id }}">{{ $agency->name }}</option>
        @endforeach
    </flux:select> --}}
     <flux:checkbox.group 
            class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-2"
            wire:model="agency_ids" 
            label="หน่วยงาน">

            @foreach ($departments->sortBy('id') as $option)
                <flux:checkbox value="{{ $option->id }}" label="{{ $option->name }}" />
            @endforeach
    </flux:checkbox.group>
    <div class="mt-4">
        <flux:button type="submit" variant="primary">บันทึก</flux:button>
       
    </div>
  </form>
</flux:modal>

<table class="table-auto w-full border border-gray-200 text-sm">
    <thead class="bg-gray-100 text-left">
        <tr>
            <th class="px-4 py-2">เลขที่</th>
            <th class="px-4 py-2">ชื่อ</th>
            <th class="px-4 py-2">วันที่</th>
            <th class="px-4 py-2">หน่วยงาน</th>
            <th class="px-4 py-2">ไฟล์</th>
             @if (auth()->user()->role === 'admin'||auth()->user()->receiving_agency_id === 1)
            <th class="px-4 py-2">จัดการ</th>
             @endif
        </tr>
    </thead>
    <tbody>
        @foreach($commands as $cmd)
            <tr class="border-t">
                <td class="px-4 py-2">{{ $cmd->command_number }}</td>
                <td class="px-4 py-2">{{ $cmd->command_name }}</td>
                 <td class="px-4 py-2">{{ \Carbon\Carbon::parse($cmd->command_date)->format('d/m/Y') }}</td>
                   <td class="px-4 py-2">
                        @foreach ($cmd->agencies as $agency)
                        <span class="inline-block bg-green-100 text-green-800 text-xs px-2 py-1 rounded mr-1">
                            {{ $agency->name }}
                        </span>
                        @endforeach
                  </td>
                <td class="px-4 py-2">
                    @if($cmd->file_path)
                        <a href="{{ Storage::url($cmd->file_path) }}" target="_blank">ไฟล์</a>
                    @endif
                </td class="px-4 py-2">
                  @if (auth()->user()->role === 'admin'||auth()->user()->receiving_agency_id === 1)
                <td class="px-4 py-2">
                    {{-- <flux:button size="sm" wire:click="edit({{ $cmd->id }})">แก้ไข</flux:button>
                    <flux:button size="sm" wire:click="confirmDelete({{ $cmd->id }})" variant="danger">ลบ</flux:button>  --}}
                    <flux:button size="sm" wire:click="edit({{ $cmd->id }})">แก้ไข</flux:button>
                      
                        <flux:button
                                size="sm"
                                onclick="confirmDelete({{ $cmd->id }})" 
                                variant="danger">
                                ลบ
                            </flux:button>
                </td>
                 @endif
            </tr>
        @endforeach
    </tbody>
</table>
<div class="mt-4">
             <p>รวมทั้งหมด {{ $commands->total() }} รายการ / แสดงหน้า 
                {{ $commands->currentPage() }} จาก {{ $commands->lastPage() }}</p>
                <div class="flex justify-center mt-4 space-x-2">
    {{-- ก่อนหน้า --}}
                @if ($commands->onFirstPage())
                    <flux:button size="sm" disabled>ก่อนหน้า</flux:button>
                @else
                    <flux:button size="sm" wire:click="previousPage" wire:loading.attr="disabled">ก่อนหน้า</flux:button>
                @endif 

                {{-- ตัวเลขหน้า --}}
                 @foreach ($commands->getUrlRange(1, $commands->lastPage()) as $page => $url)
                    @if ($page == $commands->currentPage())
                        <flux:button size="sm" variant="danger" disabled>{{ $page }}</flux:button>
                    @else
                        <flux:button size="sm" wire:click="gotoPage({{ $page }})" wire:loading.attr="disabled">{{ $page }}</flux:button>
                    @endif
                @endforeach

                {{-- ถัดไป --}}
                @if ($commands->hasMorePages())
                    <flux:button size="sm" wire:click="nextPage" wire:loading.attr="disabled">ถัดไป</flux:button>
                @else
                    <flux:button size="sm" disabled>ถัดไป</flux:button>
                @endif
            </div>

            </div>
</div>
