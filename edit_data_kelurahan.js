function copyFromKTP() {
  const checked = document.getElementById("sameAsKTP").checked;

  const alamat = document.getElementById("alamat");
  const kecamatan = document.getElementById("kecamatan");
  const kelurahan = document.getElementById("kelurahan");
  const kode_pos = document.getElementById("kode_pos");

  if (checked) {
    alamat.value = document.getElementById("ktp_alamat").value;
    kecamatan.value = document.getElementById("ktp_kecamatan").value;
    kode_pos.value = document.getElementById("ktp_kode_pos").value;

    // Ambil value dan text kelurahan dari KTP
    const ktp_kel = document.getElementById("ktp_kelurahan");
    const selectedOption = ktp_kel.options[ktp_kel.selectedIndex];

    // Set ulang kelurahan domisili agar ada option-nya
    kelurahan.innerHTML = `<option value="${selectedOption.value}" selected>${selectedOption.text}</option>`;
  } else {
    alamat.value = "";
    kecamatan.value = "";
    kode_pos.value = "";
    kelurahan.innerHTML =
      '<option value="" disabled selected>-- Pilih Kelurahan --</option>';
  }
}
