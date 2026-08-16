/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
  ],
  // These are built dynamically in Blade (e.g. bg-brand-{{ $kategori['warna'] }})
  // so the JIT scanner can't see them as literal strings — list them explicitly
  // or Tailwind will purge them from the production build.
  safelist: [
    ...["blue", "green", "orange"].flatMap((c) => [
      `bg-brand-${c}`,
      `text-brand-${c}`,
      `border-brand-${c}`,
      `border-brand-${c}/20`,
      `border-brand-${c}/50`,
      `hover:border-brand-${c}/50`,
      `group-hover:text-brand-${c}`,
      `bg-brand-${c}-soft`,
      `bg-brand-${c}/10`,
      `bg-brand-${c}/20`,
    ]),
    // dashboard chart uses a dynamic column count (1 col per exam stat)
    ...[1, 2, 3, 4, 5, 6].map((n) => `grid-cols-${n}`),
  ],
  theme: {
    extend: {
      colors: {
        ink: "#1B2430",
        cloud: "#F7F9FB",
        line: "#E4E9EF",
        brand: {
          blue: "#3B6FE0",
          "blue-soft": "#EAF0FD",
          green: "#2FAE72",
          "green-soft": "#E8F7EF",
          orange: "#F0883E",
          "orange-soft": "#FEF0E4",
        },
      },
      fontFamily: {
        display: ['"Plus Jakarta Sans"', "sans-serif"],
        sans: ["Inter", "sans-serif"],
        mono: ['"IBM Plex Mono"', "monospace"],
      },
      borderRadius: {
        xl2: "1.25rem",
      },
    },
  },
  plugins: [],
};
