import Alpine from 'alpinejs';
import { api, getUser, clearSession, categoryColor } from './api.js';

window.Alpine = Alpine;

// ---------- Global Auth Store ----------
Alpine.store('auth', {
    user: getUser(),
    async logout() {
        try {
            await api.logout();
        } catch {
            clearSession();
        } finally {
            this.user = null;
            window.location.href = '/login';
        }
    },
});

// ---------- 1. Login Page ----------
Alpine.data('loginPage', () => ({
    email: '',
    password: '',
    error: '',
    loading: false,

    async submit() {
        this.loading = true;
        this.error = '';
        try {
            const res = await api.login({ email: this.email, password: this.password });
            const user = getUser() || res.user;
            Alpine.store('auth').user = user;
            window.location.href = ['admin', 'teacher'].includes(user?.role) ? '/admin/dashboard' : '/beranda';
        } catch (e) {
            this.error = e.message;
        } finally {
            this.loading = false;
        }
    },
}));

// ---------- 2. Register Page ----------
Alpine.data('registerPage', () => ({
    name: '',
    email: '',
    password: '',
    error: '',
    loading: false,

    async submit() {
        this.loading = true;
        this.error = '';
        try {
            const res = await api.register({ name: this.name, email: this.email, password: this.password });
            Alpine.store('auth').user = getUser() || res.user;
            window.location.href = '/beranda';
        } catch (e) {
            this.error = e.message;
        } finally {
            this.loading = false;
        }
    },
}));

// ---------- 3. Beranda (List Ujian Santri) ----------
Alpine.data('berandaPage', () => ({
    loading: true,
    error: '',
    exams: {},
    openCategory: null,

    categoryOrder: ['Bahasa', 'IT', 'Karakter'],

    get orderedCategories() {
        const keys = Object.keys(this.exams);
        const ordered = this.categoryOrder.filter((c) => keys.includes(c));
        const rest = keys.filter((k) => !this.categoryOrder.includes(k));
        return [...ordered, ...rest];
    },

    toggleCategory(category) {
        this.openCategory = this.openCategory === category ? null : category;
    },

    isExpanded(category) {
        return this.openCategory === category;
    },

    categoryDotClass(category) {
        const key = category.toLowerCase();
        if (key === 'bahasa') return 'bg-brand-blue';
        if (key === 'it') return 'bg-brand-green';
        if (key === 'karakter') return 'bg-brand-orange';
        return 'bg-brand-blue';
    },

    categoryBorderClass(category) {
        const key = category.toLowerCase();
        if (key === 'bahasa') return 'border-l-brand-blue';
        if (key === 'it') return 'border-l-brand-green';
        if (key === 'karakter') return 'border-l-brand-orange';
        return 'border-l-brand-blue';
    },

    examButtonLabel(item) {
        if (item.retake_allowed) return 'Kerjakan Ulang';
        if (item.completed) return 'Lihat Hasil';
        return 'Mulai Ujian';
    },

    examButtonClass(item) {
        if (item.retake_allowed) return '';
        if (item.completed) return 'opacity-90 bg-slate-600 hover:bg-slate-700';
        return '';
    },

    async init() {
        try {
            const json = await api.getExams();
            this.exams = json?.data ?? json ?? {};
            this.openCategory = this.orderedCategories[0] ?? null;
        } catch (e) {
            this.error = e.message;
        } finally {
            this.loading = false;
        }
    },
}));

