/** @type {import('tailwindcss').Config} */
module.exports = {
  content: ["./*.html", "./src/**/*.html"],
  theme: {
    extend: {
      colors: {
        ink: {
          900: "#1F2A28",
          700: "#3A4744",
          500: "#5C6764",
          300: "#9AA3A0",
        },
        cream: {
          DEFAULT: "#FAF8F4",
          100: "#FFFFFF",
          200: "#F3EFE7",
        },
        sage: {
          50: "#EFF8F2",
          100: "#DCEEE3",
          200: "#B7DDC4",
          300: "#8FC9A3",
          400: "#67B37F",
          500: "#4C9C68",
          600: "#3B7D53",
          700: "#2D5F40",
          800: "#21462F",
          900: "#17311F",
        },
        mustard: {
          50: "#FBF3E3",
          100: "#F5E4BE",
          200: "#EACB86",
          300: "#DDAE52",
          400: "#CC9530",
          500: "#B37F22",
          600: "#8F6519",
          700: "#6D4C13",
        },
        terracotta: {
          50: "#FBEAE6",
          100: "#F3CCC2",
          200: "#E4A08F",
          300: "#D3735C",
          400: "#C15038",
          500: "#A83E28",
          600: "#85301F",
          700: "#642417",
        },
      },
      fontFamily: {
        serif: ["Fraunces", "Georgia", "serif"],
        sans: ["Work Sans", "sans-serif"],
      },
      boxShadow: {
        soft: "0 2px 8px -2px rgba(31, 42, 40, 0.08), 0 8px 24px -8px rgba(31, 42, 40, 0.10)",
        card: "0 1px 2px rgba(31, 42, 40, 0.06), 0 12px 32px -12px rgba(76, 156, 104, 0.18)",
        lift: "0 4px 12px -2px rgba(31, 42, 40, 0.10), 0 20px 40px -16px rgba(31, 42, 40, 0.16)",
        floating: "0 8px 16px -4px rgba(31, 42, 40, 0.12), 0 24px 48px -16px rgba(31, 42, 40, 0.20)",
      },
      letterSpacing: {
        tightest: "-0.03em",
      },
      lineHeight: {
        relaxed17: "1.7",
      },
      borderRadius: {
        xl2: "1.25rem",
      },
    },
  },
  plugins: [],
}
