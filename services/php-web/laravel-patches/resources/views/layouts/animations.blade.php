{{-- CSS Animations and Modern Styling --}}
<style>
/* Fade-in animation */
@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Slide-in from left */
@keyframes slideInLeft {
    from {
        opacity: 0;
        transform: translateX(-30px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

/* Slide-in from right */
@keyframes slideInRight {
    from {
        opacity: 0;
        transform: translateX(30px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

/* Pulse animation for metrics */
@keyframes pulse {
    0%, 100% {
        transform: scale(1);
    }
    50% {
        transform: scale(1.05);
    }
}

/* Shimmer loading effect */
@keyframes shimmer {
    0% {
        background-position: -1000px 0;
    }
    100% {
        background-position: 1000px 0;
    }
}

/* Rotate animation for loading */
@keyframes rotate {
    from {
        transform: rotate(0deg);
    }
    to {
        transform: rotate(360deg);
    }
}

/* Apply animations */
.fade-in {
    animation: fadeIn 0.6s ease-out;
}

.slide-in-left {
    animation: slideInLeft 0.6s ease-out;
}

.slide-in-right {
    animation: slideInRight 0.6s ease-out;
}

.pulse-on-update {
    animation: pulse 0.5s ease-in-out;
}

/* Card hover effects */
.card-hover {
    transition: all 0.3s ease;
    cursor: pointer;
}

.card-hover:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.15) !important;
}

/* Metric card styling */
.metric-card {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-radius: 10px;
    padding: 1.5rem;
    transition: all 0.3s ease;
}

.metric-card:hover {
    transform: scale(1.05);
    box-shadow: 0 8px 16px rgba(102, 126, 234, 0.3);
}

.metric-label {
    font-size: 0.875rem;
    opacity: 0.9;
    margin-bottom: 0.5rem;
}

.metric-value {
    font-size: 2rem;
    font-weight: 700;
    line-height: 1;
}

/* Loading shimmer */
.loading-shimmer {
    background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
    background-size: 1000px 100%;
    animation: shimmer 2s infinite;
}

/* Smooth transitions for all elements */
* {
    transition: background-color 0.3s ease, color 0.3s ease, transform 0.3s ease;
}

/* Button hover effects */
.btn {
    position: relative;
    overflow: hidden;
    transition: all 0.3s ease;
}

.btn::before {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    width: 0;
    height: 0;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.3);
    transform: translate(-50%, -50%);
    transition: width 0.6s ease, height 0.6s ease;
}

.btn:hover::before {
    width: 300px;
    height: 300px;
}

.btn:active {
    transform: scale(0.95);
}

/* Table row hover */
.table tbody tr {
    transition: all 0.2s ease;
}

.table tbody tr:hover {
    background-color: #f8f9fa;
    transform: scale(1.01);
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
}

/* Filter input animations */
.form-control:focus, .form-select:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
    transform: scale(1.02);
}

/* Status badges */
.badge {
    transition: all 0.3s ease;
    cursor: pointer;
}

.badge:hover {
    transform: scale(1.1);
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
}

/* Gallery image hover */
.gallery-item {
    overflow: hidden;
    border-radius: 8px;
    transition: all 0.3s ease;
}

.gallery-item:hover {
    transform: scale(1.05);
    box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
}

.gallery-item img {
    transition: transform 0.5s ease;
}

.gallery-item:hover img {
    transform: scale(1.1);
}

/* Skeleton loading for images */
.skeleton {
    background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
    background-size: 200% 100%;
    animation: shimmer 1.5s infinite;
    border-radius: 4px;
}

/* Stagger animation for lists */
.stagger-item {
    opacity: 0;
    animation: fadeIn 0.5s ease-out forwards;
}

.stagger-item:nth-child(1) { animation-delay: 0.1s; }
.stagger-item:nth-child(2) { animation-delay: 0.2s; }
.stagger-item:nth-child(3) { animation-delay: 0.3s; }
.stagger-item:nth-child(4) { animation-delay: 0.4s; }
.stagger-item:nth-child(5) { animation-delay: 0.5s; }

/* Loading spinner */
.spinner {
    width: 40px;
    height: 40px;
    border: 4px solid #f3f3f3;
    border-top: 4px solid #667eea;
    border-radius: 50%;
    animation: rotate 1s linear infinite;
    margin: 20px auto;
}

/* Smooth scroll */
html {
    scroll-behavior: smooth;
}

/* Custom scrollbar */
::-webkit-scrollbar {
    width: 10px;
    height: 10px;
}

::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 5px;
}

::-webkit-scrollbar-thumb {
    background: #667eea;
    border-radius: 5px;
}

::-webkit-scrollbar-thumb:hover {
    background: #764ba2;
}
</style>
