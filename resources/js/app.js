import "./bootstrap";
import Alpine from "alpinejs";

// Make Alpine available globally
window.Alpine = Alpine;

// Alpine.js Components
Alpine.data("carRentalApp", () => ({
    // Global state
    notifications: [],
    loading: false,

    // Initialize
    init() {
        this.loadNotifications();
        this.setupEventListeners();
    },

    // Load notifications
    async loadNotifications() {
        try {
            const response = await fetch("/customer/notifications/recent");
            this.notifications = await response.json();
        } catch (error) {
            console.error("Failed to load notifications:", error);
        }
    },

    // Setup event listeners
    setupEventListeners() {
        // Auto-refresh notifications every 30 seconds
        setInterval(() => {
            this.loadNotifications();
        }, 30000);
    },
}));

// Car Search Component
Alpine.data("carSearch", () => ({
    filters: {
        search: "",
        brand: "",
        transmission: "",
        fuel_type: "",
        seats: "",
        min_price: "",
        max_price: "",
        sort_by: "newest",
    },

    applyFilters() {
        const params = new URLSearchParams();
        Object.keys(this.filters).forEach((key) => {
            if (this.filters[key]) {
                params.append(key, this.filters[key]);
            }
        });

        window.location.href = "/cars?" + params.toString();
    },

    clearFilters() {
        Object.keys(this.filters).forEach((key) => {
            this.filters[key] = "";
        });
        this.applyFilters();
    },
}));

// Car Availability Checker
Alpine.data("availabilityChecker", (carId) => ({
    startDate: "",
    endDate: "",
    availability: null,
    checking: false,

    async checkAvailability() {
        if (!this.startDate || !this.endDate) return;

        this.checking = true;

        try {
            const response = await fetch(`/cars/${carId}/check-availability`, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document.querySelector(
                        'meta[name="csrf-token"]'
                    ).content,
                },
                body: JSON.stringify({
                    start_date: this.startDate,
                    end_date: this.endDate,
                }),
            });

            this.availability = await response.json();
        } catch (error) {
            console.error("Error checking availability:", error);
            this.availability = {
                available: false,
                message: "Terjadi kesalahan saat mengecek ketersediaan",
            };
        } finally {
            this.checking = false;
        }
    },
}));

// Payment Method Selector
Alpine.data("paymentSelector", () => ({
    selectedMethod: "",
    bankAccounts: {
        bca: {
            name: "Bank BCA",
            account_number: "1234567890",
            account_name: "CV Rental Mobil Sejahtera",
        },
        mandiri: {
            name: "Bank Mandiri",
            account_number: "0987654321",
            account_name: "CV Rental Mobil Sejahtera",
        },
        bni: {
            name: "Bank BNI",
            account_number: "1122334455",
            account_name: "CV Rental Mobil Sejahtera",
        },
        bri: {
            name: "Bank BRI",
            account_number: "5566778899",
            account_name: "CV Rental Mobil Sejahtera",
        },
    },

    selectMethod(method) {
        this.selectedMethod = method;
    },

    getSelectedBank() {
        return this.bankAccounts[this.selectedMethod] || null;
    },
}));

// Image Upload Preview
Alpine.data("imageUpload", () => ({
    previews: [],

    handleFileSelect(event) {
        const files = Array.from(event.target.files);
        this.previews = [];

        files.forEach((file) => {
            if (file.type.startsWith("image/")) {
                const reader = new FileReader();
                reader.onload = (e) => {
                    this.previews.push({
                        file: file,
                        url: e.target.result,
                        name: file.name,
                    });
                };
                reader.readAsDataURL(file);
            }
        });
    },

    removePreview(index) {
        this.previews.splice(index, 1);
    },
}));

