<div class="p-6 bg-white rounded-2xl shadow-md space-y-6">
    <div class="flex justify-between mb-2">
      
        <h1 class="text-2xl font-semibold text-gray-800 mb-6">รายการหนังสือส่ง ว.</h1>
           
           <flux:button size="sm" wire:click="create">+ เพิ่มหนังสือ ว.</flux:button>
    
    </div>
        {{-- <div class="flex flex-wrap gap-4 items-end mb-4">
            <div>
                <label>ค้นหา</label>
                <input type="text" wire:model.defer="textInput" placeholder="เลขที่, เรื่อง, หน่วยงาน"
                    class="border px-3 py-2 rounded w-full" />
            </div>

            <div>
                <label>วันที่ส่ง</label>
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
              
                 <flux:button  wire:click="search"  variant="primary" ><flux:icon.magnifying-glass variant="solid" /></flux:button>
            </div>
        </div> --}}
        {{-- <div class="flex flex-col md:flex-row gap-2 items-center mb-4">
            <flux:input type="text" wire:model.debounce.500ms="search" placeholder="ค้นหาเลขที่หรือเรื่อง..." />
            <flux:input type="date" wire:model="searchDate" />
            <flux:input type="text" wire:model.debounce.500ms="searchAgency" placeholder="ค้นหาหน่วยงาน" />
        </div> --}}
       {{-- <div class="flex flex-wrap gap-4 items-end mb-4">
            <div>
                <label>ค้นหา</label>
                <input type="text" wire:model.defer="textInput" placeholder="เลขที่, เรื่อง, หน่วยงาน"
                    class="border px-3 py-2 rounded w-full" />
            </div>

             <div>
                <label>วันที่ส่ง</label>
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
               
                 <flux:button  wire:click="search"  variant="primary" ><flux:icon.magnifying-glass variant="solid" /></flux:button>
            </div>
        </div> --}}

        <div class="flex flex-wrap gap-4 items-end mb-4">
    {{-- ค้นหาทั่วไป --}}
    <div>
        <label>ค้นหา</label>
        <input type="text" wire:model.defer="textInput" placeholder="เลขที่, เรื่อง, หน่วยงาน"
            class="border px-3 py-2 rounded w-full" />
    </div>

    {{-- วันที่ส่ง --}}
    <div>
        <label>วันที่</label>
        <input type="date" wire:model.defer="searchDate" class="border px-3 py-2 rounded w-full" />
    </div>

    {{-- เฉพาะ admin: หน่วยงานผู้บันทึก --}}
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

    {{-- ปุ่มค้นหา --}}
    <div>
        <label>&nbsp;</label>
        <flux:button wire:click="search" variant="primary">
            <flux:icon.magnifying-glass variant="solid" />
        </flux:button>
    </div>
</div>


<table  class="table-auto w-full border border-gray-200 text-sm">
    <thead class="bg-gray-100 text-left">
        <tr>
            <th class="px-4 py-2">เลขที่</th>
            <th class="px-4 py-2">วันที่</th>
            <th class="px-4 py-2">เรื่อง</th>
            <th class="px-4 py-2">ส่งถึง</th>
            <th class="px-4 py-2">ส่งจาก</th>
            <th class="px-4 py-2" >ไฟล์</th>
            <th class="px-4 py-2">จัดการ</th>
        </tr>
    </thead>
    <tbody>
        @if ($letters && $letters->count())
         @foreach ($letters as $letter)
       
        <tr class="border-t">
            <td class="px-4 py-2">{{ $letter->doc_number }}</td>
            <td class="px-4 py-2">{{ $letter->doc_date }}</td>
            <td class="px-4 py-2">{{ $letter->subject }}</td>
            <td class="px-4 py-2">
                {{-- @foreach($letter->agencies as $agency)
                    <span>{{ $agency->name }}</span>@if(!$loop->last), @endif
                @endforeach --}}
                @foreach ($letter->agencies as $agency)
                        <span class="inline-block bg-red-100 text-red-800 text-xs px-2 py-1 rounded mr-1">
                            {{ $agency->name }}
                        </span>
                        @endforeach
            </td>
            <td class="px-4 py-2">{{ $letter->sender->name }}</td>
            <td class="px-4 py-2">  
                @if($letter->file_path)
                            <a href="{{ asset('storage/' . $letter->file_path) }}" target="_blank">ดูไฟล์</a>
                @endif
            </td>
            <td class="px-4 py-2">
                <flux:button size="sm" wire:click="edit({{ $letter->id }})">แก้ไข</flux:button>
                      
                        <flux:button
                                size="sm"
                                onclick="confirmDelete({{ $letter->id }})" 
                                variant="danger">
                                ลบ
                            </flux:button>
            </td>
        </tr>
        @endforeach
        @else
        <tr>
            <td colspan="5" class="text-center">ไม่พบข้อมูล</td>
        </tr>
        @endif
    </tbody>
