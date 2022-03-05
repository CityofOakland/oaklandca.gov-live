module.exports = {
  content: [
    './templates/**/*.{twig,html,vue}',
    './src/**/*.{js,jsx,ts,tsx,vue}',
  ],
  safelist: [
    {
      pattern: /bg-(red|green|yellow)-(500|600|300)/,
      variants: ['hover']
    }
  ],
  theme: {
    colors: {
      transparent: "transparent",

      white: "#ffffff",
      black: "#000000",

      "gray-100": "#f7f7f7",
      "gray-200": "#e1e1e1",
      "gray-300": "#cfcfcf",
      "gray-400": "#b1b1b1",
      "gray-500": "#9e9e9e",
      "gray-600": "#7e7e7e",
      "gray-700": "#626262",
      "gray-800": "#515151",
      "gray-900": "#3b3b3b",
      "gray-1000": "#222222",

      "cool-gray-100": "#f5f7fa",
      "cool-gray-200": "#e4e7eb",
      "cool-gray-300": "#cbd2d9",
      "cool-gray-400": "#9aa5b1",
      "cool-gray-500": "#7b8794",
      "cool-gray-600": "#616e7c",
      "cool-gray-700": "#52606d",
      "cool-gray-800": "#3e4c59",
      "cool-gray-900": "#323f4b",
      "cool-gray-1000": "#1f2933",

      "blue-100": "#f0f4f8",
      "blue-200": "#d9e2ec",
      "blue-300": "#bcccdc",
      "blue-400": "#9fb3c8",
      "blue-500": "#829ab1",
      "blue-600": "#627d98",
      "blue-700": "#486581",
      "blue-800": "#334e68",
      "blue-900": "#243b53",
      "blue-1000": "#102a43",

      "green-100": "#e3f9e4",
      "green-200": "#c0eac4",
      "green-300": "#a3d9a4",
      "green-400": "#7ac47e",
      "green-500": "#57ad5a",
      "green-600": "#3e9141",
      "green-700": "#2e8131",
      "green-800": "#207127",
      "green-900": "#0d5713",
      "green-1000": "#04400a",

      "red-100": "#ffeeee",
      "red-200": "#facdcd",
      "red-300": "#f29b9b",
      "red-400": "#e66a6a",
      "red-500": "#d64545",
      "red-600": "#ba2525",
      "red-700": "#a61b1b",
      "red-800": "#911111",
      "red-900": "#780a0a",
      "red-1000": "#610404",

      "yellow-100": "#fffbea",
      "yellow-200": "#fff3c4",
      "yellow-300": "#fce588",
      "yellow-400": "#fadb5f",
      "yellow-500": "#f7c948",
      "yellow-600": "#f0b429",
      "yellow-700": "#de911d",
      "yellow-800": "#cb6e17",
      "yellow-900": "#b44d12",
      "yellow-1000": "#8d2b0b",
    },
    spacing: {
      px: "1px",
      "0": "0",
      "1": "0.25rem",
      "2": "0.5rem",
      "3": "0.75rem",
      "4": "1rem",
      "5": "1.25rem",
      "6": "1.5rem",
      "8": "2rem",
      "9": "2.25rem",
      "10": "2.5rem",
      "12": "3rem",
      "14": "3.5rem",
      "16": "4rem",
      "20": "5rem",
      "24": "6rem",
      "30": "7.5rem",
      "32": "8rem",
      "40": "10rem",
      "48": "12rem",
      "56": "14rem",
      "64": "16rem",
      "1/2": "50%",
      "1/3": "33.333333%",
      "2/3": "66.666667%",
      "1/4": "25%",
      "3/4": "75%",
      "1/5": "20%",
      "2/5": "40%",
      "3/5": "60%",
      "4/5": "80%",
      "1/6": "16.666667%",
      "5/6": "83.333333%",
      "1/12": "8.333333%",
      "5/12": "41.666667%",
      "7/12": "58.333333%",
      "9/12": "75%",
      "11/12": "91.666667%",
      "1/11": "9.090909%",
      "2/11": "18.181818%",
      "3/11": "27.272727%",
      "4/11": "36.363636%",
      "5/11": "45.454545%",
      "6/11": "54.545454%",
      "7/11": "63.636363%",
      "8/11": "72.727272%",
      "9/11": "81.818181%",
      "10/11": "90.909090%",
    },
    backgroundColor: (theme) => theme("colors"),
    fontFamily: {
      body: ["Montserrat", "system-ui", "-apple-system", "sans-serif"],
    },
    fontSize: {
      xs: ".75rem", // 12px
      sm: ".875rem", // 14px
      base: "1rem", // 16px
      lg: "1.125rem", // 18px
      xl: "1.25rem", // 20px
      "2xl": "1.5rem", // 24px
      "3xl": "1.875rem", // 30px
      "4xl": "2.25rem", // 36px
      "5xl": "3rem", // 48px
      "6xl": "3.75rem", // 60px
      "7xl": "4.5rem", // 72px
    },
    borderWidth: {
      DEFAULT: "1px",
      "0": "0",
      "2": "2px",
      "4": "4px",
      "8": "8px",
    },
    minHeight: {
      "2-1/2": "10rem",
      "4": "16rem",
      "8": "32rem",
    },
    height: (theme) => ({
      auto: "auto",
      ...theme("spacing"),
      half: "50%",
      full: "100%",
      screen: "100vh",
    }),
    maxHeight: {
      full: "100%",
      screen: "100vh",
    },
    width: (theme) => ({
      auto: "auto",
      ...theme("spacing"),
      full: "100%",
      screen: "100vw",
    }),
    maxWidth: (theme) => ({
      ...theme("spacing"),
      xs: "20rem",
      sm: "30rem",
      md: "40rem",
      lg: "50rem",
      xl: "60rem",
      full: "100%",
    }),
    margin: (theme, { negative }) => ({
      auto: "auto",
      ...theme("spacing"),
      ...negative(theme("spacing")),
    }),
    padding: (theme) => theme("spacing"),
    boxShadow: {
      DEFAULT: '0 1px 2px 0 rgba(theme("colors.gray-800"),.325)',
      btn:
        '0 2px 6px 2px theme("colors.gray-200"), 0 3px 7px 1px theme("colors.green-100")',
      none: "none",
    },
    zIndex: {
      auto: "auto",
      "neg-90": -90,
      "neg-80": -80,
      "neg-70": -70,
      "neg-60": -60,
      "neg-50": -50,
      "neg-40": -40,
      "neg-30": -30,
      "neg-20": -20,
      "neg-10": -10,
      "0": 0,
      "10": 10,
      "20": 20,
      "30": 30,
      "40": 40,
      "50": 50,
      "60": 60,
      "70": 70,
      "80": 80,
      "90": 90,
    },
    opacity: {
      "0": "0",
      "3": ".03",
      "6": ".06",
      "12": ".12",
      "25": ".25",
      "33": ".33",
      "50": ".5",
      "66": ".66",
      "75": ".75",
      "100": "1",
    },
    container: (theme) => ({
      center: true,
    }),
  },
  plugins: [
  ]
};