// Notification Manager
Alpine.data("notificationManager", () => ({
    notifications: [],
    unreadCount: 0,

    init() {
        this.loadNotifications();
    },

    async loadNotifications() {
        try {
            const response = await fetch("/customer/notifications/recent");
            const data = await response.json();
            this.notifications = data.notifications || [];
            this.unreadCount = data.unread_count || 0;
        } catch (error) {
            console.error("Failed to load notifications:", error);
        }
    },

    async markAsRead(notificationId) {
        try {
            await fetch(`/customer/notifications/${notificationId}/read`, {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": document.querySelector(
                        'meta[name="csrf-token"]'
                    ).content,
                },
            });

            // Update local state
            const notification = this.notifications.find(
                (n) => n.id === notificationId
            );
            if (notification && !notification.is_read) {
                notification.is_read = true;
                this.unreadCount = Math.max(0, this.unreadCount - 1);
            }
        } catch (error) {
            console.error("Failed to mark notification as read:", error);
        }
    },

    async markAllAsRead() {
        try {
            await fetch("/customer/notifications/mark-all-read", {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": document.querySelector(
                        'meta[name="csrf-token"]'
                    ).content,
                },
            });

            // Update local state
            this.notifications.forEach((n) => (n.is_read = true));
            this.unreadCount = 0;
        } catch (error) {
            console.error("Failed to mark all notifications as read:", error);
        }
    },
}));

// Form Validator
Alpine.data("formValidator", () => ({
    errors: {},

    validateField(field, value, rules) {
        this.errors[field] = [];

        if (rules.required && (!value || value.trim() === "")) {
            this.errors[field].push(`${field} wajib diisi`);
        }

        if (rules.email && value && !this.isValidEmail(value)) {
            this.errors[field].push("Format email tidak valid");
        }

        if (rules.min && value && value.length < rules.min) {
            this.errors[field].push(`Minimal ${rules.min} karakter`);
        }

        if (rules.max && value && value.length > rules.max) {
            this.errors[field].push(`Maksimal ${rules.max} karakter`);
        }
    },

    isValidEmail(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    },

    hasErrors(field) {
        return this.errors[field] && this.errors[field].length > 0;
    },

    getErrors(field) {
        return this.errors[field] || [];
    },
}));

// Modal Manager
Alpine.data("modal", (isOpen = false) => ({
    open: isOpen,

    show() {
        this.open = true;
        document.body.style.overflow = "hidden";
    },

    hide() {
        this.open = false;
        document.body.style.overflow = "auto";
    },

    toggle() {
        this.open ? this.hide() : this.show();
    },
}));

// Start Alpine
Alpine.start();

// Global utility functions
window.CarRental = {
    // Format currency
    formatCurrency(amount) {
        return new Intl.NumberFormat("id-ID", {
            style: "currency",
            currency: "IDR",
            minimumFractionDigits: 0,
        }).format(amount);
    },

    // Format date
    formatDate(date, options = {}) {
        return new Intl.DateTimeFormat("id-ID", {
            year: "numeric",
            month: "long",
            day: "numeric",
            ...options,
        }).format(new Date(date));
    },

    // Show toast notification
    showToast(message, type = "success") {
        const toast = document.createElement("div");
        toast.className = `fixed top-4 right-4 p-4 rounded-lg shadow-lg z-50 ${
            type === "success"
                ? "bg-green-500"
                : type === "error"
                ? "bg-red-500"
                : type === "warning"
                ? "bg-yellow-500"
                : "bg-blue-500"
        } text-white`;
        toast.textContent = message;

        document.body.appendChild(toast);

        // Animate in
        setTimeout(() => toast.classList.add("opacity-100"), 10);

        // Remove after 3 seconds
        setTimeout(() => {
            toast.classList.add("opacity-0");
            setTimeout(() => document.body.removeChild(toast), 300);
        }, 3000);
    },

    // Confirm dialog
    confirm(message) {
        return new Promise((resolve) => {
            if (window.Swal) {
                Swal.fire({
                    title: "Konfirmasi",
                    text: message,
                    icon: "question",
                    showCancelButton: true,
                    confirmButtonText: "Ya",
                    cancelButtonText: "Batal",
                }).then((result) => {
                    resolve(result.isConfirmed);
                });
            } else {
                resolve(confirm(message));
            }
        });
    },

    // Copy to clipboard
    copyToClipboard(text) {
        navigator.clipboard.writeText(text).then(() => {
            this.showToast("Berhasil disalin ke clipboard");
        });
    },
};

// Auto-hide alerts after 5 seconds
document.addEventListener("DOMContentLoaded", function () {
    const alerts = document.querySelectorAll(".alert");
    alerts.forEach((alert) => {
        setTimeout(() => {
            alert.style.transition = "opacity 0.3s ease";
            alert.style.opacity = "0";
            setTimeout(() => {
                if (alert.parentNode) {
                    alert.parentNode.removeChild(alert);
                }
            }, 300);
        }, 5000);
    });
});
