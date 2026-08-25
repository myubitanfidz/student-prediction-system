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
        User::firstOrCreate(['email' => 'guru@sekolah.sch.id'], [
            'name' => 'Ustadz / Guru Penguji', 'password' => 'password123', 'role' => 'admin',
        ]);
        User::firstOrCreate(['email' => 'guru2@sekolah.sch.id'], [
            'name' => 'Bu Guru (Teacher)', 'password' => 'password123', 'role' => 'teacher',
        ]);
        User::firstOrCreate(['email' => 'santri@sekolah.sch.id'], [
            'name' => 'Ahmad Santri', 'password' => 'password123', 'role' => 'student',
        ]);

        $examList = [
            ['category' => 'Bahasa', 'subcategory' => 'Inggris', 'title' => 'Tes Penempatan Bahasa Inggris', 'description' => 'Grammar, reading, dan writing dasar.', 'duration_minutes' => 30, 'questions' => [
                ['category' => null, 'type' => 'multiple_choice', 'question_text' => 'Choose the correct form: “She ___ to the library every morning.”', 'options' => ['goes', 'go', 'going', 'gone'], 'correct_answer' => 'goes'],
                ['category' => null, 'type' => 'essay', 'question_text' => 'Write 3–5 sentences introducing yourself and one learning goal.', 'options' => null, 'correct_answer' => null],
            ]],
            ['category' => 'Bahasa', 'subcategory' => 'Arab', 'title' => 'Tes Kemampuan Bahasa Arab', 'description' => 'Kosakata dan penyusunan kalimat sederhana.', 'duration_minutes' => 30, 'questions' => [
                ['category' => null, 'type' => 'multiple_choice', 'question_text' => 'Apa arti kata Arab “كِتَابٌ”?', 'options' => ['Buku', 'Pena', 'Meja', 'Pintu'], 'correct_answer' => 'Buku'],
                ['category' => null, 'type' => 'essay', 'question_text' => 'Tuliskan satu kalimat bahasa Arab tentang kegiatan sehari-hari beserta artinya.', 'options' => null, 'correct_answer' => null],
            ]],
            
            // THE GCLWAMA SUPER TEST
            // THE GCLWAMA SUPER TEST (21 Questions)
            ['category' => 'IT', 'subcategory' => 'GCLWAMA', 'title' => 'Ujian Fundamental IT (GCLWAMA)', 'description' => 'Kombinasi Gambar, Cerita, Layout, Warna, Animasi, Matematika, dan Algoritma untuk SPS.', 'duration_minutes' => 30, 'questions' => [
                
                // 1. GAMBAR (Drawing) - 3 Questions
                ['category' => 'Gambar', 'type' => 'multiple_choice', 'question_text' => 'Apa tujuan utama dari membuat sketsa (sketching) sebelum menggambar karya akhir?', 'options' => ['Menentukan komposisi dan bentuk dasar', 'Memberikan warna langsung', 'Menghapus latar belakang', 'Membuat gambar menjadi 3D'], 'correct_answer' => 'Menentukan komposisi dan bentuk dasar'],
                ['category' => 'Gambar', 'type' => 'multiple_choice', 'question_text' => 'Dalam menggambar, titik di mana garis-garis sejajar tampak bertemu di kejauhan disebut...', 'options' => ['Titik Hilang (Vanishing Point)', 'Titik Fokus', 'Garis Horizon', 'Titik Buta'], 'correct_answer' => 'Titik Hilang (Vanishing Point)'],
                ['category' => 'Gambar', 'type' => 'multiple_choice', 'question_text' => 'Fungsi utama memberikan arsiran (shading) pada sebuah gambar adalah...', 'options' => ['Memberikan kesan volume dan 3D', 'Memperjelas garis tepi (outline)', 'Menghapus kesalahan', 'Membuat gambar menjadi abstrak'], 'correct_answer' => 'Memberikan kesan volume dan 3D'],

                // 2. CERITA (Storytelling) - 3 Essays
                ['category' => 'Cerita', 'type' => 'essay', 'question_text' => 'Tuliskan sebuah cerita pendek (1-2 paragraf) tentang seorang karakter yang menemukan pintu rahasia di kamarnya.', 'options' => null, 'correct_answer' => null],
                ['category' => 'Cerita', 'type' => 'essay', 'question_text' => 'Deskripsikan secara detail penampilan fisik dan sifat dari karakter utama yang baru saja kamu buat di pertanyaan sebelumnya.', 'options' => null, 'correct_answer' => null],
                ['category' => 'Cerita', 'type' => 'essay', 'question_text' => 'Buatlah sebuah dialog singkat antara karakter utamamu dengan penjaga pintu rahasia tersebut.', 'options' => null, 'correct_answer' => null],

                // 3. LAYOUT (Tata Letak) - 3 Questions
                ['category' => 'Layout', 'type' => 'multiple_choice', 'question_text' => 'Dalam desain tata letak, area kosong di antara elemen-elemen desain disebut...', 'options' => ['White space / Negative space', 'Grid system', 'Typography', 'Margin border'], 'correct_answer' => 'White space / Negative space'],
                ['category' => 'Layout', 'type' => 'multiple_choice', 'question_text' => 'Prinsip mengarahkan mata pembaca ke elemen paling penting terlebih dahulu disebut...', 'options' => ['Hierarki Visual', 'Kontras', 'Keseimbangan', 'Proporsi'], 'correct_answer' => 'Hierarki Visual'],
                ['category' => 'Layout', 'type' => 'multiple_choice', 'question_text' => 'Membagi bidang desain menjadi 9 kotak yang sama besar untuk menempatkan objek utama disebut aturan...', 'options' => ['Rule of Thirds', 'Golden Ratio', 'Symmetry', 'Alignment'], 'correct_answer' => 'Rule of Thirds'],

                // 4. WARNA (Colouring) - 3 Questions
                ['category' => 'Warna', 'type' => 'multiple_choice', 'question_text' => 'Manakah di bawah ini yang merupakan warna primer murni?', 'options' => ['Merah, Kuning, Biru', 'Hijau, Oranye, Ungu', 'Hitam, Putih, Abu-abu', 'Cyan, Magenta, Cokelat'], 'correct_answer' => 'Merah, Kuning, Biru'],
                ['category' => 'Warna', 'type' => 'multiple_choice', 'question_text' => 'Mode warna yang standar digunakan untuk tampilan layar digital (monitor/HP) adalah...', 'options' => ['RGB', 'CMYK', 'Grayscale', 'Pantone'], 'correct_answer' => 'RGB'],
                ['category' => 'Warna', 'type' => 'multiple_choice', 'question_text' => 'Dua warna yang saling berhadapan di dalam roda warna (color wheel) disebut warna...', 'options' => ['Komplementer', 'Analogus', 'Monokromatik', 'Tersier'], 'correct_answer' => 'Komplementer'],

                // 5. ANIMASI (Animation) - 3 Questions
                ['category' => 'Animasi', 'type' => 'multiple_choice', 'question_text' => 'Jumlah gambar statis yang ditampilkan per detik untuk menciptakan ilusi gerakan disebut...', 'options' => ['Frame Rate (FPS)', 'Resolusi', 'Aspect Ratio', 'Keyframe'], 'correct_answer' => 'Frame Rate (FPS)'],
                ['category' => 'Animasi', 'type' => 'multiple_choice', 'question_text' => 'Prinsip animasi yang membuat objek terlihat lentur, seperti bola yang memipih saat jatuh ke tanah, disebut...', 'options' => ['Squash and Stretch', 'Anticipation', 'Timing', 'Exaggeration'], 'correct_answer' => 'Squash and Stretch'],
                ['category' => 'Animasi', 'type' => 'multiple_choice', 'question_text' => 'Sketsa berurutan yang digunakan untuk merencanakan alur adegan animasi sebelum dibuat disebut...', 'options' => ['Storyboard', 'Script', 'Rigging', 'Rendering'], 'correct_answer' => 'Storyboard'],

                // 6. MATEMATIKA (Math) - 3 Questions
                ['category' => 'Matematika', 'type' => 'multiple_choice', 'question_text' => 'Berapakah hasil dari operasi perhitungan berikut: 10 + 5 x 2 ?', 'options' => ['20', '30', '17', '15'], 'correct_answer' => '20'],
                ['category' => 'Matematika', 'type' => 'multiple_choice', 'question_text' => 'Jika sebuah persegi panjang memiliki panjang 8 cm dan lebar 4 cm, berapakah luasnya?', 'options' => ['32 cm²', '24 cm²', '12 cm²', '16 cm²'], 'correct_answer' => '32 cm²'],
                ['category' => 'Matematika', 'type' => 'multiple_choice', 'question_text' => 'Berapakah 20% dari angka 150?', 'options' => ['30', '20', '15', '50'], 'correct_answer' => '30'],

                // 7. ALGORITMA (Algorithm) - 3 Questions
                ['category' => 'Algoritma', 'type' => 'multiple_choice', 'question_text' => 'Dalam dasar pemrograman, apa itu sebuah "Variabel"?', 'options' => ['Tempat atau wadah untuk menyimpan nilai dan data', 'Perintah untuk menghentikan program', 'Error di dalam kode sintaks', 'Desain tampilan antarmuka (UI)'], 'correct_answer' => 'Tempat atau wadah untuk menyimpan nilai dan data'],
                ['category' => 'Algoritma', 'type' => 'multiple_choice', 'question_text' => 'Struktur logika yang digunakan untuk membuat keputusan "Jika A terjadi, maka lakukan B" disebut...', 'options' => ['If-Else (Pengkondisian)', 'Loop (Perulangan)', 'Array', 'Function'], 'correct_answer' => 'If-Else (Pengkondisian)'],
                ['category' => 'Algoritma', 'type' => 'multiple_choice', 'question_text' => 'Jika kita ingin komputer mencetak teks "Halo" sebanyak 100 kali tanpa menulis kodenya 100 kali, kita menggunakan...', 'options' => ['Looping (Perulangan)', 'Variabel', 'Database', 'Kalkulator'], 'correct_answer' => 'Looping (Perulangan)'],
            ]],
        ];

        foreach ($examList as $item) {
            $exam = Exam::updateOrCreate(
                ['category' => $item['category'], 'subcategory' => $item['subcategory']],
                ['title' => $item['title'], 'description' => $item['description'], 'duration_minutes' => $item['duration_minutes'] ?? 30],
            );

            foreach ($item['questions'] as $question) {
                Question::updateOrCreate(
                    ['exam_id' => $exam->id, 'question_text' => $question['question_text']],
                    ['type' => $question['type'], 'options' => $question['options'], 'correct_answer' => $question['correct_answer'], 'category' => $question['category'] ?? null],
                );
            }
        }
    }
}