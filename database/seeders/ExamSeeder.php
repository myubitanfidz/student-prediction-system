<?php

namespace Database\Seeders;

use App\Models\Exam;
use App\Models\Question;
use App\Models\User;
use Illuminate\Database\Seeder;

class ExamSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Akun Default Penguji & Santri
        User::firstOrCreate(['email' => 'guru@sch'], [
            'name'     => 'Ustadz / Guru Penguji',
            'password' => 'password123',
            'role'     => 'admin',
        ]);
        User::firstOrCreate(['email' => 'guru2@sch'], [
            'name'     => 'Bu Guru (Teacher)',
            'password' => 'password123',
            'role'     => 'teacher',
        ]);
        User::firstOrCreate(['email' => 'santri@sch'], [
            'name'     => 'Ahmad Santri',
            'password' => 'password123',
            'role'     => 'student',
        ]);

        // 2. Daftar Ujian & Butir Soal Terpadu
        $examList = [
            // ==================== UJIAN BAHASA INGGRIS ====================
            [
                'category'         => 'Bahasa',
                'subcategory'      => 'Inggris',
                'title'            => 'Tes Penempatan Bahasa Inggris',
                'description'      => 'Ujian penempatan grammar, reading, dan writing dasar.',
                'duration_minutes' => 30,
                'questions'        => [
                    [
                        'gclwama_tag'    => null,
                        'type'           => 'multiple_choice',
                        'question_text'  => 'Choose the correct form: “She ___ to the library every morning.”',
                        'options'        => ['goes', 'go', 'going', 'gone'],
                        'correct_answer' => 'goes',
                    ],
                    [
                        'gclwama_tag'    => null,
                        'type'           => 'multiple_choice',
                        'question_text'  => 'Which sentence is in the Past Tense?',
                        'options'        => ['He wrote a letter yesterday', 'He writes a letter', 'He is writing a letter', 'He will write a letter'],
                        'correct_answer' => 'He wrote a letter yesterday',
                    ],
                    [
                        'gclwama_tag'    => null,
                        'type'           => 'essay',
                        'question_text'  => 'Write 3–5 sentences introducing yourself, your hobby, and your main learning goal.',
                        'options'        => null,
                        'correct_answer' => null,
                    ],
                ],
            ],

            // ==================== UJIAN BAHASA ARAB ====================
            [
                'category'         => 'Bahasa',
                'subcategory'      => 'Arab',
                'title'            => 'Tes Kemampuan Bahasa Arab',
                'description'      => 'Ujian pemahaman kosakata, kaidah nahwu-sharaf, dan insya sederhana.',
                'duration_minutes' => 30,
                'questions'        => [
                    [
                        'gclwama_tag'    => null,
                        'type'           => 'multiple_choice',
                        'question_text'  => 'Apa arti dari kata bahasa Arab “كِتَابٌ”?',
                        'options'        => ['Buku', 'Pena', 'Meja', 'Pintu'],
                        'correct_answer' => 'Buku',
                    ],
                    [
                        'gclwama_tag'    => null,
                        'type'           => 'multiple_choice',
                        'question_text'  => 'Manakah susunan jumlah ismiyyah yang benar di bawah ini?',
                        'options'        => ['الطَّالِبُ نَشِيْطٌ', 'نَشِيْطٌ الطَّالِبُ', 'يَكْتُبُ الطَّالِبُ', 'طَالِبٌ فِي المَدْرَسَةِ'],
                        'correct_answer' => 'الطَّالِبُ نَشِيْطٌ',
                    ],
                    [
                        'gclwama_tag'    => null,
                        'type'           => 'essay',
                        'question_text'  => 'Tuliskan 2 kalimat bahasa Arab tentang kegiatan sehari-hari di pesantren beserta terjemahannya.',
                        'options'        => null,
                        'correct_answer' => null,
                    ],
                ],
            ],

            // ==================== THE GCLWAMA SUPER TEST (IT) ====================
            [
                'category'         => 'IT',
                'subcategory'      => 'GCLWAMA',
                'title'            => 'Ujian Pemetaan Bakat IT (GCLWAMA)',
                'description'      => 'Tes pemetaan minat dan bakat meliputi Gambar, Cerita, Layout, Warna, Animasi, Matematika, dan Algoritma.',
                'duration_minutes' => 45,
                'questions'        => [

                    // 1. G - GAMBAR (Foto / Image Upload Tugas)
                    [
                        'gclwama_tag'    => 'G',
                        'type'           => 'image_upload',
                        'question_text'  => 'Buatlah sketsa karakter/objek sederhana di kertas menggunakan pensil/pulpen, lalu foto dan unggah hasilnya di sini.',
                        'options'        => null,
                        'correct_answer' => null,
                    ],
                    [
                        'gclwama_tag'    => 'G',
                        'type'           => 'multiple_choice',
                        'question_text'  => 'Dalam perspektif menggambar, titik di mana garis-garis sejajar tampak menyatu di kejauhan disebut...',
                        'options'        => ['Titik Hilang (Vanishing Point)', 'Titik Fokus', 'Garis Horizon', 'Titik Buta'],
                        'correct_answer' => 'Titik Hilang (Vanishing Point)',
                    ],
                    [
                        'gclwama_tag'    => 'G',
                        'type'           => 'multiple_choice',
                        'question_text'  => 'Fungsi utama memberikan teknik arsiran (shading) pada sketsa objek adalah...',
                        'options'        => ['Memberikan kesan volume, dimensi, dan pencahayaan', 'Menghilangkan garis bentuk dasar', 'Membuat gambar menjadi abstrak', 'Menyamarkan kesalahan garis'],
                        'correct_answer' => 'Memberikan kesan volume, dimensi, dan pencahayaan',
                    ],

                    // 2. C - CERITA (Storytelling / Essay)
                    [
                        'gclwama_tag'    => 'C',
                        'type'           => 'essay',
                        'question_text'  => 'Tuliskan cerita pendek (1-2 paragraf) tentang seorang santri yang menemukan komputer tua misterius yang dapat memprogram masa depan.',
                        'options'        => null,
                        'correct_answer' => null,
                    ],
                    [
                        'gclwama_tag'    => 'C',
                        'type'           => 'essay',
                        'question_text'  => 'Deskripsikan secara singkat sifat, kepribadian, dan ciri khas dari tokoh utama dalam cerita yang baru saja kamu tulis.',
                        'options'        => null,
                        'correct_answer' => null,
                    ],

                    // 3. L - LAYOUT (Tata Letak Desain)
                    [
                        'gclwama_tag'    => 'L',
                        'type'           => 'multiple_choice',
                        'question_text'  => 'Dalam tata letak desain grafis, area kosong di sekitar teks atau elemen grafis yang memberi ruang bernapas disebut...',
                        'options'        => ['White Space / Negative Space', 'Grid Border', 'Margin Offset', 'Bleed Area'],
                        'correct_answer' => 'White Space / Negative Space',
                    ],
                    [
                        'gclwama_tag'    => 'L',
                        'type'           => 'multiple_choice',
                        'question_text'  => 'Prinsip desain yang mengatur ukuran, kontras, dan penempatan elemen agar mata audiens melihat info paling penting terlebih dahulu disebut...',
                        'options'        => ['Hierarki Visual', 'Simetri', 'Proporsi', 'Keseimbangan Statis'],
                        'correct_answer' => 'Hierarki Visual',
                    ],
                    [
                        'gclwama_tag'    => 'L',
                        'type'           => 'multiple_choice',
                        'question_text'  => 'Aturan komposisi visual yang membagi layar menjadi 9 kotak simetris untuk menentukan titik fokus objek disebut...',
                        'options'        => ['Rule of Thirds', 'Golden Ratio', 'Diagonal Alignment', 'Center Focal'],
                        'correct_answer' => 'Rule of Thirds',
                    ],

                    // 4. W - WARNA (Color Harmony & Modes)
                    [
                        'gclwama_tag'    => 'W',
                        'type'           => 'multiple_choice',
                        'question_text'  => 'Format mode warna standar yang digunakan untuk tampilan media layar digital (HP, monitor, web) adalah...',
                        'options'        => ['RGB', 'CMYK', 'Grayscale', 'Pantone Spot'],
                        'correct_answer' => 'RGB',
                    ],
                    [
                        'gclwama_tag'    => 'W',
                        'type'           => 'multiple_choice',
                        'question_text'  => 'Dua warna yang posisinya saling berseberangan langsung pada roda warna (color wheel) dinamakan warna...',
                        'options'        => ['Komplementer', 'Analogus', 'Monokromatik', 'Triadik'],
                        'correct_answer' => 'Komplementer',
                    ],
                    [
                        'gclwama_tag'    => 'W',
                        'type'           => 'multiple_choice',
                        'question_text'  => 'Kombinasi warna merah, kuning, dan biru dalam teori warna dasar digolongkan sebagai...',
                        'options'        => ['Warna Primer', 'Warna Sekunder', 'Warna Tersier', 'Warna Netral'],
                        'correct_answer' => 'Warna Primer',
                    ],

                    // 5. A - ANIMASI (Animation Principles & Motion)
                    [
                        'gclwama_tag'    => 'A_animasi',
                        'type'           => 'multiple_choice',
                        'question_text'  => 'Jumlah frame gambar individual yang ditampilkan dalam rentang satu detik untuk menghasilkan ilusi gerakan disebut...',
                        'options'        => ['FPS (Frames Per Second)', 'DPI (Dots Per Inch)', 'Aspect Ratio', 'Keyframe Interval'],
                        'correct_answer' => 'FPS (Frames Per Second)',
                    ],
                    [
                        'gclwama_tag'    => 'A_animasi',
                        'type'           => 'multiple_choice',
                        'question_text'  => 'Prinsip animasi fundamental yang memberikan efek kelenturan dan elastisitas ketika sebuah objek bergerak atau menabrak bidang datar adalah...',
                        'options'        => ['Squash and Stretch', 'Anticipation', 'Staging', 'Slow In and Slow Out'],
                        'correct_answer' => 'Squash and Stretch',
                    ],
                    [
                        'gclwama_tag'    => 'A_animasi',
                        'type'           => 'multiple_choice',
                        'question_text'  => 'Rangkaian gambar sketsa berurutan yang memetakan alur adegan video/animasi sebelum diproduksi dinamakan...',
                        'options'        => ['Storyboard', 'Timeline Sequence', 'Rendering Map', 'Moodboard'],
                        'correct_answer' => 'Storyboard',
                    ],

                    // 6. M - MATEMATIKA (Logika Angka)
                    [
                        'gclwama_tag'    => 'M',
                        'type'           => 'multiple_choice',
                        'question_text'  => 'Berapakah hasil dari operasi perhitungan urutan operator berikut: 12 + 6 × 3 - 4 ?',
                        'options'        => ['26', '50', '22', '34'],
                        'correct_answer' => '26',
                    ],
                    [
                        'gclwama_tag'    => 'M',
                        'type'           => 'multiple_choice',
                        'question_text'  => 'Jika sebuah gambar beresolusi 1920 × 1080 piksel, rasio aspek (aspect ratio) perbandingannya adalah...',
                        'options'        => ['16:9', '4:3', '21:9', '1:1'],
                        'correct_answer' => '16:9',
                    ],
                    [
                        'gclwama_tag'    => 'M',
                        'type'           => 'multiple_choice',
                        'question_text'  => 'Berapakah nilai 25% dari total 240 file aset grafis?',
                        'options'        => ['60', '48', '75', '80'],
                        'correct_answer' => '60',
                    ],

                    // 7. A - ALGORITMA (Computational Thinking & Code Flow)
                    [
                        'gclwama_tag'    => 'A_algoritma',
                        'type'           => 'multiple_choice',
                        'question_text'  => 'Dalam pemrograman dasar, sebuah container penampung memori untuk menyimpan nilai atau data yang dapat berubah disebut...',
                        'options'        => ['Variabel', 'Konstanta Statis', 'Tipe Data Void', 'Loop Counter'],
                        'correct_answer' => 'Variabel',
                    ],
                    [
                        'gclwama_tag'    => 'A_algoritma',
                        'type'           => 'multiple_choice',
                        'question_text'  => 'Struktur kendali logika percabangan untuk mengeksekusi instruksi tertentu hanya jika syarat bernilai benar (True) adalah...',
                        'options'        => ['If - Else', 'While Loop', 'Array Index', 'Function Callback'],
                        'correct_answer' => 'If - Else',
                    ],
                    [
                        'gclwama_tag'    => 'A_algoritma',
                        'type'           => 'multiple_choice',
                        'question_text'  => 'Proses menjalankan baris kode yang sama secara berulang-ulang hingga kondisi terminasi terpenuhi disebut...',
                        'options'        => ['Looping (Perulangan)', 'Inheritance', 'Parsing', 'Compiling'],
                        'correct_answer' => 'Looping (Perulangan)',
                    ],
                ],
            ],
        ];

        // 3. Masukkan ke Database
        foreach ($examList as $item) {
            $exam = Exam::updateOrCreate(
                [
                    'category'    => $item['category'],
                    'subcategory' => $item['subcategory'],
                ],
                [
                    'title'            => $item['title'],
                    'description'      => $item['description'],
                    'duration_minutes' => $item['duration_minutes'] ?? 30,
                ]
            );

            foreach ($item['questions'] as $question) {
                Question::updateOrCreate(
                    [
                        'exam_id'       => $exam->id,
                        'question_text' => $question['question_text'],
                    ],
                    [
                        'gclwama_tag'    => $question['gclwama_tag'],
                        'type'           => $question['type'],
                        'options'        => $question['options'],
                        'correct_answer' => $question['correct_answer'],
                    ]
                );
            }
        }
    }
}