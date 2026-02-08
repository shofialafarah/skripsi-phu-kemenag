<?php
include 'koneksi.php';

if (isset($_GET['id'])) {
    $id_batal_meninggal = $_GET['id'];

    // Query untuk mendapatkan data berdasarkan ID
    $query = "SELECT * FROM pembatalan_meninggal WHERE id_batal_meninggal = ?";
    $stmt = $koneksi->prepare($query);
    $stmt->bind_param("i", $id_batal_meninggal);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_assoc();

    if (!$data) {
        echo "Data tidak ditemukan.";
        exit();
    }
} else {
    echo "ID pembatalan tidak ditemukan.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Pembatalan Meninggal Dunia</title>
    <link rel="icon" href="logo_kemenag.png">
    <style>
        body {
    margin: 0;
    padding: 0;
    background: linear-gradient(45deg, #e8f5e9, #c8e6c9, #a5d6a7, #81c784);
    background-size: 400% 400%;
    animation: gradientBG 15s ease infinite;
    min-height: 100vh;
    overflow-x: hidden;
}

@keyframes gradientBG {
    0% {
        background-position: 0% 50%;
    }
    50% {
        background-position: 100% 50%;
    }
    100% {
        background-position: 0% 50%;
    }
}

.container {
    position: relative;
    z-index: 10;
    background-color: rgba(255, 255, 255, 0.9);
    backdrop-filter: blur(5px);
    border-radius: 15px;
    box-shadow: 0 8px 32px rgba(31, 38, 135, 0.37);
}

/* Optional: Add subtle floating elements */
.background-shape {
    position: absolute;
    background-color: rgba(76, 175, 80, 0.1);
    border-radius: 50%;
    animation: float 6s ease-in-out infinite;
}

@keyframes float {
    0%, 100% {
        transform: translateY(0);
    }
    50% {
        transform: translateY(-30px);
    }
}

h1 {
    text-align: center;
    color: #2e7d32;
    margin-bottom: 25px;
    font-weight: 600;
    border-bottom: 3px solid #4caf50;
    padding-bottom: 15px;
}

p {
    text-align: center;
    color: #388e3c;
    margin-bottom: 30px;
}

.btn-group {
    display: flex;
    justify-content: center;
    gap: 20px;
    flex-wrap: wrap;
}

.btn {
    display: inline-block;
    padding: 12px 25px;
    text-decoration: none;
    border-radius: 8px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 1px;
    transition: all 0.3s ease;
    
    /* Kemenag Green Gradient */
    background: linear-gradient(135deg, #2e7d32, #388e3c);
    color: white;
    
    /* Subtle shadow */
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.btn:hover {
    /* Darker green gradient on hover */
    background: linear-gradient(135deg, #1b5e20, #2e7d32);
    transform: translateY(-3px);
    box-shadow: 0 6px 8px rgba(0, 0, 0, 0.15);
}

.btn:active {
    transform: translateY(1px);
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

/* Responsive adjustments */
@media screen and (max-width: 600px) {
    body {
        padding: 20px;
    }
    
    .btn-group {
        flex-direction: column;
        align-items: center;
    }
    
    .btn {
        width: 100%;
        text-align: center;
        margin-bottom: 15px;
    }
}
.btn {
    position: relative;
    overflow: hidden;
}

.ripple {
    position: absolute;
    border-radius: 50%;
    transform: scale(0);
    animation: ripple 1s linear;
    background-color: rgba(255, 255, 255, 0.3);
}

@keyframes ripple {
    to {
        transform: scale(4);
        opacity: 0;
    }
}
    </style>
</head>
<body>
    <h1>Cetak Pembatalan Haji - Meninggal Dunia</h1>

    <p>Silakan pilih salah satu format surat untuk dicetak:</p>

    <div class="btn-group">
        <!-- Tombol untuk mencetak SPTJM -->
        <a href="sptjm_meninggal.php?id=<?php echo $id_batal_meninggal; ?>" class="btn" target="_blank">Cetak SPTJM</a>
        
        <!-- Tombol untuk mencetak Permohonan -->
        <a href="surat_permohonan_meninggal.php?id=<?php echo $id_batal_meninggal; ?>" class="btn" target="_blank">Cetak Surat Permohonan</a>
        
        <!-- Tombol untuk mencetak Surat Tanda Tangan Kasi -->
        <a href="surat_pembatalan_meninggal.php?id=<?php echo $id_batal_meninggal; ?>" class="btn" target="_blank">Cetak Surat Pembatalan</a>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
    // Button hover animation
    const buttons = document.querySelectorAll('.btn');
    
    buttons.forEach(button => {
        // Add ripple effect on hover
        button.addEventListener('mouseover', (e) => {
            const ripple = document.createElement('span');
            ripple.classList.add('ripple');
            
            const x = e.clientX - e.target.offsetLeft;
            const y = e.clientY - e.target.offsetTop;
            
            ripple.style.left = `${x}px`;
            ripple.style.top = `${y}px`;
            
            button.appendChild(ripple);
            
            // Remove ripple after animation
            setTimeout(() => {
                ripple.remove();
            }, 1000);
        });
    });
});
document.addEventListener('DOMContentLoaded', () => {
    function createBackgroundShapes() {
        const container = document.body;
        const shapesCount = 5;

        for (let i = 0; i < shapesCount; i++) {
            const shape = document.createElement('div');
            shape.classList.add('background-shape');

            // Random size
            const size = Math.random() * 100 + 50;
            shape.style.width = `${size}px`;
            shape.style.height = `${size}px`;

            // Random position
            shape.style.top = `${Math.random() * 100}%`;
            shape.style.left = `${Math.random() * 100}%`;

            // Custom animation delay
            shape.style.animationDelay = `${Math.random() * 3}s`;

            container.appendChild(shape);
        }
    }

    createBackgroundShapes();
});
    </script>
</body>
</html>
