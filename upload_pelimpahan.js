document.addEventListener('DOMContentLoaded', function () {
  // Upload
  const uploadButtons = document.querySelectorAll('.upload-doc-btn');
  uploadButtons.forEach(button => {
    button.addEventListener('click', function () {
      const id_pelimpahan = this.getAttribute('data-id');
      document.getElementById('id_pelimpahan').value = id_pelimpahan;
    });
  });

  // Delete
  const deleteButtons = document.querySelectorAll('.delete-doc-btn');
  deleteButtons.forEach(button => {
    button.addEventListener('click', function () {
      const id = this.getAttribute('data-id');
      document.getElementById('delete-id').value = id;

      // Debug
      console.log("ID pelimpahan yang akan DIHAPUS:", id);
    });
  });
});
