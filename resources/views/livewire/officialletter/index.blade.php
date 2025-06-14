
{{-- <container>
    <flux:input wire:model="search" placeholder="ค้นหา..."/>
    <button wire:click="$refresh">รีเฟรช</button>
    <table>
        <slot name="head">
            <th>เลขที่ลงรับ</th>
            <th>ชื่อเรื่อง</th>
            <th>วันที่</th>
            <th>Action</th>
        </slot>
        <slot name="body">
            @foreach($letters as $item)
                <tr>
                    <td>{{ $item->reg_number }}</td>
                    <td>{{ $item->subject }}</td>
                    <td>{{ $item->reg_date }}</td>
                    <td>
                        <a href="{{ route('officialletter.edit', $item->id) }}">✏️</a>
                        <button wire:click="delete({{ $item->id }})">🗑️</button>
                    </td>
                </tr>
            @endforeach
        </slot>
    </table>

    {{ $letters->links() }}
</container> --}}

<div class="p-6 bg-white rounded-2xl shadow-md space-y-6">
  
 @push('scripts')
<script>
    window.addEventListener('swal:success', event => {
        Swal.fire({
            icon: 'success',
            title: event.detail.title,
            text: event.detail.text,
            timer: 2000,
            showConfirmButton: false,
            toast: true,
            position: 'top-end',
        })
    });
</script>
@endpush
@php
    $isAdmin = auth()->user()->role === 'admin';
    $isSecretariat = \App\Models\ReceivingAgency::where('id', auth()->user()->receiving_agency_id)
        ->where('name', 'like', '%สำนักปลัด%')->exists();
