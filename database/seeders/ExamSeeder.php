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
        User::firstOrCreate(['email' => 'santri@sekolah.sch.id'], [
            'name' => 'Ahmad Santri', 'password' => 'password123', 'role' => 'student',
        ]);

        // This is the editable initial question bank. Add or revise an item here,
        // then run `php artisan db:seed --class=ExamSeeder` during development.
        $examList = [
            ['category' => 'Bahasa', 'subcategory' => 'Inggris', 'title' => 'Tes Penempatan Bahasa Inggris', 'description' => 'Grammar, reading, dan writing dasar.', 'questions' => [
                ['type' => 'multiple_choice', 'question_text' => 'Choose the correct form: “She ___ to the library every morning.”', 'options' => ['goes', 'go', 'going', 'gone'], 'correct_answer' => 'goes'],
                ['type' => 'essay', 'question_text' => 'Write 3–5 sentences introducing yourself and one learning goal.', 'options' => null, 'correct_answer' => null],
            ]],
            ['category' => 'Bahasa', 'subcategory' => 'Arab', 'title' => 'Tes Kemampuan Bahasa Arab', 'description' => 'Kosakata dan penyusunan kalimat sederhana.', 'questions' => [
                ['type' => 'multiple_choice', 'question_text' => 'Apa arti kata Arab “كِتَابٌ”?', 'options' => ['Buku', 'Pena', 'Meja', 'Pintu'], 'correct_answer' => 'Buku'],
                ['type' => 'essay', 'question_text' => 'Tuliskan satu kalimat bahasa Arab tentang kegiatan sehari-hari beserta artinya.', 'options' => null, 'correct_answer' => null],
            ]],
            ['category' => 'IT', 'subcategory' => 'Programming', 'title' => 'Ujian Logika & Pemrograman Dasar', 'description' => 'Logika pemecahan masalah dan konsep coding dasar.', 'questions' => [
                ['type' => 'multiple_choice', 'question_text' => 'Struktur yang mengulang instruksi selama sebuah kondisi bernilai benar adalah...', 'options' => ['while loop', 'if-else', 'switch-case', 'try-catch'], 'correct_answer' => 'while loop'],
                ['type' => 'essay', 'question_text' => 'Jelaskan langkah algoritma untuk menentukan sebuah angka ganjil atau genap.', 'options' => null, 'correct_answer' => null],
            ]],
            ['category' => 'IT', 'subcategory' => 'Videografi', 'title' => 'Ujian Dasar Videografi', 'description' => 'Bahasa visual, gerak kamera, dan alur video.', 'questions' => [
                ['type' => 'multiple_choice', 'question_text' => 'Gerakan kamera ke kiri atau kanan secara horizontal disebut...', 'options' => ['Pan', 'Tilt', 'Zoom', 'Roll'], 'correct_answer' => 'Pan'],
                ['type' => 'essay', 'question_text' => 'Jelaskan perbedaan A-roll dan B-roll dalam sebuah video.', 'options' => null, 'correct_answer' => null],
            ]],
            ['category' => 'IT', 'subcategory' => 'DKV', 'title' => 'Ujian Dasar DKV', 'description' => 'Warna, tipografi, dan komunikasi visual.', 'questions' => [
                ['type' => 'multiple_choice', 'question_text' => 'Mode warna yang umum digunakan untuk kebutuhan cetak adalah...', 'options' => ['CMYK', 'RGB', 'HEX', 'HSL'], 'correct_answer' => 'CMYK'],
                ['type' => 'essay', 'question_text' => 'Mengapa white space penting dalam sebuah desain?', 'options' => null, 'correct_answer' => null],
            ]],
            ['category' => 'IT', 'subcategory' => 'DKF', 'title' => 'Ujian Dasar Fotografi (DKF)', 'description' => 'Exposure, komposisi, dan teknik kamera.', 'questions' => [
                ['type' => 'multiple_choice', 'question_text' => 'Tiga komponen exposure triangle adalah...', 'options' => ['ISO, shutter speed, aperture', 'ISO, frame rate, white balance', 'Kontras, saturasi, brightness', 'Focal length, zoom, fokus'], 'correct_answer' => 'ISO, shutter speed, aperture'],
                ['type' => 'essay', 'question_text' => 'Kapan shutter speed tinggi diperlukan? Beri satu contoh.', 'options' => null, 'correct_answer' => null],
            ]],
            ['category' => 'IT', 'subcategory' => 'Komik', 'title' => 'Ujian Komik & Ilustrasi', 'description' => 'Panel, alur visual, dan ekspresi karakter.', 'questions' => [
                ['type' => 'multiple_choice', 'question_text' => 'Kotak pembagi adegan dalam komik disebut...', 'options' => ['Panel', 'Bubble', 'Storyboard', 'Grid'], 'correct_answer' => 'Panel'],
                ['type' => 'essay', 'question_text' => 'Bagaimana sudut gambar atau ekspresi visual dapat menunjukkan emosi karakter?', 'options' => null, 'correct_answer' => null],
            ]],
            ['category' => 'IT', 'subcategory' => 'Animasi', 'title' => 'Ujian Dasar Animasi', 'description' => 'Prinsip gerak dan perencanaan adegan.', 'questions' => [
                ['type' => 'multiple_choice', 'question_text' => 'Storyboard digunakan terutama untuk...', 'options' => ['Merencanakan urutan visual adegan', 'Merekam suara', 'Menghapus latar', 'Mengekspor video'], 'correct_answer' => 'Merencanakan urutan visual adegan'],
                ['type' => 'essay', 'question_text' => 'Jelaskan mengapa timing penting agar gerakan animasi terasa meyakinkan.', 'options' => null, 'correct_answer' => null],
            ]],
            ['category' => 'Karakter', 'subcategory' => 'Akhlak', 'title' => 'Tes Akhlak', 'description' => 'Sikap baik dalam kehidupan sehari-hari.', 'questions' => [
                ['type' => 'multiple_choice', 'question_text' => 'Sikap yang tepat saat berbeda pendapat dengan teman adalah...', 'options' => ['Mendengarkan dan menyampaikan pendapat dengan santun', 'Mengejek pendapatnya', 'Meninggikan suara', 'Menyebarkan kesalahannya'], 'correct_answer' => 'Mendengarkan dan menyampaikan pendapat dengan santun'],
                ['type' => 'essay', 'question_text' => 'Ceritakan satu tindakan kecil untuk menjaga akhlak baik di kelas atau asrama.', 'options' => null, 'correct_answer' => null],
            ]],
            ['category' => 'Karakter', 'subcategory' => 'Adab', 'title' => 'Tes Adab', 'description' => 'Adab kepada guru, teman, dan lingkungan.', 'questions' => [
                ['type' => 'multiple_choice', 'question_text' => 'Ketika guru sedang menjelaskan, adab yang baik adalah...', 'options' => ['Menyimak dan bertanya dengan izin', 'Mengobrol dengan teman', 'Bermain gawai', 'Memotong penjelasan'], 'correct_answer' => 'Menyimak dan bertanya dengan izin'],
                ['type' => 'essay', 'question_text' => 'Bagaimana kamu menunjukkan adab saat meminjam barang teman?', 'options' => null, 'correct_answer' => null],
            ]],
        ];

        foreach ($examList as $item) {
            $exam = Exam::updateOrCreate(
                ['category' => $item['category'], 'subcategory' => $item['subcategory']],
                ['title' => $item['title'], 'description' => $item['description']],
            );

            foreach ($item['questions'] as $question) {
                Question::updateOrCreate(
                    ['exam_id' => $exam->id, 'question_text' => $question['question_text']],
                    ['type' => $question['type'], 'options' => $question['options'], 'correct_answer' => $question['correct_answer']],
                );
            }
        }
    }
}