</table>

{{-- {{ $letters->links() }} --}}
 <div class="mt-4">
             <p>รวมทั้งหมด {{ $letters->total() }} รายการ / แสดงหน้า 
                {{ $letters->currentPage() }} จาก {{ $letters->lastPage() }}</p>
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
        {{-- <x-slot name="title">{{ $letterId ? 'แก้ไข' : 'เพิ่ม' }} หนังสือส่ง ว.</x-slot> --}}

       <h2 class="text-2xl font-bold mb-4">
    {{ $letterId ? 'แก้ไข' : 'เพิ่ม' }} หนังสือส่ง ว.{{$letterId}}
        </h2>
        <form wire:submit.prevent="save">
                <flux:input label="เลขที่หนังสือ" wire:model="doc_number" />
               
                <flux:input type="date" label="วันที่ส่ง" wire:model="doc_date" />
               
                <flux:input label="เรื่อง" wire:model="subject" />
               
                <flux:select label="ส่งจาก" wire:model="sender_id">
                    @foreach($executives as $exec)
                        <option value="{{ $exec->id }}">{{ $exec->name }}</option>
                    @endforeach
                </flux:select>
              
                
                <div>
                    <!-- ช่องป้อนชื่อหน่วยงาน -->
                    <input
                    
                        list="agency-list"
                        wire:model.lazy="selectedAgencyName"
                        wire:keydown.enter.prevent="addAgency"
                        placeholder="พิมพ์ชื่อหน่วยงานแล้วกด Enter"
                        class="border px-3 py-2 w-full rounded 
                            @error('selectedAgencyName') border-red-500 @else border-gray-300 @enderror"
                    />

                    <!-- รายการหน่วยงานทั้งหมด -->
                    <datalist id="agency-list">
                        @isset($departments)
                            @foreach ($departments as $agency)
                                <option value="{{ $agency->name }}">
                            @endforeach
                        @endisset
                    </datalist>

                    <!-- หน่วยงานที่เลือกแล้ว -->
                    <div class="mt-2">
                        @foreach ($agency_ids as $id)
                            @php
                                $agency = $departments->firstWhere('id', $id);
                            @endphp
                            @if($agency)
                                <span class="inline-block bg-blue-100 text-blue-800 px-2 py-1 rounded-full text-sm mr-1 mb-1">
                                    {{ $agency->name }}
                                    <button wire:click="removeAgency({{ $agency->id }})" class="ml-1 text-red-500">&times;</button>
                                </span>
                            @endif
                        @endforeach
                    </div>
                </div>

                {{-- <flux:input type="file" wire:model="file_path" label="ไฟล์แนบ" /> --}}
                <flux:input type="file" wire:model="file_path" label="ไฟล์แนบ" />

                @error('file_path')
                    <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                @enderror

                @if ($letterId && $filePreviewPath = optional(\App\Models\SendingLetter::find($letterId))->file_path)
                    <div class="mt-2">
                        <a href="{{ asset('storage/' . $filePreviewPath) }}" target="_blank" class="text-blue-600 underline">
                            ดูไฟล์เดิม
                        </a>
                    </div>
                @endif
                
                <flux:button type="submit">บันทึก</flux:button>
        </form>
    </flux:modal>
</div>