// ---------- 4. Pengerjaan Ujian ----------
// ---------- 4. Pengerjaan Ujian ----------
Alpine.data('examPage', (examId) => ({
    loading: true,
    submitting: false,
    error: '',
    exam: null,
    questions: [],
    results: [],
    completed: false,
    retakeAllowed: false,
    currentIndex: 0,
    jawaban: {},
    
    // --- NEW: Timer and SPS Variables ---
    spsPrediction: null,
    timeRemaining: 0,
    timerInterval: null,

    // Format seconds into MM:SS for the frontend
    get formattedTime() {
        let minutes = Math.floor(this.timeRemaining / 60);
        let seconds = this.timeRemaining % 60;
        return `${minutes}:${seconds < 10 ? '0' : ''}${seconds}`;
    },

    startTimer() {
        if (this.timerInterval) clearInterval(this.timerInterval);
        
        this.timerInterval = setInterval(() => {
            if (this.timeRemaining > 0) {
                this.timeRemaining--;
            } else {
                clearInterval(this.timerInterval);
                alert('Waktu habis! Jawaban Anda otomatis dikumpulkan.');
                this.submit(); // Auto-submit when time is up
            }
        }, 1000);
    },
    // ------------------------------------

    get currentQuestion() {
        return this.questions[this.currentIndex] ?? null;
    },

    get isLast() {
        return this.currentIndex >= this.questions.length - 1;
    },

    // ... (Keep your existing getter functions like answeredCount, mcTotal, etc.) ...

    async init() {
        try {
            const json = await api.getExam(examId);
            const data = json?.data ?? json;
            this.exam = { ...data.exam, warna: categoryColor(data.exam?.category) };
            this.completed = !!data.completed;
            this.retakeAllowed = !!data.retake_allowed;
            this.results = data.results ?? [];
            this.questions = data.questions ?? [];
            this.currentIndex = 0;
            
            // --- NEW: Load SPS and start timer ---
            this.spsPrediction = data.sps_prediction ?? null;
            
            // If the exam isn't completed yet, start the countdown
            if (!this.completed && this.questions.length > 0) {
                // Fallback to 30 mins if duration_minutes isn't set
                this.timeRemaining = (this.exam.duration_minutes || 30) * 60; 
                this.startTimer();
            }
            // -------------------------------------

        } catch (e) {
            this.error = e.message;
        } finally {
            this.loading = false;
        }
    },

    async submit() {
        this.submitting = true;
        this.error = '';

        // --- NEW: Clear the timer on manual submit ---
        if (this.timerInterval) clearInterval(this.timerInterval);
        // ---------------------------------------------

        // ... (Keep the rest of your submit logic exactly the same) ...

        const unanswered = this.questions.filter((q) => {
            const v = this.jawaban[q.id];
            return v === undefined || v === null || String(v).trim() === '';
        });

        if (unanswered.length) {
            this.error = `Masih ada ${unanswered.length} soal yang belum dijawab. Periksa kembali sebelum mengumpulkan.`;
            this.submitting = false;
            return;
        }

        const answers = Object.entries(this.jawaban)
            .filter(([, v]) => v !== '' && v != null)
            .map(([question_id, answer_text]) => ({ question_id: Number(question_id), answer_text }));

        try {
            await api.submitExam({ exam_id: this.exam.id, answers });
            window.location.href = '/ujian/' + this.exam.id;
        } catch (e) {
            this.error = e.message;
        } finally {
            this.submitting = false;
        }
    },
}));

// ---------- 5. Portofolio ----------
// ---------- 5. Portofolio ----------
Alpine.data('portofolioPage', () => ({
    links: [''],
    files: [],
    maxFiles: 5,
    submitting: false,
    error: '',
    success: false,

    addLink() {
        this.links.push('');
    },

    removeLink(index) {
        if (this.links.length > 1) {
            this.links.splice(index, 1);
        }
    },

    addFiles(fileList) {
        const room = this.maxFiles - this.files.length;
        this.files.push(...Array.from(fileList).slice(0, room));
    },

    removeFile(i) {
        this.files.splice(i, 1);
    },

    async submit() {
        this.submitting = true;
        this.error = '';
        this.success = false;

        try {
            const validLinks = this.links.filter(l => l && l.trim() !== '').join(', ');
            const formData = new FormData();
            if (validLinks) formData.append('links', validLinks);
            this.files.forEach((f) => formData.append('files[]', f));

            await api.submitPortfolio(formData);
            this.success = true;
            this.files = [];
            this.links = [''];
            window.location.href = '/dashboard';
        } catch (e) {
            this.error = e.message;
        } finally {
            this.submitting = false;
        }
    },
}));

