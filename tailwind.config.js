/** @type {import('tailwindcss').Config} */
module.exports = {
  content: ['./app/Views/**/*.php'],
  theme: {
    extend: {
      colors: {
        // Reemplaza el ámbar genérico de Tailwind por el dorado de marca
        // (el mismo tono del botón del login), para que los ~90 usos de
        // amber-* ya existentes en las vistas hereden la identidad de
        // Florence Art sin tener que tocar cada archivo.
        amber: {
          50: '#fbf6ec',
          100: '#f3e7cc',
          200: '#e8d1a0',
          300: '#d9b873',
          400: '#c8a157',
          500: '#b68b3f',
          600: '#a97e46',
          700: '#8c6530',
          800: '#6b4c25',
          900: '#4a3419',
        },
      },
    },
  },
  plugins: [],
  future: {
    // Los estilos hover: solo aplican con puntero preciso (mouse), para que
    // un tap en móvil no deje el estado :hover "pegado" hasta el siguiente toque.
    hoverOnlyWhenSupported: true,
  },
};
