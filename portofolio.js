document.addEventListener("DOMContentLoaded", function () {

    const content = document.getElementById("content");
    const links = document.querySelectorAll(".nav-link");
  
    const sections = {
      about: ` 
              <div class="fade-in">
                  <h2>Tentang Saya</h2>
                  <div style="text-align: left; max-width: 800px; margin: 0 auto;">
                      <p style="margin: 20px 0;">
                          👋 Halo! Saya <span class="typing-text"><b>Shofia Nabila Elfa Rahma</b> </span>, 
                          <span>mahasiswa di Universitas Islam Kalimantan Muhammad Arsyad al-Banjari.
                          Sebagai seseorang yang berawal dari dunia Tata Busana, saya kini mengeksplorasi dunia teknologi, khususnya pengembangan perangkat lunak.
                          Saya memiliki pengalaman dengan berbagai bahasa pemrograman seperti Pascal, C++, Java, PHP, Python.
                          Saat ini saya sedang mempelajari Node.js dengan database MongoDB, serta menggunakan Figma untuk mendesain antarmuka.
                      </p></span>
                      
                      <h3 style="margin: 20px 0;">🚀 Apa yang Saya Kerjakan</h3>
                      <ul style="list-style: none; padding: 0;">
                          <li style="margin: 10px 0;">💻 Frontend Development: HTML, CSS, JavaScript</li>
                          <li style="margin: 10px 0;">� Backend Development: Java, Python, C++, PHP</li>
                          <li style="margin: 10px 0;">🎨 UI/UX Design: Figma</li>
                          <li style="margin: 10px 0;">📱 Mobile Development: Flutter</li>
                      </ul>
  
                      <h3 style="margin: 20px 0;">🎯 Fokus Saya Saat Ini</h3>
                      <p>Saat ini, saya sedang fokus untuk mendalami desain antarmuka pengguna menggunakan Figma. Saya tertarik untuk menciptakan pengalaman visual yang menarik dan memudahkan pengguna dalam berinteraksi dengan aplikasi. Figma menjadi alat utama saya dalam membuat desain antarmuka yang tidak hanya estetik tetapi juga fungsional.</p>
                  </div>
              </div>
          `,
      projects: `
              <div class="fade-in">
                  <h2>Proyek Saya</h2>
                  <div class="projects-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 2rem; padding: 2rem 0; text-align: left;">
                    <div class="satu">
                        <div class="pertama">
                            
                        </div>
                        <div class="content" style="color: #666;">
                        </div>
                    </div>
                    <div class="dua">
                        <div class="kedua">
                            
                        </div>
                        <div class="content" style="color: #666;">
                        </div>
                    </div>
                    <div class="tiga">
                        <div class="ketiga">
                            
                        </div>
                        <div class="content" style="color: #666;">
                        </div>
                    </div>
                        <!-- Proyek 1 -->
                        <div class="project-card" style="border: 1px solid #eee; padding: 1.5rem; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
                            <h3 style="color: #2a2a2a;">🖼️ Aplikasi Penampil Gambar</h3>
                            <p style="margin: 10px 0;">Aplikasi untuk menampilkan gambar yang dipilih pengguna dari folder lokal mereka.</p>
                            <p style="color: #666; margin: 10px 0;">🛠️ Java (Swing for GUI)</p>
                            <div style="margin-top: 15px;">
                                <a href="https://github.com/shofialafarah/Aplikasi-Penampil_Gambar" style="color: #0066cc; text-decoration: none;">GitHub</a>
                            </div>
                        </div>
        
                        <!-- Proyek 2 -->
                        <div class="project-card" style="border: 1px solid #eee; padding: 1.5rem; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
                            <h3 style="color: #2a2a2a;">📦 Aplikasi Inventaris Barang</h3>
                            <p style="margin: 10px 0;">Aplikasi untuk mengelola daftar inventaris barang, dengan fitur penambahan dan penghapusan barang.</p>
                            <p style="color: #666; margin: 10px 0;">🛠️ Java (Console Application)</p>
                            <div style="margin-top: 15px;">
                                <a href="https://github.com/shofialafarah/Shofia_Nabila_Elfa_Rahma-2110010113-UTS" style="color: #0066cc; text-decoration: none;">GitHub</a>
                            </div>
                        </div>

                        <!-- Proyek 3 -->
                        <div class="project-card" style="border: 1px solid #eee; padding: 1.5rem; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
                            <h3 style="color: #2a2a2a;">📚 Aplikasi Pengelola KRS</h3>
                            <p style="margin: 10px 0;">Aplikasi untuk mengelola dan merencanakan KRS mahasiswa dengan fitur penambahan dan penghapusan mata kuliah.</p>
                            <p style="color: #666; margin: 10px 0;">🛠️ Java (Console Application)</p>
                            <div style="margin-top: 15px;">
                                <a href="https://github.com/shofialafarah/ShofiaNabilaElfaRahma-2110010113-TB" style="color: #0066cc; text-decoration: none;">GitHub</a>
                            </div>
                        </div>

                  </div>
              </div>
          `,
      contact: `
              <div class="fade-in">
                  <h2>Yuk, Ngobrol Bareng!</h2>
                  <div style="max-width: 600px; margin: 2rem auto; text-align: left;">
                      <p style="margin: 1rem 0;">Gak usah ragu buat reach out, ya! Saya selalu senang banget kalau bisa kolaborasi di proyek seru atau sekadar ngobrol tentang teknologi, desain, atau hal-hal keren lainnya. Bisa kontak saya via medsos berikut:</p>
                      
                      <div style="display: flex; flex-direction: column; gap: 1rem; margin: 2rem 0;">
                          <a href="mailto:shofia.lafarah74@gmail.com" style="color: #1a1a1a; text-decoration: none; display: flex; align-items: center; gap: 10px;">
                              📧 shofia.lafarah74@gmail.com
                          </a>
                          <a href="https://linkedin.com/in/shofia-nabila-elfa-rahma" style="color: #1a1a1a; text-decoration: none; display: flex; align-items: center; gap: 10px;">
                              💼 LinkedIn: /in/shofia-nabila-elfa-rahma
                          </a>
                          <a href="https://github.com/shofialafarah" style="color: #1a1a1a; text-decoration: none; display: flex; align-items: center; gap: 10px;">
                              🐱 GitHub: @shofialafarah
                          </a>
                          <a href="https://instagram.com/shofialafarah" style="color: #1a1a1a; text-decoration: none; display: flex; align-items: center; gap: 10px;">
                              🐦 Instagram: @shofialafarah
                          </a>
                      </div>
  
                      <div style="background: #f8f9fa; padding: 1.5rem; border-radius: 8px; margin-top: 2rem;">
                          <h3 style="margin-bottom: 1rem;">🌟 Status Saya</h3>
                          <p>Saat ini, saya terbuka untuk proyek freelance dan peluang kolaborasi menarik!</p>
                      </div>
                  </div>
              </div>
          `,
    };
  
    function showSection(sectionId) {
      content.innerHTML = sections[sectionId];
    }
  
    links.forEach((link) => {
      link.addEventListener("click", (e) => {
        e.preventDefault();
        showSection(link.dataset.section);
      });
    });
  
    showSection("about");
  });
  