// ---------- 6. Profile & Prediksi Kemampuan ----------
Alpine.data('profilePage', () => ({
    loading: true,
    error: '',
    profile: null,
    stats: [],
    portfolio: null,
    languageLevel: '-',
    languageAccuracy: 0,
    itLevel: '-',
    itAccuracy: 0,

    get totalAnswered() {
        return this.stats.reduce((sum, item) => sum + (Number(item.answered_count) || 0), 0);
    },

    get totalQuestionsCount() {
        // Asumsi estimasi total seluruh butir soal dari semua kategori (atau hitung dinamis)
        const total = this.stats.length * 3; // jika rata-rata tiap exam 3 soal
        return total > 0 ? total : (this.totalAnswered || 1);
    },

    get overallProgress() {
        if (!this.stats.length) return 0;
        const totalExams = this.stats.length;
        const completedExams = this.stats.filter(s => Number(s.answered_count) > 0).length;
        return Math.min(100, Math.round((completedExams / totalExams) * 100));
    },

    async init() {
        try {
            const json = await api.getDashboard();
            const data = json?.data ?? json;
            this.profile = {
                ...data.student,
                role: Alpine.store('auth').user?.role ?? 'student'
            };
            this.stats = data.exam_stats ?? [];
            this.portfolio = data.portfolio ?? null;
            this.calculatePredictions();
        } catch (e) {
            this.error = e.message;
        } finally {
            this.loading = false;
        }
    },

    calculatePredictions() {
        const bahasaStats = this.stats.filter((s) => s.category?.toLowerCase() === 'bahasa');
        if (bahasaStats.length > 0) {
            const avg = bahasaStats.reduce((acc, curr) => acc + (Number(curr.mc_accuracy_pct) || 0), 0) / bahasaStats.length;
            this.languageAccuracy = Math.round(avg);

            if (avg < 20) this.languageLevel = 'A1';
            else if (avg < 40) this.languageLevel = 'A2';
            else if (avg < 60) this.languageLevel = 'B1';
            else if (avg < 75) this.languageLevel = 'B2';
            else if (avg < 90) this.languageLevel = 'C1';
            else this.languageLevel = 'C2';
        }

        const itStats = this.stats.filter((s) => s.category?.toLowerCase() === 'it');
        if (itStats.length > 0) {
            const avg = itStats.reduce((acc, curr) => acc + (Number(curr.mc_accuracy_pct) || 0), 0) / itStats.length;
            this.itAccuracy = Math.round(avg);

            if (avg < 34) this.itLevel = 'Rookie';
            else if (avg < 67) this.itLevel = 'Amateur';
            else this.itLevel = 'Pro';
        }
    },
}));

// ---------- Animated Counter (global) ----------
Alpine.data('animatedCounter', (target = 0, ms = 700) => ({
    display: 0,
    start() {
        const end = Number(target) || 0;
        const tick = performance.now();
        const step = (now) => {
            const progress = Math.min(1, (now - tick) / ms);
            this.display = Math.round(end * progress);
            if (progress < 1) requestAnimationFrame(step);
        };
        requestAnimationFrame(step);
    },
}));

