<div class="p-6 bg-white rounded-2xl shadow-md space-y-6">
    <div class="flex justify-between items-center">
        <h2 class="text-2xl font-semibold text-gray-800 mb-6">จัดการผู้ใช้งาน</h2>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50 text-left text-sm font-semibold text-gray-700">
                <tr>
                    <th class="px-4 py-2">ชื่อ</th>
                    <th class="px-4 py-2">อีเมล</th>
                    <th class="px-4 py-2">สถานะ</th>
                   
                    <th class="px-4 py-2">แผนก</th>
                    <th class="px-4 py-2">การจัดการ</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 text-sm text-gray-700">
                @forelse ($users as $user)
                    <tr>
                        <td class="px-4 py-2">{{ $user->name }}</td>
                        <td class="px-4 py-2">{{ $user->email }}</td>
                        <td class="px-4 py-2">
                            @if($user->approved)
                                <span class="text-green-600 font-medium">อนุมัติแล้ว</span>
                            @else
                                <span class="text-red-600 font-medium">รออนุมัติ</span>
                            @endif
                        </td>
                        
                        <td class="px-4 py-2">
                            {{ $user->receivingAgency->name ?? 'ยังไม่ระบุ' }}
                        </td>
                        <td class="px-4 py-2 space-x-2">
                            {{-- @if(!$user->approved)
                                <flux:button
                                    size="sm"
                                    wire:click="approve({{ $user->id }})"
                                   variant="primary"
                                    >
                                    อนุมัติ
                                </flux:button>
                               
                            @endif

                            <flux:select
                                wire:model="selectedAgencies.{{ $user->id }}"
                                wire:change="assignAgency({{ $user->id }})"
                                class="w-40"
                            >
                                <option value="">เลือกแผนก</option>
                                @foreach($agencies as $agency)
                                    <option value="{{ $agency->id }}">{{ $agency->name }}</option>
                                @endforeach
                            </flux:select> --}}
                             <flux:modal.trigger name="edit-user">
                                <flux:button size="sm" variant="primary" wire:click="edit({{ $user->id }})">แก้ไข</flux:button>
                            </flux:modal.trigger>

                            <flux:button size="sm" variant="danger"
                                {{-- wire:click="confirmDelete({{ $user->id }})" --}}
                                  onclick="confirmDelete({{ $user->id }})"
                                >ลบ</flux:button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-4 text-center text-gray-500">
                            ไม่มีผู้ใช้งาน
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
          {{-- Modal แก้ไข --}}
   
    <flux:modal wire:model="showModal" class="md:w-960">
        <form wire:submit.prevent="updateUser" class="space-y-4 p-4">
            <h3 class="text-lg font-semibold">แก้ไขสมาชิก</h3>

            <div>
                <label class="block mb-1">ชื่อ</label>
                <input type="text" class="w-full border p-2" wire:model.defer="editing.name">
            </div>

            <div>
                <label class="block mb-1">อีเมล</label>
                <input type="email" class="w-full border p-2" wire:model.defer="editing.email">
            </div>

            <div>
                <label class="block mb-1">รหัสผ่านใหม่ (ถ้าต้องการเปลี่ยน)</label>
                <input type="password" class="w-full border p-2" wire:model.defer="newPassword">
            </div>

            <div>
                <label class="block mb-1">แผนก</label>
                <select wire:model.defer="editing.receiving_agency_id" class="w-full border p-2">
                    <option value="">-- เลือก --</option>
                    @foreach($receivingAgencies as $agency)
                        <option value="{{ $agency->id }}">{{ $agency->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="inline-flex items-center">
                    <flux:field variant="inline">
                        <flux:checkbox wire:model="editing.approved" />
                        
                        <flux:label>อนุมัติการใช้งาน</flux:label>
                        <flux:error name="terms" />
                    </flux:field>
                </label>
             
            </div>

            <div class="flex justify-end gap-2">
                <flux:button type="submit" variant="primary">บันทึก</flux:button>
                <flux:modal.close>
                    <flux:button >ยกเลิก</flux:button>
                </flux:modal.close>
            </div>
        </form>
    </flux:modal>
    </div>

   
</div>