@endphp        
    <div class="flex justify-between mb-2">
      
        <h1 class="text-2xl font-semibold text-gray-800 mb-6">รายการหนังสือรับ</h1>
        {{-- <flux:button wire:navigate href="{{ route('officialletter.create') }}" variant="primary">+ เพิ่มหนังสือรับ</flux:button> --}}
            
            @if (auth()->user()->role === 'admin'||auth()->user()->receiving_agency_id === 1)
            <flux:modal.trigger name="toggle-letter-modal">
                <flux:button size="sm" wire:click="create">+ เพิ่มหนังสือ</flux:button>
            </flux:modal.trigger>
            @endif
    
    </div>
    <!-- Modal -->
        <flux:modal wire:model="showModal" title="{{ $editing?->id ? 'แก้ไขหนังสือ' : 'เพิ่มหนังสือ' }}"  class="md:w-960">
            @include('livewire.officialletter.form')
        </flux:modal>
    <div class="flex flex-wrap gap-4 items-end mb-4">
             <div>
                <label>ค้นหา</label>
                 {{-- <flux:input   wire:model="textInput" 
                 placeholder="ค้นหาด้วยชื่อเรื่อง / หนังสือมาจาก / เลขที่... / หน่วยงานที่รับหนังสือ" class="max-w-xs" /> --}}
                 <input type="text" wire:model.defer="textInput" placeholder="เรื่อง,มาจาก,เลขที่,หน่วยงานที่รับ"
                    class="border px-4 py-2 rounded w-full" />
             
            </div>
            <div>
                <label>วันที่ส่ง</label>
                <input type="date" wire:model.defer="searchDate" class="border px-3 py-2 rounded w-full" />
            </div>
            <div>
                <label>&nbsp;</label>
               
                <flux:button  wire:click="search"  variant="primary"><flux:icon.magnifying-glass variant="solid" /></flux:button>
            </div>
             
    </div>
    <div class="overflow-x-auto">
        {{-- <x-message></x-message> --}}
            {{-- <table class="min-w-full table-auto text-sm">
        <thead class="bg-gray-50">
            <tr class="border-b text-left">
                <th class="px-3 py-3">เลขที่</th>
                <th class="px-3 py-3">เลขที่ลงรับ</th>
                <th class="px-3 py-3">ชื่อเรื่อง</th>
                <th class="px-3 py-3">หนังสือมาจาก</th>
                <th class="px-3 py-3">หน่วยงานที่รับหนังสือ</th>
                <th class="px-3 py-3">วันที่ลงรับ</th>
                <th class="px-3 py-3">จัดการ</th>
            </tr>
        </thead>
        <tbody>
            @foreach($letters as $letter)
            <tr class="border-b">
                <td class="px-3 py-3 whitespace-nowrap">{{ $letter->id }}</td>
                <td class="px-3 py-3 whitespace-nowrap">{{ $letter->reg_number }}</td>
                <td class="px-3 py-3 whitespace-nowrap">{{ $letter->subject }}</td>
                <td class="px-3 py-3 whitespace-nowrap">
                    {{ \App\Models\Department::find($letter->from_agency)?->name ?? 'ไม่พบข้อมูล' }}
                </td>
                <td class="px-3 py-3 whitespace-nowrap">
                    <ul class="list-disc list-inside">
                        @php
                            $rawToAgency = $letter->to_agency;
                            $ids = is_string($rawToAgency) ? json_decode($rawToAgency, true) : (is_array($rawToAgency) ? $rawToAgency : []);
                            $agencies = \App\Models\ReceivingAgency::whereIn('id', $ids)->pluck('name')->toArray();
                        @endphp
                        @forelse ($agencies as $agency)
                            <li>{{ $agency }}</li>
                        @empty
                            <li class="text-gray-400">ไม่มีข้อมูล</li>
                        @endforelse
                    </ul>
                </td>
                <td class="px-3 py-3 whitespace-nowrap">{{ $letter->reg_date }}</td>
                <td class="px-3 py-3 whitespace-nowrap">
                    <flux:modal.trigger name="show-data">
                        <flux:button wire:click="show({{ $letter->id }})" size="sm">ดู</flux:button>
                    </flux:modal.trigger>
                    <flux:button size="sm" wire:click="edit({{ $letter->id }})">แก้ไข</flux:button>
                    <flux:button size="sm"
                        wire:click="delete({{ $letter->id }})"
                        wire:confirm="Are you sure you want to delete?"
                        variant="danger">
                        ลบ
                    </flux:button>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table> --}}
       
        <div class="overflow-x-auto">
  <table class="table-auto w-full border border-gray-200 text-sm">
      <thead class="bg-gray-100 text-left">
          <tr>
              <th class="px-4 py-2">เลขที่</th>
              <th class="px-4 py-2">เลขที่ลงรับ</th>
              <th class="px-4 py-2">เรื่อง</th>
              <th class="px-4 py-2">หนังสือมาจาก</th>
              <th class="px-4 py-2">หน่วยงานที่รับหนังสือ</th>
              <th class="px-4 py-2">วันที่ลงรับ</th>
              {{-- <th class="px-4 py-2">บันทึกโดย</th>
             <th class="px-4 py-2">แก้ไขล่าสุดโดย</th> --}}
              <th class="px-4 py-2">จัดการ</th>
          </tr>
      </thead>
      <tbody>
          @foreach($letters as $letter)
              <tr class="border-t">
                  <td class="px-4 py-2">{{ $letter->id }}</td>
                  <td class="px-4 py-2">{{ $letter->reg_number }}</td>
                  <td class="px-4 py-2">{{ $letter->subject }}</td>
                  <td class="px-4 py-2">{{ \App\Models\Department::find($letter->from_agency)?->name ?? '-' }}</td>
                  <td class="px-4 py-2">
                      {{-- @php
                          $ids = json_decode($letter->to_agency ?? '[]', true);
                          $agencies = \App\Models\ReceivingAgency::whereIn('id', $ids)->pluck('name')->toArray();
                      @endphp
                      {{ implode(', ', $agencies) }} --}}
                       <ul class="list-disc list-inside">
                        @php
                            $rawToAgency = $letter->to_agency;
                            $ids = is_string($rawToAgency) ? json_decode($rawToAgency, true) : (is_array($rawToAgency) ? $rawToAgency : []);
                            $agencies = \App\Models\ReceivingAgency::whereIn('id', $ids)->pluck('name')->toArray();
                        @endphp
                        @forelse ($agencies as $agency)
                            <li>{{ $agency }}</li>
                        @empty
                            <li class="text-gray-400">ไม่มีข้อมูล</li>
                        @endforelse
                    </ul>
                  </td>
                  <td class="px-4 py-2">{{ $letter->reg_date }}</td>
                  {{-- <td class="px-4 py-2">{{ $letter->creator?->name ?? '-' }}</td>
                  <td class="px-4 py-2">{{ $letter->updater?->name ?? '-' }}</td> --}}
                  <td class="px-4 py-2 space-x-1">
                      {{-- <button wire:click="show({{ $letter->id }})" class="text-blue-500 underline">ดู</button>
                      <button wire:click="edit({{ $letter->id }})" class="text-yellow-500 underline">แก้ไข</button>
                      <button wire:click="delete({{ $letter->id }})" class="text-red-500 underline">ลบ</button> --}}
                        <flux:modal.trigger name="show-data">
                        <flux:button wire:click="show({{ $letter->id }})" size="sm">ดู</flux:button>
                        </flux:modal.trigger>
                        
                        {{-- <flux:button size="sm"
                            wire:click="delete({{ $letter->id }})"
                            wire:confirm="Are you sure you want to delete?"
                            variant="danger">
                            ลบ
                        </flux:button> --}}
                        
                        <!-- แสดงปุ่มลบ แก้ไขเฉพาะคนมีสิทธิ์ -->
                        @if ($isAdmin || $isSecretariat)
                            <flux:button size="sm" wire:click="edit({{ $letter->id }})">แก้ไข</flux:button>
                            <flux:button
                                size="sm"
                                onclick="confirmDelete({{ $letter->id }})"
                                variant="danger">
                                ลบ
                            </flux:button>
                        @endif
                  </td>
              </tr>
          @endforeach
      </tbody>
  </table>
