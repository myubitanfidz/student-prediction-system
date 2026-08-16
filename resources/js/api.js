// resources/js/api.js
//
// Wrapper tipis di atas fetch() untuk manggil API backend (Laravel API terpisah,
// lihat dokumentasi yang dikasih temanmu). Base URL diambil dari
// import.meta.env.VITE_API_BASE_URL kalau ada, fallback ke localhost:8000.
//
// ASUMSI YANG BELUM DIKONFIRMASI DOKUMENTASI (cek & sesuaikan setelah tes di Postman):
//   - /login dan /register diasumsikan balikin sesuatu seperti:
//       { status: "success", data: { token: "...", user: { id, name, email } } }
//     Kalau bentuknya beda (misal token ada di root, atau field-nya "access_token"),
//     tinggal sesuaikan di fungsi login()/register() di bawah — sudah aku kasih
//     beberapa fallback path yang umum dipakai Laravel Sanctum.
//   - Endpoint yang butuh login (submit ujian, portofolio, dashboard) diasumsikan
//     menerima Bearer token di header Authorization. Kalau ternyata backend tidak
//     pakai token sama sekali (cuma percaya user_id yang dikirim di body), token
//     itu nggak akan disalahin, cuma nggak dipakai — aman untuk dua-duanya.

// A relative URL keeps the API on the same host and port as the Laravel pages.
// VITE_API_BASE_URL can still override this if the API is moved to another project later.
const API_BASE = import.meta.env.VITE_API_BASE_URL || '/api';

export function getToken() {
    return localStorage.getItem('ts_token');
}
export function getUser() {
    try { return JSON.parse(localStorage.getItem('ts_user') || 'null'); }
    catch { return null; }
}
export function setSession(token, user) {
    if (token) localStorage.setItem('ts_token', token);
    if (user) localStorage.setItem('ts_user', JSON.stringify(user));
}
export function clearSession() {
    localStorage.removeItem('ts_token');
    localStorage.removeItem('ts_user');
}
export function isLoggedIn() {
    return !!getUser();
}

async function request(path, { method = 'GET', body, isForm = false } = {}) {
    const headers = { Accept: 'application/json' };
    if (!isForm) headers['Content-Type'] = 'application/json';
    const token = getToken();
    if (token) headers['Authorization'] = `Bearer ${token}`;

    const res = await fetch(`${API_BASE}${path}`, {
        method,
        headers,
        body: isForm ? body : (body ? JSON.stringify(body) : undefined),
    });

    let json = null;
    try { json = await res.json(); } catch { /* respon kosong / bukan json */ }

    if (!res.ok) {
        const message = json?.message || json?.errors
            ? Object.values(json.errors ?? {}).flat().join(' ') || json.message
            : `Terjadi kesalahan (${res.status})`;
        throw new Error(message || 'Terjadi kesalahan pada server.');
    }
    return json;
}

// Cari token/user di beberapa kemungkinan bentuk respons — lihat catatan asumsi di atas.
function extractSession(json) {
    const data = json?.data ?? json ?? {};
    const token = data.token || data.access_token || json?.token || null;
    const user = data.user || data.santri || (data.id ? data : null);
    return { token, user };
}

export const api = {
    register(payload) {
        // payload: { name, email, password }
        return request('/register', { method: 'POST', body: payload }).then((json) => {
            const { token, user } = extractSession(json);
            setSession(token, user);
            return json;
        });
    },
    login(payload) {
        // payload: { email, password }
        return request('/login', { method: 'POST', body: payload }).then((json) => {
            const { token, user } = extractSession(json);
            setSession(token, user);
            return json;
        });
    },
    logout() {
        clearSession();
    },
    getExams(userId) {
        return request('/exams' + (userId ? '?user_id=' + encodeURIComponent(userId) : ''));
    },
    getExam(id) {
        return request(`/exams/${id}`);
    },
    submitExam(payload) {
        // payload: { user_id, answers: [{ question_id, answer_text }] }
        return request('/exams/submit', { method: 'POST', body: payload });
    },
    submitPortfolio(formData) {
        // formData: FormData berisi user_id, links, files[]
        return request('/portfolios', { method: 'POST', body: formData, isForm: true });
    },
    getDashboard(userId) {
        return request(`/dashboard/${userId}`);
    },
    gradeAnswer(payload) {
        // payload: { answer_id, score } — dipakai halaman guru, bukan bagian spek santri
        return request('/exams/grade', { method: 'POST', body: payload });
    },
};

// Warna kategori — ditentukan di frontend karena API tidak mengirim warna.
// Kalau nama kategori dari backend berbeda casing/ejaan, tambahkan mapping di sini.
export function categoryColor(categoryName = '') {
    const key = categoryName.trim().toLowerCase();
    if (key === 'bahasa') return 'blue';
    if (key === 'it') return 'green';
    if (key === 'karakter') return 'orange';
    return 'blue';
}
