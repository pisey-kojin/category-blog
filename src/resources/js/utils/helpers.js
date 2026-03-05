export function showFlashMessage(message, type = 'success') {
    const flashDiv = document.createElement('div');
    flashDiv.className = `fixed top-4 right-4 px-4 py-2 rounded shadow-lg ${
        type === 'success' ? 'bg-green-500' : 'bg-red-500'
    } text-white z-50 transition-opacity duration-500`;
    flashDiv.textContent = message;

    document.body.appendChild(flashDiv);

    setTimeout(() => {
        flashDiv.style.opacity = '0';
        setTimeout(() => flashDiv.remove(), 500);
    }, 3000);
}
