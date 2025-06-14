<script>
    // ✅ Success Alert event.detail.timer ||
    window.addEventListener('swal:success', event => {
        Swal.fire({
            icon: 'success',
            title: event.detail.title || 'สำเร็จ',
            text: event.detail.text || '',
            timer: event.detail.timer || 10000,
           // timerProgressBar: true,   // แสดง progress bar ระยะเวลา
            showConfirmButton: false, // ❗ ต้องมีอันนี้
           toast: event.detail.toast || false,
          position: event.detail.position || 'top-end',//'center',//
      
        });
    });

    // ✅ Error Alert
    window.addEventListener('swal:error', event => {
        Swal.fire({
            icon: 'error',
            title: event.detail.title || 'เกิดข้อผิดพลาด',
            text: event.detail.text || '',
            timer: event.detail.timer || 3000,
            showConfirmButton: true,
        });
    });

    // ✅ Info Alert
    window.addEventListener('swal:info', event => {
        Swal.fire({
            icon: 'info',
            title: event.detail.title || 'แจ้งเตือน',
            text: event.detail.text || '',
            timer: event.detail.timer || 2000,
            showConfirmButton: false,
        });
    });
</script>
