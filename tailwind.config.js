/** @type {import('tailwindcss').Config} */
module.exports = {
  content: ['./app/Views/**/*.php'],
  theme: {
    extend: {},
  },
  plugins: [],
  future: {
    // Los estilos hover: solo aplican con puntero preciso (mouse), para que
    // un tap en móvil no deje el estado :hover "pegado" hasta el siguiente toque.
    hoverOnlyWhenSupported: true,
  },
};
