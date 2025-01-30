export default {
    install(app) {
        // Solo mantenemos las utilidades básicas
        app.config.globalProperties.$gantt = {
            formatDate(date) {
                if (!date) return "";
                const d = new Date(date);
                return d.toLocaleDateString();
            },
        };
    },
};
