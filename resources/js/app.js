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
    expanded: {},

    categoryOrder: ['Bahasa', 'IT', 'Karakter'],

    get orderedCategories() {
        const keys = Object.keys(this.exams);
        const ordered = this.categoryOrder.filter((c) => keys.includes(c));
        const rest = keys.filter((k) => !this.categoryOrder.includes(k));
        return [...ordered, ...rest];
    },

    toggleCategory(category) {
        this.expanded[category] = !this.expanded[category];
    },

    isExpanded(category) {
        return !!this.expanded[category];
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

    async init() {
        try {
            const json = await api.getExams();
            this.exams = json?.data ?? json ?? {};
            this.orderedCategories.forEach((cat, i) => {
                this.expanded[cat] = i === 0;
            });
        } catch (e) {
            this.error = e.message;
        } finally {
            this.loading = false;
        }
    },
}));

// ---------- 4. Pengerjaan Ujian ----------
Alpine.data('examPage', (examId) => ({
    loading: true,
    submitting: false,
    error: '',
    tab: 'pg',
    exam: null,
    pilihanGanda: [],
    essai: [],
    jawaban: {},

    get answeredCount() {
        return this.pilihanGanda.filter((q) => this.jawaban[q.id] !== undefined && this.jawaban[q.id] !== '').length;
    },

    async init() {
        try {
            const json = await api.getExam(examId);
            const data = json?.data ?? json;
            this.exam = { ...data.exam, warna: categoryColor(data.exam?.category) };
            this.pilihanGanda = (data.questions ?? []).filter((q) => q.type === 'multiple_choice');
            this.essai = (data.questions ?? []).filter((q) => q.type === 'essay');

            if (!this.pilihanGanda.length && this.essai.length) {
                this.tab = 'essai';
            }
        } catch (e) {
            this.error = e.message;
        } finally {
            this.loading = false;
        }
    },

    async submit() {
        this.submitting = true;
        this.error = '';

        const answers = Object.entries(this.jawaban)
            .filter(([, v]) => v !== '' && v != null)
            .map(([question_id, answer_text]) => ({ question_id: Number(question_id), answer_text }));

        if (!answers.length) {
            this.error = 'Harap isi minimal satu pertanyaan sebelum mengumpulkan.';
            this.submitting = false;
            return;
        }

        try {
            await api.submitExam({ exam_id: this.exam.id, answers });
            window.location.href = '/dashboard';
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

    get summaryTotal() {
        return this.students.length;
    },

    get summaryParticipated() {
        return this.students.filter((s) => this.loginCount(s) > 0).length;
    },

    get summaryTestsDone() {
        return this.students.reduce((sum, s) => sum + this.testsDone(s), 0);
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

    loginCount(student) {
        return Number(student?.login_count ?? student?.participated ?? 0);
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
                title: this.statTitle(item),
                value: this.statValue(item),
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