// ---------- 7. Admin Dashboard ----------
Alpine.data('adminDashboardPage', () => ({
    loading: true,
    error: '',
    students: [],
    expanded: {},
    retakeBusy: null,

    get summaryTotal() {
        return this.students.length;
    },

    get summaryTestsDone() {
        return this.students.reduce((sum, s) => sum + this.testsDone(s), 0);
    },

    get summaryAvgHighest() {
        if (!this.students.length) return 0;
        const sum = this.students.reduce((acc, s) => acc + this.highestScore(s), 0);
        return Math.round(sum / this.students.length);
    },

    toggleStudent(id) {
        this.expanded[id] = !this.expanded[id];
    },

    studentName(student) {
        return student?.name || student?.student_name || student?.full_name || '-';
    },

    studentEmail(student) {
        return student?.email || student?.student_email || '';
    },

    testsDone(student) {
        return Number(student?.tests_done ?? student?.exam_count ?? 0);
    },

    highestScore(student) {
        return Number(student?.highest_score ?? student?.max_score ?? 0) || 0;
    },

    portfolioFiles(student) {
        if (Array.isArray(student?.portfolio?.files)) return student.portfolio.files;
        if (Array.isArray(student?.files)) return student.files;
        return [];
    },

    groupedStats(student) {
        const stats = student?.exam_stats ?? student?.stats ?? [];
        if (!Array.isArray(stats)) return { Bahasa: [], Karakter: [], IT: [] };

        return stats.reduce((groups, item) => {
            const key = String(item?.category || '').trim().toLowerCase();
            if (key === 'bahasa') groups.Bahasa.push(item);
            else if (key === 'karakter') groups.Karakter.push(item);
            else if (key === 'it') groups.IT.push(item);
            return groups;
        }, { Bahasa: [], Karakter: [], IT: [] });
    },

    statTitle(stat) {
        return stat?.exam_title || stat?.title || stat?.name || '-';
    },

    statValue(stat) {
        return Number(stat?.mc_accuracy_pct ?? stat?.percentage ?? stat?.score ?? 0) || 0;
    },

    categoryBorderClass(label) {
        const map = { Bahasa: 'border-l-4 border-l-brand-blue', IT: 'border-l-4 border-l-brand-green', Karakter: 'border-l-4 border-l-brand-orange' };
        return map[label] || 'border-l-4 border-l-brand-blue';
    },

    categoryBarClass(label) {
        const map = { Bahasa: 'bg-brand-blue', IT: 'bg-brand-green', Karakter: 'bg-brand-orange' };
        return map[label] || 'bg-brand-blue';
    },

    barWidth(studentId, value) {
        return this.expanded[studentId] ? `width: ${value}%` : 'width: 0%';
    },

    async allowRetake(student, item) {
        if (!item?.exam_id || item.retake_allowed) return;
        const key = student.id + '-' + item.exam_id;
        this.retakeBusy = key;
        try {
            await api.allowRetake({ user_id: student.id, exam_id: item.exam_id });
            item.retake_allowed = true;
        } catch (e) {
            alert(e.message || 'Gagal memberikan izin ulang');
        } finally {
            this.retakeBusy = null;
        }
    },

    async init() {
        const user = getUser();
        if (!user || !['admin', 'teacher'].includes(user.role)) {
            this.error = 'Akses ditolak. Halaman ini khusus Admin / Guru.';
            this.loading = false;
            return;
        }

        try {
            const json = await api.getAdminStudents();
            this.students = json?.data ?? json ?? [];
        } catch (e) {
            this.error = e.message;
        } finally {
            this.loading = false;
        }
    },

    revealBars(student) {
        const raw = this.groupedStats(student);
        return Object.entries(raw).map(([label, items]) => ({
            label,
            items: items.map((item) => ({
                exam_id: item.exam_id,
                title: this.statTitle(item),
                value: this.statValue(item),
                completed: !!item.completed,
                retake_allowed: !!item.retake_allowed,
            })),
        }));
    },
}));

// ---------- 8. Admin Koreksi Jawaban ----------
Alpine.data('adminKoreksiPage', () => ({
    loading: true,
    error: '',
    student: null,
    answers: [],
    scores: {},

    async init() {
        const user = getUser();
        if (!user || !['admin', 'teacher'].includes(user.role)) {
            this.error = 'Akses ditolak.';
            this.loading = false;
            return;
        }

        const parts = window.location.pathname.split('/').filter(Boolean);
        const userId = parts[parts.length - 1];

        try {
            const json = await api.getAdminStudentAnswers(userId);
            const data = json?.data ?? json ?? {};
            this.student = data.student ?? null;
            this.answers = data.answers ?? [];
            this.answers.forEach((a) => {
                if (a.current_score != null) this.scores[a.answer_id] = a.current_score;
            });
        } catch (e) {
            this.error = e.message;
        } finally {
            this.loading = false;
        }
    },

    async saveScore(answerId) {
        const score = this.scores[answerId];
        if (score === undefined || score === '') {
            alert('Masukkan nilai 0-100 terlebih dahulu');
            return;
        }

        try {
            await api.gradeAnswer({ answer_id: answerId, score: parseFloat(score) });
            const answer = this.answers.find((a) => a.answer_id === answerId);
            if (answer) answer.current_score = parseFloat(score);
            alert('Nilai berhasil disimpan');
        } catch (e) {
            alert(e.message);
        }
    },
}));

Alpine.start();