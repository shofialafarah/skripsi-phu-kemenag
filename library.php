<?php
// Fungsi untuk membalik tanggal dari format English (Y-M-D) -> Indo (D-M-Y)
function IndonesiaTgl($tanggal) {
    $tgl = substr($tanggal,8,2);
    $bln = substr($tanggal,5,2);
    $thn = substr($tanggal,0,4);
    $tanggal = "$tgl-$bln-$thn";
    return $tanggal;
}

// Fungsi untuk membuat format pada angka (uang)
function format_angka($angka) {
    $hasil = number_format($angka,0, ",",".");
    return $hasil;
}
?>
