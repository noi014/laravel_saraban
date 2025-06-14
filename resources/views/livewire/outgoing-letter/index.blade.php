<div class="p-6 bg-white rounded-2xl shadow-md space-y-6">
    <div class="flex justify-between mb-2">
      
        <h1 class="text-2xl font-semibold text-gray-800 mb-6">รายการหนังสือส่ง</h1>
           
           <flux:button size="sm" wire:click="create">+ เพิ่มหนังสือ</flux:button>
    
    </div>
        <div class="flex flex-wrap gap-4 items-end mb-4">
            <div>
                <label>ค้นหา</label>
                <input type="text" wire:model.defer="textInput" placeholder="เลขที่, เรื่อง, หน่วยงาน"
                    class="border px-3 py-2 rounded w-full" />
            </div>

            <div>
                <label>วันที่</label>
                <input type="date" wire:model.defer="searchDate" class="border px-3 py-2 rounded w-full" />
            </div>
            @if (auth()->user()->role === 'admin')
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
                {{-- <button wire:click="$refresh" class="px-4 py-2 bg-blue-500 text-white rounded">
                    🔍 ค้นหา
                </button> --}}
                 <flux:button  wire:click="search"  variant="primary" ><flux:icon.magnifying-glass variant="solid" /></flux:button>
            </div>
        </div>

    
    {{-- @if(session()->has('success'))
        <div class="text-green-500">{{ session('success') }}</div>
    @endif --}}

    <table class="table-auto w-full border border-gray-200 text-sm">
        <thead class="bg-gray-100 text-left">
            <tr>
                <th class="px-4 py-2" >เลขที่</th>
                <th class="px-4 py-2" >วันที่</th>
                <th class="px-4 py-2" >เรื่อง</th>
                <th class="px-4 py-2" >ส่งถึง</th>
                
                <th class="px-4 py-2" >ส่งจาก</th>
                <th class="px-4 py-2" >ไฟล์</th>
                <th class="px-4 py-2" >จัดการ</th>
            </tr>
        </thead>
        <tbody>
            @foreach($letters as $letter)
                <tr  class="border-t" >
                    <td class="px-4 py-2" >{{ $letter->doc_number }}</td>
                    <td class="px-4 py-2" >{{ \Carbon\Carbon::parse($letter->doc_date)->format('d/m/Y') }}</td>
                     <td class="px-4 py-2" >{{ $letter->subject }}</td>
                    <td class="px-4 py-2" >{{ $letter->toAgency->name ?? '-' }}</td>
                   
                    <td class="px-4 py-2" >{{ $letter->sender->name ?? '-' }}</td>
                    <td class="px-4 py-2" >
                        @if($letter->file_path)
                            <a href="{{ asset('storage/' . $letter->file_path) }}" target="_blank">ดูไฟล์</a>
                        @endif
                    </td>
                    <td class="px-4 py-2">
                        <flux:button size="sm" wire:click="edit({{ $letter->id }})">แก้ไข</flux:button>
                        {{-- <flux:button size="sm" wire:click="delete({{ $letter->id }})" variant="danger">ลบ</flux:button> --}}
                        <flux:button
                                size="sm"
                                onclick="confirmDelete({{ $letter->id }})"
                                variant="danger">
                                ลบ
                            </flux:button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- {{ $letters->links() }} --}}
     <div class="mt-4">
             <p>รวมทั้งหมด {{ $letters->total() }} รายการ / แสดงหน้า {{ $letters->currentPage() }} จาก {{ $letters->lastPage() }}</p>
                <div class="flex justify-center mt-4 space-x-2">
    {{-- ก่อนหน้า --}}
                @if ($letters->onFirstPage())
                    <flux:button size="sm" disabled>ก่อนหน้า</flux:button>
                @else
                    <flux:button size="sm" wire:click="previousPage" wire:loading.attr="disabled">ก่อนหน้า</flux:button>
                @endif

                {{-- ตัวเลขหน้า --}}
                @foreach ($letters->getUrlRange(1, $letters->lastPage()) as $page => $url)
                    @if ($page == $letters->currentPage())
                        <flux:button size="sm" variant="danger" disabled>{{ $page }}</flux:button>
                    @else
                        <flux:button size="sm" wire:click="gotoPage({{ $page }})" wire:loading.attr="disabled">{{ $page }}</flux:button>
                    @endif
                @endforeach

                {{-- ถัดไป --}}
                @if ($letters->hasMorePages())
                    <flux:button size="sm" wire:click="nextPage" wire:loading.attr="disabled">ถัดไป</flux:button>
                @else
                    <flux:button size="sm" disabled>ถัดไป</flux:button>
                @endif
            </div>

            </div>

    <flux:modal wire:model="showModal" class="md:w-960">
        {{-- <x-slot name="title">{{ $letterId ? 'แก้ไข' : 'เพิ่ม' }} หนังสือส่ง</x-slot> --}}
        <h2 class="text-2xl font-bold mb-4">
                {{ $letterId ? 'แก้ไข' : 'เพิ่ม' }} หนังสือส่ง
        </h2>
        <flux:input wire:model="doc_number" label="เลขที่หนังสือ" />
        <flux:input wire:model="doc_date" label="วันที่" type="date" />
        {{-- <flux:select wire:model="to_agency_id" label="ส่งถึงหน่วยงาน">
            @foreach($department as $agency)
                <option value="{{ $agency->id }}">{{ $agency->name }}</option>
            @endforeach
        </flux:select> --}}
       <div>
    <label for="to_agency_input" class="block font-medium mb-1">ส่งถึงหน่วยงาน</label>
    <input 
        list="departments" 
        id="to_agency_input"
        wire:model.lazy="to_agency_name" 
        wire:change="updateToAgencyId" 
        placeholder="พิมพ์ชื่อส่งถึงหน่วยงาน.."
        class="border px-3 py-2 w-full rounded 
               @error('to_agency_name') border-red-500 @else border-gray-300 @enderror"
    >

    <datalist id="departments">
        @foreach ($departments as $department)
            <option value="{{ $department->name }}">
        @endforeach
    </datalist>

    {{-- ซ่อน id ที่ได้จากชื่อ  --}}
    <input type="hidden" wire:model="to_agency_id" />
    
    @error('to_agency_name') 
        <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
    @enderror
</div>

        <flux:input wire:model="subject" label="เรื่อง" />
        <flux:select wire:model="sender_id" label="ส่งจาก">
            @foreach($executives as $exec)
                <option value="{{ $exec->id }}">{{ $exec->name }}</option>
            @endforeach
        </flux:select>
        {{-- <flux:input type="file" wire:model="file" label="ไฟล์แนบ" /> --}}
        <flux:input type="file" wire:model="file" label="ไฟล์แนบ" />

                @error('file')
                    <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                @enderror

                @if ($letterId && $filePreviewPath = optional(\App\Models\OutgoingLetter::find($letterId))->file_path)
                    <div class="mt-2">
                        <a href="{{ asset('storage/' . $filePreviewPath) }}" target="_blank" class="text-blue-600 underline">
                            ดูไฟล์เดิม
                        </a>
                    </div>
                @endif

        <flux:button wire:click="save">บันทึก</flux:button>
    </flux:modal>
</div>
