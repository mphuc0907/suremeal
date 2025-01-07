tailwind.config = {
    theme: {
        extend: {
            screens: {
                '3xl': '1440px',
                '2xl': '1280px',
                xl: '1024px',
                lg: '992px',
                md: '768px',
                DEFAULT: '1360px',
            },
            container: {
                center: true,
                padding: {
                    DEFAULT: '24px',
                    '3xl': '40px',
                    '2xl': '32px',
                    md: '24px',
                },
            },

        },

        colors: {
            primary: '#ED1B24',
            secondary: '#0E74BC',
            white: '#FFF',
            black: '#000',
            'black-2': '#0F0F0F',
            neutral: {
                900: "#111527",
                800: "#1F2237",
                700: "#373A51",
                600: "#4B4E63",
                500: "#6B7280",
                400: "#9CA3AF",
                300: "#D1D5DB",
                200: "#E5E7EB",
                100: "#F3F4F6",
                50: "#F9FAFB",
            },
            gray: {
                10: "#112722",
                9: "#1F2237",
                8: "#373A51",
                7: "#4B4E63",
            },
            red: {
                1: "#FF575E",
            }
        }
    }
}