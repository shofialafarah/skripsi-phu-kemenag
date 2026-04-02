/** =============================================================================
 * Nama Aplikasi: Sistem Informasi Pelayanan Ibadah Haji Berbasis Web pada Kementerian Agama Kabupaten Banjar
 * Author: SHOFIA NABILA ELFA RAHMA - 2110010113
 * Copyright (c) 2025. All Rights Reserved.
 * Dibuat untuk keperluan Skripsi di Universitas Islam Kalimantan Muhammad Arsyad Al Banjari Banjarmasin
 * ==============================================================================
 */
document.addEventListener("DOMContentLoaded", function () {
  const apiKey = "10fc461d891b997b984bfd3e8114334b";
  const city = "Martapura,ID";
  const url = `https://api.openweathermap.org/data/2.5/weather?q=${city}&appid=${apiKey}&units=metric`;

  fetch(url)
    .then((response) => {
      if (!response.ok) {
        throw new Error("Gagal memuat data: " + response.status);
      }
      return response.json();
    })
    .then((data) => {
      if (data && data.main) {
        const temp = Math.round(data.main.temp);
        const humidity = data.main.humidity;
        const pressure = data.main.pressure;
        const wind = data.wind.speed;
        const icon = data.weather[0].icon;

        document.querySelector(".temperature").textContent = `${temp}°C`;
        document.querySelector(".date-info").textContent =
          "Martapura, Kalimantan Selatan";
      }
    })
    .catch((error) => {
      console.error("Gagal ambil data cuaca:", error);
      document.querySelector(".temperature").textContent = "--°C";
    });
});
