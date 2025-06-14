<div class="p-6 bg-white rounded-2xl shadow-md space-y-6">
    <div class="flex justify-between mb-2">
      
        <h1 class="text-2xl font-semibold text-gray-800 mb-6">รายการประกาศ</h1>
            @if (auth()->user()->role === 'admin'||auth()->user()->receiving_agency_id === 1)
           <flux:button size="sm" wire:click="create">+ เพิ่มประกาศ</flux:button>
             @endif
           
    
    </div>
    {{-- Search Section --}}
    {{-- <div class="flex gap-4 mb-4">
        <input type="text" wire:model.defer="searchTerm" placeholder="เลขที่ / ชื่อประกาศ" class="border p-2 rounded" />
        <input type="date" wire:model.defer="searchDate" class="border p-2 rounded" />
        <button wire:click="$refresh" class="bg-blue-500 text-white px-4 py-2 rounded">ค้นหา</button>
    </div> --}}
{{-- Add Button --}}
     {{-- @if (auth()->user()->role === 'admin'||auth()->user()->receiving_agency_id === '1')
    <button wire:click="create" class="bg-green-500 text-white px-4 py-2 rounded mb-2">+ เพิ่มประกาศ</button>
    @endif --}}
            {{-- @if (auth()->user()->role === 'admin'||auth()->user()->receiving_agency_id === '1')
                <flux:button size="sm" wire:click="create">+ เพิ่มประกาศ</flux:button>
             @endif --}}

    {{-- Table --}}
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
    <table class="table-auto w-full border border-gray-200 text-sm">
        <thead class="bg-gray-100 text-left">
            <tr >
                <th class="px-4 py-2">เลขที่</th>
                <th class="px-4 py-2" >ชื่อ</th>
                <th class="px-4 py-2">วันที่</th>
                <th class="px-4 py-2">หน่วยงาน</th>
                <th class="px-4 py-2">ไฟล์</th>
                 @if (auth()->user()->role === 'admin'||auth()->user()->receiving_agency_id === 1)
                <th class="px-4 py-2">จัดการ</th>
                 @endif
            </tr>
        </thead>
        <tbody>
            @foreach ($announcements as $a)
                <tr class="border-t">
                    <td class="px-4 py-2">{{ $a->announcement_number }}</td>
                    <td class="px-4 py-2" >{{ $a->announcement_name }}</td>
                    <td class="px-4 py-2">{{ \Carbon\Carbon::parse($a->announcement_date)->format('d/m/Y') }}</td>
                    <td class="px-4 py-2">
                        {{-- {{ $a->agencies->pluck('name')->join(', ') }} --}}
                        @foreach ($a->agencies as $agency)
                        <span class="inline-block bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded mr-1">
                            {{ $agency->name }}
                        </span>
                        @endforeach
                    </td>
                    <td class="px-4 py-2">
                        @if($a->file_path)
                            <a href="{{ Storage::url($a->file_path) }}" target="_blank">ดูไฟล์</a>
                        @endif
                    </td>
                     @if (auth()->user()->role === 'admin'||auth()->user()->receiving_agency_id === 1)
                    <td class="px-4 py-2">
                        
                            {{-- <button wire:click="openModal({{ $a->id }})" class="text-blue-500">แก้ไข</button> <button wire:click="delete({{ $a->id }})" class="text-red-500">ลบ</button>--}}
                              {{-- <flux:button size="sm" wire:click="edit({{ $a->id }})">แก้ไข</flux:button>
                               <flux:button size="sm" wire:click="confirmDelete({{ $a->id }})" variant="danger">ลบ</flux:button> --}}
                            <flux:button size="sm" wire:click="edit({{ $a->id }})">แก้ไข</flux:button>
                      
                        <flux:button
                                size="sm"
                                onclick="confirmDelete({{ $a->id }})" 
                                variant="danger">
                                ลบ
                            </flux:button>
                       
                    </td> @endif
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- {{ $announcements->links() }} --}}
    <div class="mt-4">
             <p>รวมทั้งหมด {{ $announcements->total() }} รายการ / แสดงหน้า 
                {{ $announcements->currentPage() }} จาก {{ $announcements->lastPage() }}</p>
                <div class="flex justify-center mt-4 space-x-2">
    {{-- ก่อนหน้า --}}
                @if ($announcements->onFirstPage())
                    <flux:button size="sm" disabled>ก่อนหน้า</flux:button>
                @else
                    <flux:button size="sm" wire:click="previousPage" wire:loading.attr="disabled">ก่อนหน้า</flux:button>
                @endif 

                {{-- ตัวเลขหน้า --}}
                 @foreach ($announcements->getUrlRange(1, $announcements->lastPage()) as $page => $url)
                    @if ($page == $announcements->currentPage())
                        <flux:button size="sm" variant="danger" disabled>{{ $page }}</flux:button>
                    @else
                        <flux:button size="sm" wire:click="gotoPage({{ $page }})" wire:loading.attr="disabled">{{ $page }}</flux:button>
                    @endif
                @endforeach

                {{-- ถัดไป --}}
                @if ($announcements->hasMorePages())
                    <flux:button size="sm" wire:click="nextPage" wire:loading.attr="disabled">ถัดไป</flux:button>
                @else
                    <flux:button size="sm" disabled>ถัดไป</flux:button>
                @endif
            </div>

            </div>

    {{-- Modal --}}
    <flux:modal wire:model.defer="showModal" class="md:w-960">
         <h2 class="text-2xl font-bold mb-4">
                {{ $announcementId ? 'แก้ไข' : 'เพิ่ม' }} ประกาศ
        </h2>
    <form wire:submit.prevent="save">
            <div class="bg-white rounded p-6 w-full max-w-xl">
                {{-- <h2 class="text-lg font-bold mb-4">{{ $announcementId ? 'แก้ไข' : 'เพิ่ม' }} ประกาศ</h2> --}}

            
                <flux:input label="เลขที่ประกาศ" wire:model.defer="announcement_number" />
                <flux:input label="ชื่อประกาศ" wire:model.defer="announcement_name" />
                <flux:input type="date" label="วันที่สั่งประกาศ" wire:model.defer="announcement_date" />
                <flux:input type="file" wire:model="file" label="ไฟล์แนบ" />
                @error('file')
                    <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                @enderror

                @if ($announcementId && $filePreviewPath = optional(\App\Models\Announcement::find($announcementId))->file_path)
                    <div class="mt-2">
                        <a href="{{ asset('storage/' . $filePreviewPath) }}" target="_blank" class="text-blue-600 underline">
                            ดูไฟล์เดิม
                        </a>
                    </div>
                @endif
                {{-- <label>เลือกหน่วยงาน:</label>
                <select wire:model="selected_agencies" multiple class="border p-2 w-full mb-4" size="4">
                    @foreach($agencyOptions as $agency)
                        <option value="{{ $agency->id }}">{{ $agency->name }}</option>
                    @endforeach
                </select> --}}
                <flux:checkbox.group 
                    class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-2"
                    wire:model="selected_agencies" 
                    label="หน่วยงาน">

                    @foreach ($agencyOptions->sortBy('id') as $option)
                        <flux:checkbox value="{{ $option->id }}" label="{{ $option->name }}" />
                    @endforeach
                </flux:checkbox.group>

                <div class="flex justify-end gap-2">
                    {{-- <button wire:click="$set('showModal', false)" class="px-4 py-2 bg-gray-300 rounded">ยกเลิก</button> --}}
                    <button wire:click="save" class="px-4 py-2 bg-green-500 text-white rounded">บันทึก</button>
                </div>
            </div>

            </form>
        </flux:modal>
</div>

