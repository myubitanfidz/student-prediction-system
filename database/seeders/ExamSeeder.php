<?php

namespace Database\Seeders;

use App\Models\Exam;
use App\Models\Question;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class ExamSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Akun Admin / Guru (untuk koreksi esai)
        User::firstOrCreate(
            ['email' => 'guru@sekolah.sch.id'],
            [
                'name'     => 'Ustadz / Guru Penguji',
                'password' => 'password123',
                'role'     => 'admin',
            ]
        );

        // 2. Akun Dummy Santri (untuk pengetesan login & ujian)
        User::firstOrCreate(
            ['email' => 'santri@sekolah.sch.id'],
            [
                'name'     => 'Ahmad Santri',
                'password' => 'password123',
                'role'     => 'student',
            ]
        );

        // 3. Daftar Semua Ujian sesuai spesifikasi
        $examList = [
            // BAHASA
            [
                'category'    => 'Bahasa',
                'subcategory' => 'Inggris',
                'title'       => 'Tes Penempatan Bahasa Inggris',
                'description' => 'Evaluasi kemampuan dasar grammar, reading, dan writing.',
                'questions'   => [
                    [
                        'type'          => 'multiple_choice',
                        'question_text' => 'Choose the correct form: "She ___ to the library every morning."',
                        'options'       => ['goes', 'go', 'going', 'gone'],
                    ],
                    [
                        'type'          => 'essay',
                        'question_text' => 'Write a short paragraph (3-5 sentences) introducing your background and goals in this school.',
                        'options'       => null,
                    ],
                ],
            ],
            [
                'category'    => 'Bahasa',
                'subcategory' => 'Arab',
                'title'       => 'Tes Kemampuan Bahasa Arab',
                'description' => 'Ujian dasar qawaid dan pemahaman teks bahasa Arab.',
                'questions'   => [
                    [
                        'type'          => 'multiple_choice',
                        'question_text' => 'ما معنى كلمة "كِتَابٌ" باللغة الإندونيسية؟',
                        'options'       => ['Buku', 'Pena', 'Meja', 'Pintu'],
                    ],
                    [
                        'type'          => 'essay',
                        'question_text' => 'Tuliskan satu kalimat bahasa Arab tentang kegiatan sehari-hari beserta artinya.',
                        'options'       => null,
                    ],
                ],
            ],

            // IT
            [
                'category'    => 'IT',
                'subcategory' => 'Programming',
                'title'       => 'Ujian Logika & Pemrograman Dasar',
                'description' => 'Tes logika pemecahan masalah dan pemahaman dasar coding.',
                'questions'   => [
                    [
                        'type'          => 'multiple_choice',
                        'question_text' => 'Manakah struktur kontrol yang digunakan untuk perulangan bersyarat dalam pemrograman?',
                        'options'       => ['while loop', 'if-else', 'switch-case', 'try-catch'],
                    ],
                    [
                        'type'          => 'essay',
                        'question_text' => 'Jelaskan logika sederhana bagaimana cara mencari bilangan ganjil atau genap dalam sebuah algoritma!',
                        'options'       => null,
                    ],
                ],
            ],
            [
                'category'    => 'IT',
                'subcategory' => 'DKV',
                'title'       => 'Ujian Dasar Desain Komunikasi Visual',
                'description' => 'Prinsip tata letak, warna, dan komposisi visual.',
                'questions'   => [
                    [
                        'type'          => 'multiple_choice',
                        'question_text' => 'Format warna yang digunakan untuk kebutuhan cetak adalah...',
                        'options'       => ['CMYK', 'RGB', 'HEX', 'HSL'],
                    ],
                    [
                        'type'          => 'essay',
                        'question_text' => 'Jelaskan fungsi dari White Space (ruang kosong) dalam desain grafis!',
                        'options'       => null,
                    ],
                ],
            ],
            [
                'category'    => 'IT',
                'subcategory' => 'DKF',
                'title'       => 'Ujian Dasar Fotografi (DKF)',
                'description' => 'Pemahaman eksposur, segitiga exposure, dan sudut pandang kamera.',
                'questions'   => [
                    [
                        'type'          => 'multiple_choice',
                        'question_text' => 'Komponen Segitiga Exposure terdiri dari...',
                        'options'       => ['ISO, Shutter Speed, Aperture', 'ISO, Frame Rate, White Balance', 'Contrast, Saturation, Brightness', 'Focal Length, Zoom, Focus'],
                    ],
                    [
                        'type'          => 'essay',
                        'question_text' => 'Kapan kita perlu menggunakan Shutter Speed tinggi? Berikan contoh situasinya!',
                        'options'       => null,
                    ],
                ],
            ],
            [
                'category'    => 'IT',
                'subcategory' => 'Komik',
                'title'       => 'Ujian Pembuatan Komik & Ilustrasi',
                'description' => 'Paneling, alur cerita visual, dan desain karakter.',
                'questions'   => [
                    [
                        'type'          => 'multiple_choice',
                        'question_text' => 'Tata letak kotak-kotak pembagi adegan dalam komik biasa disebut...',
                        'options'       => ['Panel', 'Bubble', 'Storyboard', 'Grid'],
                    ],
                    [
                        'type'          => 'essay',
                        'question_text' => 'Bagaimana cara menyampaikan emosi sedih/marah karakter hanya lewat sudut gambar (angle) atau ekspresi visual?',
                        'options'       => null,
                    ],
                ],
            ],
            [
                'category'    => 'IT',
                'subcategory' => 'Videografi',
                'title'       => 'Ujian Dasar Videografi & Editing',
                'description' => 'Teknik pengambilan gambar video dan alur pascaproduksi.',
                'questions'   => [
                    [
                        'type'          => 'multiple_choice',
                        'question_text' => 'Pergerakan kamera ke kiri dan ke kanan secara horizontal di atas tripod disebut...',
                        'options'       => ['Pan', 'Tilt', 'Zoom', 'Roll'],
                    ],
                    [
                        'type'          => 'essay',
                        'question_text' => 'Jelaskan perbedaan mendasar antara B-Roll dan A-Roll dalam video dokumenter/konten!',
                        'options'       => null,
                    ],
                ],
            ],

            // KARAKTER
            [
                'category'    => 'Karakter',
                'subcategory' => 'Pemahaman Aqidah & Akhlak',
                'title'       => 'Tes Pemahaman Aqidah & Akhlak',
                'description' => 'Penilaian adab, akhlak keseharian, dan dasar-dasar tauhid.',
                'questions'   => [
                    [
                        'type'          => 'multiple_choice',
                        'question_text' => 'Adab yang baik ketika berbicara kepada guru atau orang tua adalah...',
                        'options'       => ['Menatap santun dan berbicara dengan nada lembut', 'Memotong pembicaraan bila tidak setuju', 'Berbicara dengan suara keras agar jelas', 'Bermain gawai saat didengarkan'],
                    ],
                    [
                        'type'          => 'essay',
                        'question_text' => 'Bagaimana sikap yang Anda ambil jika terjadi perselisihan pendapat dengan teman satu asrama/kelas?',
                        'options'       => null,
                    ],
                ],
            ],
        ];

        // 4. Masukkan ke Database
        foreach ($examList as $item) {
            $exam = Exam::create([
                'category'    => $item['category'],
                'subcategory' => $item['subcategory'],
                'title'       => $item['title'],
                'description' => $item['description'],
            ]);

            foreach ($item['questions'] as $q) {
                Question::create([
                    'exam_id'       => $exam->id,
                    'type'          => $q['type'],
                    'question_text' => $q['question_text'],
                    'options'       => $q['options'],
                ]);
            }
        }
    }
}