</div>

          

 
            <div class="mt-4">
              {{-- <livewire:official-letters /> --}}
             <p>รวมทั้งหมด {{ $letters->total() }} รายการ / แสดงหน้า {{ $letters->currentPage() }} จาก {{ $letters->lastPage() }}</p>
             {{-- {{ $letters->links('livewire::pagination-links') }} --}}
               {{-- {{ $letters->links('pagination::simple-tailwind') }} --}}
                 {{-- {{ $letters->links() }} --}}
             
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

            <flux:modal name="show-data" class="md:w-960">
                <div class="space-y-6">
                    <div>
                        <flux:heading size="lg">รายละเอียดหนังสือ</flux:heading>
                        {{-- <flux:text class="mt-2">Make changes to your personal details.</flux:text> --}}
                    </div>
                    @if($selectedLetter)
                    <div class="space-y-6">
                        <div class="col-span-2"><strong>เลขที่ลงรับ:</strong> {{ $selectedLetter->reg_number }}</div>
                        <div class="col-span-2"><strong>วันที่ลงรับ:</strong> {{ $selectedLetter->reg_date }}</div>
                        <div class="col-span-2"><strong>เลขที่หนังสือ:</strong> {{ $selectedLetter->doc_number }}</div>
                        <div class="col-span-2"><strong>วันที่หนังสือ:</strong> {{ $selectedLetter->doc_date }}</div>
                        <div class="col-span-2"><strong>จากหน่วยงาน:</strong>
                        {{ \App\Models\Department::find($selectedLetter->from_agency)?->name ?? 'ไม่พบข้อมูล' }}
                        </div>
                        <div class="col-span-2"><strong>หน่วยงานที่รับหนังสือ:</strong>
                            
                            <ul class="list-disc list-inside">
                                @foreach($selectedLetter->to_agency as $agencyId)
                                    <li>{{ \App\Models\ReceivingAgency::find($agencyId)?->name ?? 'ไม่พบข้อมูล' }}</li>
                                @endforeach
                            </ul>
                           
                        </div>
  
                        <div class="col-span-2"><strong>ชื่อเรื่อง:</strong> {{ $selectedLetter->subject }}</div>
                        <div class="col-span-2"><strong>หน่วยงานที่รับ:</strong> {{ $selectedLetter->receiver_department }}</div>
                         <div class="col-span-2"><strong>บันทึกโดย:</strong> {{ $selectedLetter->creator?->name ?? '-' }}</div>
                          <div class="col-span-2"><strong>แก้ไขล่าสุดโดย:</strong> {{ $selectedLetter->updater?->name ?? '-' }}</div>
                       
                        <div class="col-span-2">
                            <strong>ไฟล์แนบ:</strong>
                            <ul class="list-disc list-inside">
                                @foreach ($selectedLetter->attachments ?? [] as $file)
                                    <li>
                                        <a href="{{ asset('storage/'.$file->file_path) }}" target="_blank" class="text-blue-600 underline">
                                            {{ basename($file->file_path) }}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endif
                </div>
            </flux:modal>


            
            
    </div>
  

    
</div>